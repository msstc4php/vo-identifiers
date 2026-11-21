# Changelog

## [Unreleased]

### Added

- Package scaffold, CI and tool configuration from `vo-finance`; source notes in `docs/sources/IDENTIFIERS.md`, the
  authority for every rule (enforced, format only, or not built, each with its reason), and the terms of use of every table
  the plan wanted to embed.
- Check-digit helpers in `Internal\` with the examples of their sources as tests: `Luhn`, `Ean13CheckDigit`,
  `Mod11CheckDigit` (ISSN and ISBN-10), `Iso7064Mod11_2` (ISNI, ORCID), `Iso7064Mod37_36` (ISAN), `Iso7064Mod97` (EID), `RorCheckDigits`
  (ROR: Crockford base32 decoded to an integer, then MOD 97-10), `VinCheckDigit`, `S10CheckDigit`, `AirWaybillCheckDigit`, `CasCheckDigit`, `EcNumberCheckDigit` and
  `EicCheckDigit` (validation only: ENTSO-E allows no other use of its algorithm).
- Publication and media identifiers in `Publication\`: `Isbn` (ISBN-10 and ISBN-13, GTIN), `Issn` (977 GTIN), `Ismn`, `Isrc`, `Iswc`
  (format only), `Isan`, `Isni`, `Orcid`, `Doi` (ASCII case-insensitive equality), `Ror`, `Ark` (format only) and
  `PublicationIdentifierFactory`; `Exception\InvalidPublicationIdentifierException`.
- Publication review fixes: `Isbn` refuses 979-0 (ISMN), `Doi` takes any numeric directory indicator and decodes resolver URLs, `Ark::equals()` ignores hyphens, `Isan` accepts forms without check characters, `ISBN-13:`/`ISBN-10` labels are dropped, `Orcid` accepts `orcid.org/...`, `Issn::toGtin()` throws `InvalidArgumentException` for a bad variant.
