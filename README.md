# HL7

An ADT/HL7 parser for PHP that turns raw HL7 messages into strongly-typed, structured objects.

Instead of hand-splitting pipe-delimited strings, you parse a message once and read its
segments, fields, and data-type components through named accessors.

## Requirements

- PHP 8.4 or newer

## Installation

```bash
composer require roundingwell/hl7
```

## Quick start

```php
use RoundingWell\HL7\MessageFactory;

$factory = new MessageFactory();

// Parse from a string...
$message = $factory->parse($rawHl7);

// ...or straight from a file.
$message = $factory->parseFile('/path/to/message.hl7');
```

The factory reads the delimiter and encoding characters from the `MSH` segment, detects the
line ending (`\r`, `\n`, or `\r\n`), and returns a `Message`. When the message type maps to a
known trigger event (for example `A01`), a message subclass is returned; otherwise a generic
`Message` is used.

### Reading segments and fields

```php
$msh = $message->getMSH();
echo $msh->getMessageControlId()->getValue(); // "599102"
echo $msh->getMessageType()->getTriggerEvent()->getValue(); // "A01"

// Assuming the message is an A01
$pid = $message->getPID();

echo $pid->getDateOfBirth()->getValue();

// Repeating fields return a list of data-type instances.
foreach ($pid->getPatientName() as $name) {
    echo $name->getGivenName()->getValue();               // "DONALD"
    echo $name->getFamilyName()->getSurname()->getValue(); // "DUCK"
}

// Multiple occurrences of the same segment (e.g. DG1) are available too.
$diagnoses = $message->listDG1();
```

Some messages nest repeating groups of segments (e.g. the `PROCEDURE` group in an `A01`, which
bundles a `PR1` with its `ROL` segments). Groups expose the same lookup helpers as a message:

```php
foreach ($message->getAll('PROCEDURE') as $procedure) {
    $pr1 = $procedure->get('PR1');
    $roles = $procedure->getAll('ROL');
}
```

Composite data types expose their components through named accessors, and those components
may themselves be composites (sub-components), so you can drill down as far as the data type
defines:

```php
foreach ($pid->getIdentifierList() as $cx) {
    echo $cx->getId()->getValue();                              // "10006579"
    echo $cx->getAssigningAuthority()->getNamespaceId()->getValue(); // "1"
    echo $cx->getIdentifierTypeCode()->getValue();              // "MRN"
}
```

Every composite also exposes its components positionally via `getComponent(int $index)`
(0-based) and `getComponents()`, which the named accessors are built on.

### Generating acknowledgments

