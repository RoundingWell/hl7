# Duplicate segment positions via positionally-unique structure keys

**Date:** 2026-07-13
**Branch:** feat/generic-classes
**Status:** Approved

## Problem

`AbstractGroup` keys everything by segment name, conflating three roles:

1. **Definition key** — `$definitions[$name]`.
2. **Storage key** — `$structures[$name]`; `getNames()` drives the ordered parse walk.
3. **Match token** — `firstNamesOf()` returns `[$name]`, compared by `matchStructure()`
   against the wire segment name. This works ONLY because key == wire name.

The parser (`parseStructures`) advances a forward-only pointer over `getNames()`. HAPI's
v281 ADT structure lists ARV and ROL **twice** — once before PV1 (positions 7-8) and once
after PV2 (positions 12-13; A06 is 13-14 because of an extra MRG segment). Because a name maps
to a single definition/storage slot and the pointer never scans backward, a second ARV/ROL
appearing after PV1 can never be re-matched. It is silently **dropped** — not merged.

This limitation is currently documented (not fixed) by comment blocks in
`src/Message/ADT/{A01,A03,A06,A08}.php` and locked in by a regression test,
`A01Test::testRolBeforePv1IsCapturedButRolAfterPv1IsDropped`.

A per-name list (`array<string, list<StructureDefinition>>`) is not viable: the parser needs a
single global ordering across all positions (ARV, ROL, …, PV1, …, ARV, ROL). Bucketing by name
loses that interleaving. The model must remain a single ordered slot list.

## Approach (A): positionally-unique keys + explicit match-name

Keep string keys, but give duplicated positions distinct keys (`ROL`, `ROL2`, HAPI's own
style) and **decouple the structure key (position identity) from the segment match-name
(wire id)** so `ROL2` still matches wire `ROL`. Unique-named segments (EVN, NK1, PV1, …) are
unaffected — for them the derived match-name already equals the key.

### Key insight

The conflation lives in exactly one place: the leaf branch of `firstNamesOf()`, which returns
the *key* as the match token. Every match path — `matchStructure`, `firstNames`,
`followNamesFor` — flows through `firstNamesOf`, so fixing that one branch fixes matching
everywhere. Storage (`getRepetition`/`getAll`) keeps using the key, which is what yields
positional retention.

The match-name is **derived**, not stored: every `Structure` implements `getName()`, and for a
leaf segment it returns exactly the wire name — a typed segment via its class short-name
(`AbstractSegment::getName`), a `GenericSegment` via its constructed name (`args[0]`). So the
generic-vs-typed asymmetry resolves itself with no new field or signature.

## Changes

### 1. `src/AbstractGroup.php`

**Decouple match-name from key** — `firstNamesOf()` leaf branch:

```php
private function firstNamesOf(string $name): array
{
    if (!$this->isGroup($name)) {
        return [$this->getDefinition($name)->newInstance()->getName()]; // was: [$name]
    }
    // group probe — unchanged
}
```

For every existing unique segment the derived name equals the key, so behavior is identical.
Only `ARV2`/`ROL2` (key ≠ wire) change: `firstNamesOf('ROL2')` returns `['ROL']`, so wire `ROL`
matches the second slot while storage stays keyed by `ROL2`.

**Fail-loud duplicate-key guard** — `add()`:

```php
public function add(string $name, StructureDefinition $definition): void
{
    if (isset($this->definitions[$name])) {
        throw new InvalidArgumentException(
            "Cannot add {$this->getName()}.{$name}, a structure with that key already exists",
        );
    }
    $this->definitions[$name] = $definition;
}
```

Converts the "accidentally registered the same key twice" footgun (which silently overwrote a
definition and merged two positions into one storage slot) into a loud error. `InvalidArgumentException`
is already imported.

### 2. `src/Message/ADT/{A01,A03,A06,A08}.php`

Replace the 4-line "accepted limitation" comment block that sits immediately after the `PV2`
registration with the real second-position registrations:

```php
$this->add('ARV2', new StructureDefinition(GenericSegment::class, ['ARV'], isRepeating: true));
$this->add('ROL2', new StructureDefinition(GenericSegment::class, ['ROL'], isRepeating: true));
```

Update each class doc-comment's numbered position list so the second ARV/ROL entries read as
`ARV2`/`ROL2` (A01/A03/A08 positions 12-13; A06 positions 13-14). No new getters are added.

### 3. Tests

- **Replace** `A01Test::testRolBeforePv1IsCapturedButRolAfterPv1IsDropped` with a test proving
  both positions are retained: parse a `ROL` before PV1 and a `ROL` after PV1, then assert
  `getAll('ROL')` has count 1 **and** `getAll('ROL2')` has count 1, and that the two instances
  are distinct objects (so the test cannot pass by merging). Add an ARV analogue.
- **Add** an `AbstractGroup`/`GenericGroup` test asserting `add()` throws
  `InvalidArgumentException` when the same key is registered twice — required for 100% coverage
  of the new guard.
- All existing tests remain unchanged and green.

## Scope and non-goals

- **No** signature changes to `StructureDefinition`, `add()`, or the `Group` interface.
- `getAll(key)` stays position-specific (`getAll('ROL')` / `getAll('ROL2')`). No
  aggregate-by-wire-name accessor is added (YAGNI — no getter exposes ARV/ROL, and
  correct-position retention is the success criterion).
- `GenericGroup` inherits the fix (its only test checks `getName`).
- `GenericMessage` parses via bucket-by-name (`groupByName`) and never calls `parseStructures`
  — unaffected.
- The `*Procedure` / `*Insurance` sub-groups register `ROL` only once each — no intra-group
  collision, untouched.

## Success criteria

- A message with ARV/ROL both before **and** after PV1 retains every occurrence at its correct
  position, reachable via `getAll('ARV')`/`getAll('ARV2')` and `getAll('ROL')`/`getAll('ROL2')`.
- All existing tests pass (the one regression test is replaced with justification).
- The documented "drop" limitation (comments + regression test) is gone, replaced by tests
  asserting correct retention.
- `composer lint`, `composer analyze` clean; `composer coverage-check` at 100%; full
  `composer test` green.
- Source of truth for structure ordering: HAPI v281.
