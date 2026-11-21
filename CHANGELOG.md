# Changelog

## [Unreleased]

### Added

- Chemistry, trade and energy identifiers: `Substance\CasRegistryNumber`, `EcNumber` (check digits enforced), `UnNumber`;
  `Trade\HsCode`, `TariffCode`; `Energy\EnergyIdentificationCode` with `EicObjectType` (validation only), `Mpan` (format
  only); `Exception\InvalidSubstanceIdentifierException`, `InvalidTradeIdentifierException`,
  `InvalidEnergyIdentifierException`.
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
- Transport and logistics identifiers in `Transport\`: `Vin` (position 9 reported by `hasValidCheckDigit()`, never refused; `modelYear()` candidates), `Wmi`, `ContainerNumber` and `ImoNumber` (format only), `Mmsi` with `MmsiType`, `S10ItemIdentifier`, `AirWaybillNumber`, `UicVehicleNumber`, `UnLocode`, `IataAirportCode`, `IcaoAirportCode`, `Iso15459Identifier`, `DataIdentifierString` and `DataIdentifierField` (ISO/IEC 15434 format 06, lossless) and `TransportIdentifierFactory`; `Exception\InvalidTransportIdentifierException`.
- Transport review fixes: `Iso15459Identifier::issuingAgencyCode()` and `issuingAgencyName()` (AIM register 2026-06-16, longest match), the 35-character cap applies to `J` fields only, `Mmsi` and `AirWaybillNumber` no longer strip spaces, `Mmsi::type()` requires a MID starting 2 to 7, source quotes for S10 and UIC.
- Publication review fixes: `Isbn` refuses 979-0 (ISMN), `Doi` takes any numeric directory indicator and decodes resolver URLs, `Ark::equals()` ignores hyphens, `Isan` accepts forms without check characters, `ISBN-13:`/`ISBN-10` labels are dropped, `Orcid` accepts `orcid.org/...`, `Issn::toGtin()` throws `InvalidArgumentException` for a bad variant.
- Telecom identities in `Telecom\`: `Imei` (14 digits or 15 with Luhn), `Imeisv`, `Iccid` (`89`, Luhn, 6 to 20 digits), `Imsi` (6 to 15 digits, MNC length from the caller), `Eid` (32 digits, MOD 97-10, no prefix rule), `masked()` on the five (`Imeisv` too), and `TelecomIdentifierFactory`; `Exception\InvalidTelecomIdentifierException`.
- `Imei::equals()` and `Isbn::equals()` compare across forms (14 and 15 digit IMEI, ISBN-10 and ISBN-13) while the stored string keeps the form given.
- Every value object serializes as its value alone and validates it again on `unserialize()` (`Internal\ReparsesOnUnserialize`;
  `DataIdentifierField` has its own pair): a forged or stale payload throws, other keys are ignored, a payload without a string
  value throws vo-base `InvalidValueObjectException`.
- `MAX_LENGTH` on every class with a bounded length (characters of the longest stored form).
- The three factories return a native union of their candidates (`PublicationIdentifierFactory` and
  `TransportIdentifierFactory` no longer return `ValueObject`).
- One wording per failure: `has an invalid format`, `has a wrong check digit` (also for IMEI, ICCID, EID, CAS and EC, which
  said "invalid check digit"), `has a wrong check character` (also for EIC).
- Embedded table: the AIM register of ISO/IEC 15459 issuing agency codes, edition 2026-06-16, decode-only; a new edition ships as a minor release.
- Not built, each with its reason in `docs/sources/IDENTIFIERS.md`: `Wmi::region()` and `country()`, `Mmsi::country()`, `Isbn::formatted()`, `IcaoAirportCode::region()`, `ImoCompanyNumber`, any check digit for ISWC, container number, IMO number and MPAN.
- Format only, each with its reason in the README: `ImoNumber`, `ContainerNumber`, `Iswc`, `Mpan`, `Ark`, `UnLocode`, `IataAirportCode`, `IcaoAirportCode`, `UnNumber`, `HsCode`, `TariffCode`.
