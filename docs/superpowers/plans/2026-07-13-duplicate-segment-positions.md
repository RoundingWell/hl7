# Duplicate Segment Positions Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Let `AbstractGroup` retain the same HL7 segment name at multiple structural positions (ARV/ROL before PV1 *and* after PV2) instead of silently dropping the later occurrence.

**Architecture:** Decouple the structure *key* (position identity, e.g. `ROL2`) from the segment *match-name* (wire id, e.g. `ROL`). The match-name is derived from each leaf segment's own `getName()` inside `firstNamesOf()`; storage stays keyed, giving positional retention. Duplicated positions get distinct keys (`ARV2`/`ROL2`) registered in the four ADT messages. A fail-loud guard rejects duplicate keys.

**Tech Stack:** PHP 8.4+, PHPUnit, Composer scripts (`composer lint`/`format`/`analyze`/`phpunit`/`coverage-check`), mago linter.

## Global Constraints

- TDD: write the failing test first, watch it fail, then implement.
- Conventional commits.
- Surgical changes only (Rule 3): touch only what each task needs; do not reformat adjacent code.
- After each task: `composer lint`, `composer analyze`, and the task's tests must pass.
- Final validation: `composer test -- --no-progress` green and `composer coverage-check` at 100%.
- Preserve existing conventions: `#[Override]`, `#[CoversClass]`, one-sentence "why" comment above each test, `@mago-expect` annotations where present.
- Source of truth for structure ordering: HAPI v281.
- Second ARV/ROL position sits **immediately after `PV2`** in every ADT message (A01/A03/A08 = positions 12-13; A06 = positions 13-14 due to an extra earlier `MRG`).

---

### Task 1: Fail-loud duplicate-key guard in `AbstractGroup::add()`

**Files:**
- Modify: `src/AbstractGroup.php` (`add()`, around line 24-27)
- Test: `tests/AbstractMessageTest.php` (already `#[CoversClass(AbstractGroup::class)]`)

**Interfaces:**
- Consumes: `AbstractGroup::add(string $name, StructureDefinition $definition): void`, `Fixtures\FakeGroupMessage` (registers keys `MSH`, `NK1`, `PV2`, `PROCEDURE`, `ZFA`).
- Produces: `add()` now throws `InvalidArgumentException` when `$name` is already registered. No signature change.

- [ ] **Step 1: Add imports to the test file**

In `tests/AbstractMessageTest.php`, add these `use` statements alongside the existing imports:

```php
use InvalidArgumentException;
use RoundingWell\HL7\Segment\MSH;
use RoundingWell\HL7\StructureDefinition;
```

- [ ] **Step 2: Write the failing test**

Add this method to `tests/AbstractMessageTest.php`:

```php
public function testAddRejectsADuplicateStructureKey(): void
{
    // Positional retention relies on every structure key being unique; re-registering a key
    // would silently overwrite the first definition and merge two positions into one slot,
    // so a duplicate key must fail loudly rather than corrupt the structure.
    $message = new FakeGroupMessage();

    $this->expectException(InvalidArgumentException::class);

    $message->add('MSH', new StructureDefinition(MSH::class));
}
```

- [ ] **Step 3: Run the test to verify it fails**

Run: `composer phpunit -- --no-progress --filter testAddRejectsADuplicateStructureKey`
Expected: FAIL — no exception thrown (the second `add` currently overwrites silently).

- [ ] **Step 4: Implement the guard**

In `src/AbstractGroup.php`, replace the body of `add()`:

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

`InvalidArgumentException` is already imported in `AbstractGroup.php` (line 7).

- [ ] **Step 5: Run the test to verify it passes**

Run: `composer phpunit -- --no-progress --filter testAddRejectsADuplicateStructureKey`
Expected: PASS.

- [ ] **Step 6: Verify nothing else regressed + lint + analyze**

Run: `composer phpunit -- --no-progress` then `composer lint` then `composer analyze`
Expected: all green (no existing caller registers a key twice; `GenericMessage` guards with an `in_array` check before `add`).

