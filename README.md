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
echo $msh->getMessageType()->triggerEvent->getValue(); // "A01"

// Assuming the message is an A01
$pid = $message->getPID();

echo $pid->getDateOfBirth()->getValue();

// Repeating fields return a list of data-type instances.
foreach ($pid->getPatientName() as $name) {
    echo $name->givenName->getValue();          // "DONALD"
    echo $name->familyName->surname->getValue(); // "DUCK"
}

// Multiple occurrences of the same segment (e.g. DG1) are available too.
$diagnoses = $message->getAllSegments('DG1');
```

Composite data types expose their components as public, readonly properties, and those
components may themselves be composites (sub-components), so you can drill down as far as the
data type defines:

```php
foreach ($pid->getIdentifierList() as $cx) {
    echo $cx->id->getValue();                          // "10006579"
    echo $cx->assigningAuthority->namespaceId->getValue();
    echo $cx->identifierTypeCode->getValue();          // "MRN"
}
```

## Concepts

| Type            | Responsibility                                                                                                          |
| --------------- | ----------------------------------------------------------------------------------------------------------------------- |
| `MessageFactory`| Parses raw HL7 into a `Message`, resolving encoding and message type.                                                   |
| `Message`       | A collection of `Segment` objects with lookup helpers (`getSegment`, `getAllSegments`, `getRequiredSegment`, `getMSH`). |
| `Message\Axx`   | Message type-specific subclasses (e.g. `A01`) add named accessors for message-specific segments.                          |
| `Segment`       | A collection of numbered `Field`s. Typed subclasses (e.g. `PID`) add named accessors.                                   |
| `Field`         | Holds one or more data-type instances and knows whether it is required or repeating.                                    |
| `Type`          | A parsed HL7 data type — either a scalar (`ST`, `NM`, `DTM`, …) or a `Composite` of other types.                        |
| `Encoding`      | The delimiter, component/repetition/sub-component separators, escape character, and line ending.                        |

Unknown segments are still parsed: their fields are exposed as `ST` values so no data is lost.

## Supported types

**Messages:** `A01`, `A03`, `A06`, `A08`

**Segments:** `DG1`, `DRG`, `EVN`, `MSH`, `NK1`, `OBX`, `PID`, `PV1`, `PV2`

**Data types:** `CE`, `CNE`, `CP`, `CWE`, `CX`, `DLD`, `DR`, `DT`, `DTM`, `EI`, `FC`, `FNx`,
`HD`, `ID`, `IS`, `JCC`, `MO`, `MSG`, `NM`, `PL`, `PT`, `SAD`, `SI`, `SNM`, `ST`, `TS`, `TX`,
`VID`, `Varies`, `XAD`, `XCN`, `XON`, `XPN`, `XTN`

## Error handling

Parsing failures throw exceptions extending `RoundingWell\HL7\Exception\HL7Exception`:

- `InvalidFile` — the file does not exist or cannot be read.
- `InvalidMessage` — the message is missing its `MSH` segment, delimiter, or encoding characters.
- `InvalidSegment` — a required segment is not present.
- `InvalidField` — a field is not defined for the segment.
- `InvalidValue`, `InvalidComponent`, `InvalidDateTime` — a field value fails validation.

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
