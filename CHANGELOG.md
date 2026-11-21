# Changelog

## [Unreleased]

### Added

- Package scaffold, CI and tool configuration from `vo-finance`; source notes in `docs/sources/IDENTIFIERS.md`, the
  authority for every rule (enforced, format only, or not built, each with its reason), and the terms of use of every table
  the plan wanted to embed.
- Check-digit helpers in `Internal\` with the examples of their sources as tests: `Luhn`, `Ean13CheckDigit`,
  `Mod11CheckDigit` (ISSN and ISBN-10), `Iso7064Mod11_2` (ISNI, ORCID), `Iso7064Mod37_36` (ISAN), `Iso7064Mod97` (EID, ROR),
  `Iso6346CheckDigit`, `VinCheckDigit`, `S10CheckDigit`, `AirWaybillCheckDigit`, `CasCheckDigit`, `EcNumberCheckDigit` and
  `EicCheckDigit`.