- [ ] **Step 7: Commit**

```bash
git add src/AbstractGroup.php tests/AbstractMessageTest.php
git commit -m "feat: reject duplicate structure keys in AbstractGroup::add"
```

---

### Task 2: Decouple match-name from key; retain second ARV/ROL in A01

**Files:**
- Modify: `src/AbstractGroup.php` (`firstNamesOf()`, leaf branch around line 210-213)
- Modify: `src/Message/ADT/A01.php` (doc-comment positions 12-13; comment block + registrations after `PV2`, around lines 36-37 and 65-70)
- Test: `tests/Message/ADT/A01Test.php` (replace the drop regression test around lines 137-148)

**Interfaces:**
- Consumes: `StructureDefinition::newInstance(): Structure`, `Structure::getName(): string` (typed segment → class short-name; `GenericSegment` → constructed name). `AbstractGroup::getAll(string $name): list<Structure>`.
- Produces: `firstNamesOf()` returns the derived wire name for leaf segments (was the key). A01 registers keys `ARV2` (wire `ARV`) and `ROL2` (wire `ROL`), both optional repeating, immediately after `PV2`.

- [ ] **Step 1: Replace the drop regression test with retention tests**

In `tests/Message/ADT/A01Test.php`, delete `testRolBeforePv1IsCapturedButRolAfterPv1IsDropped` (lines 137-148) and add:

```php
public function testRolIsRetainedAtBothStructuralPositions(): void
{
    // HAPI lists ROL both before PV1 (pos 8) and after PV2 (pos 13). Each occurrence must be
    // retained at its own position — the pre-PV1 ROL under key 'ROL', the post-PV2 ROL under
    // 'ROL2' — so no role is lost and the two are never merged into one slot.
    $message = $this->parse(beforePv1: ['ROL|1'], afterPv1: ['ROL|2']);

    $before = $message->getAll('ROL');
    $after = $message->getAll('ROL2');

    $this->assertCount(1, $before);
    $this->assertCount(1, $after);
    $this->assertNotSame($before[0], $after[0]);
}

public function testArvIsRetainedAtBothStructuralPositions(): void
{
    // ARV mirrors ROL: it appears before PV1 (pos 7) and after PV2 (pos 12). Both occurrences
    // must survive at their respective positions ('ARV' and 'ARV2').
    $message = $this->parse(beforePv1: ['ARV|1'], afterPv1: ['ARV|2']);

    $before = $message->getAll('ARV');
    $after = $message->getAll('ARV2');

    $this->assertCount(1, $before);
    $this->assertCount(1, $after);
    $this->assertNotSame($before[0], $after[0]);
}
```

- [ ] **Step 2: Run the tests to verify they fail**

Run: `composer phpunit -- --no-progress --filter A01Test`
Expected: the two new tests FAIL — `getAll('ROL2')`/`getAll('ARV2')` return `[]` (count 0), because `ROL2`/`ARV2` are not registered and the forward-only parser drops the post-PV1 occurrence.

- [ ] **Step 3: Decouple the match-name in `firstNamesOf()`**

In `src/AbstractGroup.php`, change the leaf branch of `firstNamesOf()`:

```php
private function firstNamesOf(string $name): array
{
    if (!$this->isGroup($name)) {
        return [$this->getDefinition($name)->newInstance()->getName()];
    }

    $probe = $this->getDefinition($name)->newInstance();

    assert($probe instanceof self, "Group {$this->getName()}.{$name} must extend AbstractGroup");

    return $probe->firstNames();
}
```

Only the first `return` changed (was `return [$name];`). For every existing unique key the derived name equals the key, so their matching is unchanged.

- [ ] **Step 4: Register the second ARV/ROL positions in A01**

In `src/Message/ADT/A01.php`, replace the comment block that sits after the `PV2` registration (lines 66-69, beginning `// HAPI lists ARV and ROL a second time...`) with:

```php
        $this->add('ARV2', new StructureDefinition(GenericSegment::class, ['ARV'], isRepeating: true));
        $this->add('ROL2', new StructureDefinition(GenericSegment::class, ['ROL'], isRepeating: true));
```

