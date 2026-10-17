# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.11.0] - 2026-10-17

### Changed

- The package now requires `php-standard-library/type` (^6.2) at runtime.
- The Composer dist archive no longer ships tests or development configuration.
- Parsing no longer builds throwaway structures in the parse walk: a definition's leading segment
  names are resolved once per group instead of once per candidate probe. An 111-segment `ADT_A01`
  with 100 `NK1` parses in 5.0 ms instead of 6.8 ms under PHP 8.5 with opcache on the CLI. Parsed
  structures, ordering, and exception messages are unchanged.
- Segment fields are parsed the first time they are read instead of during `parse()`. On the
  same `ADT_A01`, parsing takes 0.7 ms instead of 5.0 ms, and parse plus serialize without reading
  drops from 28.1 ms to 0.7 ms. Values, mutability, and exception types and messages for a field
  you read are unchanged.
- `parse()` no longer throws for a malformed field value (`InvalidValue`, `InvalidDateTime`, or an
  `OutOfBoundsException` for too many repetitions). The exception is thrown when that field is
  read, every time it is read, and reading other fields of the same segment still works.
  Structural errors (`InvalidMessage`, and `InvalidSegment` for an `MSH` line without a field
  separator) still throw from `parse()`.
- `serialize()` with the delimiters a message was parsed with is now byte-for-byte: segments keep
  their trailing empty fields, components, and subcomponents, and each field keeps its received
  bytes unless its content changed. Writing a field re-encodes only that field. Serializing with
  different delimiters re-encodes and trims every field, as before.
- Calling `parse()` on a segment that already holds data replaces it instead of merging into it.

### Fixed

- A field declared with `maxReps: N` now accepts exactly N repetitions; the (N+1)th throws
  `OutOfBoundsException`. Previously a 0-based repetition index was compared to the maximum with
  `>`, so every capped field accepted one repetition too many — each `maxReps: 1` field accepted
  two. The exception message now reports the 1-based repetition number so it agrees with the
  allowed count.

## [0.10.0] - 2026-08-10

### Changed

- `Message` now declares `getSegment()` and `generateACK()`. Both were already implemented by
  `AbstractMessage` and documented as public API, but were invisible to code holding the
  interface — such as the `Message` returned by `MessageFactory::parse()`. Implementations that
  do not extend `AbstractMessage` must now provide both methods.

## [0.9.0] - 2026-07-23

### Added

- All segment accessors `get*()` now have docblocks that contain the segment name and HL7 path

### Changed

- `DTM::getDateTime()` accepts an optional `DateTimeZone` that is applied when parsing a
  timestamp without an explicit UTC offset, making the result deterministic across hosts instead
  of adopting the environment default. The parsed instant is no longer memoized so a different
  timezone is honored on each call. `DT` is unchanged: a date has no time-of-day for a timezone
  to apply to.

## [0.8.0] - 2026-07-22

### Changed

- `Message::debug()` now renders untyped content that lives in extra components, so undeclared
  (Z) segments and any values a sender placed beyond a field's declared schema appear in the dump
  instead of being silently dropped. Such elements render without a schema name; a lone untyped
  value collapses onto its field line while multiple parts expand into numbered children.

## [0.7.0] - 2026-07-22

### Added

- `Message::debug()` returns an indented dump of a message's populated structure for debugging,
  labelling each element with its access path and schema name (e.g. `PID.5.1 (Family Name)`) and
  omitting empty elements.
- `Group::getStructures()` returns a group's materialized child structures in appearance order.
- `Field` interface (`getField()`/`setField()`), extended by `Type`, so a materialized value
  carries the schema field name it fills.

### Changed

- **BREAKING:** The `Message`, `Group`, and `Type` interfaces gained methods (`Message::debug()`,
  `Group::getStructures()`, and `Type`'s `getField()`/`setField()` via `Field`). Classes that
  implement these interfaces directly rather than extending the provided abstract bases
  (`AbstractMessage`, `AbstractGroup`, `AbstractPrimitive`, `AbstractComposite`) must add the new
  methods. Permitted under 0.x versioning.

## [0.6.0] - 2026-07-21

### Changed

- Segments that appear out of their schema position now parse into their declared, typed slot
  and are readable via `getAll()`/typed accessors, instead of being retained for serialization
  only. All occurrences of a declared segment are preserved, so `getAll()` may return more than
  one even for a non-repeating definition; the non-repeating cap now applies only to hand-built
  messages.
- Hand-built (never-parsed) messages now serialize their segments in creation order rather than
  schema order.
- `AbstractGroup::setRepetition()` (protected) is replaced by `AbstractGroup::append()`; a group's
  children are now stored as one ordered list.
- `AbstractGroup::getRepetition()` now throws `OutOfBoundsException` when asked for a repetition
  more than one past the existing count (e.g. repetition `#3` with zero occurrences so far),
  instead of silently creating a phantom repetition at that index.

## [0.5.0] - 2026-07-21

### Changed

- Typed message parsing now retains every unmatched segment in place instead of dropping unexpected or out-of-order segments, so parse → serialize preserves the received segment order (generalizes the previous Z-segment-only retention)
- `DT` and `DTM` now store the raw value without validating it during parsing; the character match runs lazily on the first `getFormat()`/`getDateTime()` call. `getFormat()` throws only when the value cannot match the character pattern, and `getDateTime()` throws when it cannot build a valid instant. This keeps message parsing from aborting on a malformed date field and defers validation to consumers.

## [0.4.0] - 2026-07-20

### Changed

- `GenericMessage` now parses typed segments via `SegmentFactory` instead of always producing `GenericSegment` instances

## [0.3.0] - 2026-07-20

### Added

- ADT `A04`, `A07`, and `A13` message types, reusing the HAPI structures they map to (`A04`/`A13` reuse `A01`, `A07` reuses `A06`)

### Changed

- `A08` now extends `A01` to share its message structure instead of redefining it

## [0.2.0] - 2026-07-20

### Added

- Retain undeclared Z-segments in typed message parsing

## [0.1.0] - 2026-07-19

### Added

- Initial release of the tool

[Unreleased]: https://github.com/RoundingWell/hl7/compare/0.11.0...main
[0.11.0]: https://github.com/RoundingWell/hl7/releases/tag/0.11.0
[0.10.0]: https://github.com/RoundingWell/hl7/releases/tag/0.10.0
[0.9.0]: https://github.com/RoundingWell/hl7/releases/tag/0.9.0
[0.8.0]: https://github.com/RoundingWell/hl7/releases/tag/0.8.0
[0.7.0]: https://github.com/RoundingWell/hl7/releases/tag/0.7.0
[0.6.0]: https://github.com/RoundingWell/hl7/releases/tag/0.6.0
[0.5.0]: https://github.com/RoundingWell/hl7/releases/tag/0.5.0
[0.4.0]: https://github.com/RoundingWell/hl7/releases/tag/0.4.0
[0.3.0]: https://github.com/RoundingWell/hl7/releases/tag/0.3.0
[0.2.0]: https://github.com/RoundingWell/hl7/releases/tag/0.2.0
[0.1.0]: https://github.com/RoundingWell/hl7/releases/tag/0.1.0
