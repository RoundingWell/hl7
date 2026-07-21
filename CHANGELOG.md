# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Changed

- Typed message parsing now retains every unmatched segment in place instead of dropping unexpected or out-of-order segments, so parse → serialize preserves the received segment order (generalizes the previous Z-segment-only retention)

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

[Unreleased]: https://github.com/RoundingWell/hl7/compare/0.4.0...HEAD
[0.4.0]: https://github.com/RoundingWell/hl7/compare/0.3.0...0.4.0
[0.3.0]: https://github.com/RoundingWell/hl7/compare/0.2.0...0.3.0
[0.2.0]: https://github.com/RoundingWell/hl7/compare/0.1.0...0.2.0
[0.1.0]: https://github.com/RoundingWell/hl7/releases/tag/0.1.0