Any parsed message can produce an `ACK` response. Supply an acknowledgment code, a
[PSR-20](https://www.php-fig.org/psr/psr-20/) clock (for `MSH-7`), and an `IdGenerator`
(for the acknowledgment's own `MSH-10`):

```php
use RoundingWell\HL7\AcknowledgmentCode;
use RoundingWell\HL7\SymfonyUidGenerator;
use Symfony\Component\Clock\NativeClock;

$ack = $message->generateACK(
    AcknowledgmentCode::AA,
    new NativeClock(),
    new SymfonyUidGenerator(),
);
```

`generateACK()` swaps the sender/receiver, echoes the request's control ID into `MSA-2`,
and writes the acknowledgment code to `MSA-1`. The returned `ACK` is a `Message` object,
which can be serialized back to HL7 (see below).

> `SymfonyUidGenerator` and `NativeClock` require the optional `symfony/uid` and
> `symfony/clock` packages. Any PSR-20 clock and any `IdGenerator` implementation work.

### Serializing back to HL7

Any `Message` can be turned back into a wire string with `serialize()`:

```php
$wire = $message->serialize($encoding);
```

`parse()` followed by `serialize()` reproduces the original message, with two deviations from a
byte-for-byte round-trip: trailing empty fields, components, and subcomponents are trimmed (HL7
treats trailing delimiters as optional), and segments are joined by the line ending rather than
terminated by it (no trailing line ending is appended).

## Concepts

| Type            | Responsibility                                                                                                          |
| --------------- | ----------------------------------------------------------------------------------------------------------------------- |
| `MessageFactory`  | Parses raw HL7 into a `Message`, resolving encoding and message type.                                                   |
| `Message`         | Interface for a whole message: a `Group` plus `getMSH()`, `getVersion()`, `parse()`, and `serialize()`.                 |
| `Group`           | Interface for a named collection of `Structure`s (segments and nested groups) with lookup helpers (`get`, `getAll`, `getRepetition`, `getNames`, `isRequired`, `isRepeating`). |
| `Message\ADT\Axx` | Specific ADT message subclasses (e.g. `A01`) add named accessors for message-specific segments.                         |
| `Segment`         | Interface for a collection of numbered fields (each a `Type`), read with `getField()` / `getFieldRepetition()`. Typed subclasses (e.g. `PID`, `MSH`) add named accessors. |
| `Type`            | An HL7 data type — a `Primitive` scalar (`ST`, `NM`, `DTM`, …), a `Composite` of other types, or a `Varies` placeholder for undefined fields. |
| `Encoding`        | The field, component, repetition, and sub-component separators, plus the escape and truncation characters and line ending. |
| `AcknowledgmentCode` | Enum of the HL7 table 0008 acknowledgment codes (`AA`, `AE`, `AR`) accepted by `generateACK()`. |
| `IdGenerator`      | Interface for generating unique message control IDs (`MSH-10`), e.g. for a generated `ACK`. |
| `SymfonyUidGenerator` | `IdGenerator` implementation backed by `symfony/uid`, producing time-ordered UUIDv7 identifiers. |

### Untyped fields

Fields that are not defined for a segment are still parsed so no data is lost. Whole segments
that have no typed subclass (for example `GT1` in an ADT message) are exposed as
`GenericSegment`s, and their fields are `GenericComposite` instances — a schema-less composite
that preserves any component (`^`) structure instead of flattening it.

A `GenericComposite` has no defined components, so every parsed component lands in its extra
components (`getExtraComponents()`), each a `Varies` wrapping a `GenericPrimitive`. Read a
scalar field through its single component:

```php
$gt1 = $message->get('GT1');

$field = $gt1->getFieldRepetition(2, 0);                        // a GenericComposite
echo $field->getExtraComponents()->getComponent(0)->getData()->getValue(); // "8291"
```

Component structure is retained: an undefined field `a^b^c` keeps three components (one extra
component per `^`), and a component carrying subcomponents (`a&b`) keeps `b` as a subcomponent
of that component rather than promoting it to its own component.

### Unmatched segments

Typed messages never silently drop a segment. Any segment the schema cannot place — a
site-defined Z-segment, any other name the schema does not declare, or a declared segment that
reappears after its slot in the message has already been consumed — is parsed as a
`GenericSegment` and serialized back in its original position, so parse → serialize round trips
do not lose data:

```php
$zds = $message->get('ZDS'); // a GenericSegment, readable like any untyped segment
```

A retained segment is exposed through `get()` / `getAll()` on the group where it appeared only
when its name is not one the schema declares there. A declared segment retained out of position
is kept for serialization only, so it is not double-counted through `getAll()`.

## Supported types

**Messages:** `A01`, `A03`, `A04`, `A06`, `A07`, `A08`, `A13`, `ACK`

**Segments:** `DG1`, `DRG`, `EVN`, `MSA`, `MSH`, `NK1`, `OBX`, `PID`, `PV1`, `PV2`

**Data types:** `CE`, `CNE`, `CP`, `CWE`, `CX`, `DLD`, `DR`, `DT`, `DTM`, `EI`, `FC`, `FNx`, `Generic`,
`HD`, `ID`, `IS`, `JCC`, `MO`, `MSG`, `NM`, `PL`, `PT`, `SAD`, `SI`, `SNM`, `ST`, `TS`, `TX`,
`VID`, `Varies`, `XAD`, `XCN`, `XON`, `XPN`, `XTN`

## Error handling

Parsing failures throw exceptions extending `RoundingWell\HL7\Exception\HL7Exception`:

- `InvalidFile` — the file does not exist or cannot be read.
- `InvalidMessage` — the message is missing its `MSH` segment, delimiter, or encoding characters.
- `InvalidValue`, `InvalidDateTime` — a field value fails validation.

Looking up structures on a parsed message throws standard SPL exceptions:

- `InvalidArgumentException` — requesting a segment, field, or group structure that was never registered.
- `OutOfBoundsException` — requesting a repetition of a non-repeating structure, or a negative repetition.

## Development

```bash
composer lint      # check code style
composer format    # fix code style
composer analyze   # static analysis
composer test      # run tests and enforce 100% coverage
composer verify    # lint + analyze + test
```

## License

Released under the [MIT License](LICENSE.md).