Then update the class doc-comment so the second occurrences read as distinct keys. Change:

```php
 * 12. ARV (Access Restriction) (optional repeating)
 * 13. ROL (Role) (optional repeating)
```

to:

```php
 * 12. ARV2 (Access Restriction, 2nd position) (optional repeating)
 * 13. ROL2 (Role, 2nd position) (optional repeating)
```

- [ ] **Step 5: Run the A01 tests to verify they pass**

Run: `composer phpunit -- --no-progress --filter A01Test`
Expected: PASS (both retention tests and all pre-existing A01 tests).

- [ ] **Step 6: Run the full suite + lint + analyze**

Run: `composer phpunit -- --no-progress` then `composer lint` then `composer analyze`
Expected: all green. The `firstNamesOf` change is exercised by every parse; confirm no other message regressed.

- [ ] **Step 7: Commit**

```bash
git add src/AbstractGroup.php src/Message/ADT/A01.php tests/Message/ADT/A01Test.php
git commit -m "feat: retain duplicate ARV/ROL positions via decoupled match-name"
```

---

### Task 3: Retain second ARV/ROL in A03, A06, A08

**Files:**
- Modify: `src/Message/ADT/A03.php` (comment block + doc-comment, after `PV2`)
- Modify: `src/Message/ADT/A06.php` (comment block + doc-comment, after `PV2`)
- Modify: `src/Message/ADT/A08.php` (comment block + doc-comment, after `PV2`)
- Test: `tests/Message/ADT/A03Test.php`, `tests/Message/ADT/A06Test.php`, `tests/Message/ADT/A08Test.php`

**Interfaces:**
- Consumes: the decoupled `firstNamesOf()` from Task 2 and `add()` guard from Task 1. Each message test already has a `parse(array $beforePv1 = [], array $afterPv1 = []): <Message>` helper and uses `Encoding("\r")`.
- Produces: A03/A06/A08 each register `ARV2` (wire `ARV`) and `ROL2` (wire `ROL`) immediately after `PV2`.

- [ ] **Step 1: Write the failing retention test in each of the three test classes**

Add this method to `tests/Message/ADT/A03Test.php`, `tests/Message/ADT/A06Test.php`, and `tests/Message/ADT/A08Test.php` (identical body — each class has its own `parse` helper):

```php
public function testArvAndRolAreRetainedAtBothStructuralPositions(): void
{
    // HAPI lists ARV and ROL both before PV1 and again after PV2. Each occurrence must be
    // retained at its own position ('ARV'/'ROL' before, 'ARV2'/'ROL2' after) so no access
    // restriction or role is lost and positions are never merged.
    $message = $this->parse(beforePv1: ['ARV|1', 'ROL|1'], afterPv1: ['ARV|2', 'ROL|2']);

    $this->assertCount(1, $message->getAll('ARV'));
    $this->assertCount(1, $message->getAll('ROL'));
    $this->assertCount(1, $message->getAll('ARV2'));
    $this->assertCount(1, $message->getAll('ROL2'));
}
```

- [ ] **Step 2: Run the three tests to verify they fail**

Run: `composer phpunit -- --no-progress --filter testArvAndRolAreRetainedAtBothStructuralPositions`
Expected: FAIL for A03/A06/A08 — `getAll('ARV2')`/`getAll('ROL2')` return `[]` because the second positions are not yet registered.

- [ ] **Step 3: Register the second positions in A03**

In `src/Message/ADT/A03.php`, replace the comment block after the `PV2` registration (lines 64-67, beginning `// HAPI lists ARV and ROL a second time...`) with:

```php
        $this->add('ARV2', new StructureDefinition(GenericSegment::class, ['ARV'], isRepeating: true));
        $this->add('ROL2', new StructureDefinition(GenericSegment::class, ['ROL'], isRepeating: true));
```

Update the class doc-comment, changing:

```php
 * 12. ARV (Access Restriction) (optional repeating)
 * 13. ROL (Role) (optional repeating)
```

to:

```php
 * 12. ARV2 (Access Restriction, 2nd position) (optional repeating)
 * 13. ROL2 (Role, 2nd position) (optional repeating)
```

- [ ] **Step 4: Register the second positions in A06**

In `src/Message/ADT/A06.php`, replace the comment block after the `PV2` registration (lines 67-70, beginning `// HAPI lists ARV and ROL a second time...`) with:

```php
        $this->add('ARV2', new StructureDefinition(GenericSegment::class, ['ARV'], isRepeating: true));
        $this->add('ROL2', new StructureDefinition(GenericSegment::class, ['ROL'], isRepeating: true));
```

Update the class doc-comment. A06's second positions are 13-14 (an extra `MRG` shifts them). Change:

```php
 * 13. ARV (Access Restriction) (optional repeating)
 * 14. ROL (Role) (optional repeating)
```

to:

```php
 * 13. ARV2 (Access Restriction, 2nd position) (optional repeating)
 * 14. ROL2 (Role, 2nd position) (optional repeating)
```

- [ ] **Step 5: Register the second positions in A08**

In `src/Message/ADT/A08.php`, replace the comment block after the `PV2` registration (lines 68-71, beginning `// HAPI lists ARV and ROL a second time...`) with:

```php
        $this->add('ARV2', new StructureDefinition(GenericSegment::class, ['ARV'], isRepeating: true));
        $this->add('ROL2', new StructureDefinition(GenericSegment::class, ['ROL'], isRepeating: true));
```

Update the class doc-comment, changing:

```php
 * 12. ARV (Access Restriction) (optional repeating)
 * 13. ROL (Role) (optional repeating)
```

to:

```php
 * 12. ARV2 (Access Restriction, 2nd position) (optional repeating)
 * 13. ROL2 (Role, 2nd position) (optional repeating)
```

- [ ] **Step 6: Run the three tests to verify they pass**

Run: `composer phpunit -- --no-progress --filter testArvAndRolAreRetainedAtBothStructuralPositions`
Expected: PASS for A03, A06, and A08.

- [ ] **Step 7: Full validation + lint + analyze**

Run: `composer lint` then `composer analyze` then `composer test -- --no-progress` then `composer coverage-check`
Expected: all green; coverage at 100%.

- [ ] **Step 8: Commit**

```bash
git add src/Message/ADT/A03.php src/Message/ADT/A06.php src/Message/ADT/A08.php \
        tests/Message/ADT/A03Test.php tests/Message/ADT/A06Test.php tests/Message/ADT/A08Test.php
git commit -m "feat: retain duplicate ARV/ROL positions in A03, A06, A08"
```

---

## Notes for the implementer

- Do **not** add getters for ARV/ROL — none exist today and none are required (retention is verified via `getAll`).
- Do **not** change `StructureDefinition`, `add()`'s signature, or the `Group` interface.
- Do **not** touch `GenericMessage` (it parses by bucket-by-name and never calls `parseStructures`), `GenericGroup` (inherits the fix), or the `*Procedure`/`*Insurance` sub-groups (single `ROL` each, no collision).
- If `composer lint` reports style violations, run `composer format`, then re-run `composer lint`.
- The three ADT message files already have `GenericSegment` and `StructureDefinition` imported (used by existing registrations) — no new imports needed there.

## Self-Review

- **Spec coverage:** guard (Task 1) ✓; `firstNamesOf` decoupling + A01 (Task 2) ✓; A03/A06/A08 + doc-comments (Task 3) ✓; regression test replaced (Task 2, Step 1) ✓; no new getters / no interface change (Notes) ✓; 100% coverage + lint + analyze (Task 3, Step 7) ✓.
- **Placeholder scan:** none — every code and command step is concrete.
- **Type consistency:** keys `ARV2`/`ROL2` and wire names `ARV`/`ROL` used consistently across all tasks; `getAll(string): list<Structure>` and `add(string, StructureDefinition): void` match the source.
