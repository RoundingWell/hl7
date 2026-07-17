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

## Concepts

| Type            | Responsibility                                                                                                          |
| --------------- | ----------------------------------------------------------------------------------------------------------------------- |
| `MessageFactory`  | Parses raw HL7 into a `Message`, resolving encoding and message type.                                                   |
| `Message`         | Interface for a whole message: a `Group` plus `getMSH()`, `getVersion()`, and `parse()`.                                |
| `Group`           | Interface for a named collection of `Structure`s (segments and nested groups) with lookup helpers (`get`, `getAll`, `getRepetition`, `getNames`, `isRequired`, `isRepeating`, `isGroup`). |
| `Message\ADT\Axx` | Specific ADT message subclasses (e.g. `A01`) add named accessors for message-specific segments.                         |
| `Segment`         | Interface for a collection of numbered fields (each a `Type`), read with `getField()` / `getFieldRepetition()`. Typed subclasses (e.g. `PID`, `MSH`) add named accessors. |
| `Type`            | An HL7 data type — a `Primitive` scalar (`ST`, `NM`, `DTM`, …), a `Composite` of other types, or a `Varies` placeholder for undefined fields. |
| `Encoding`        | The field, component, repetition, and sub-component separators, plus the escape and truncation characters and line ending. |

### Untyped fields

Fields that are not defined for a segment are still parsed so no data is lost. Whole segments
that have no typed subclass (for example `GT1` in an ADT message) are exposed as
`GenericSegment`s, and their fields are `Varies` instances — a wrapper that defers to a
`GenericPrimitive` until a concrete type is assigned.

Read an undefined field the same way as any other, then unwrap it with `getData()`:

```php
$gt1 = $message->get('GT1');

$field = $gt1->getFieldRepetition(2, 0); // a Varies instance
echo $field->getData()->getValue();      // "8291" — the field value as a string
```

Any components beyond the first are preserved on the primitive's extra components
(`getExtraComponents()`) rather than being discarded.

## Supported types

**Messages:** `A01`, `A03`, `A06`, `A08`

**Segments:** `DG1`, `DRG`, `EVN`, `MSH`, `NK1`, `OBX`, `PID`, `PV1`, `PV2`

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
