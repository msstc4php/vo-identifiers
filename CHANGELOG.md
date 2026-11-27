# Changelog

## [2.0.0] - unreleased

A major release with two breaking changes; everything else (classes, methods, canonical stored forms, `MAX_LENGTH`,
enum backed values, exception classes) is as in 1.2.0, and nothing that 1.2 accepted is refused. The README "Backward
compatibility" promise now covers 2.x.

### Changed (BC)

- `Publication\Isan::equals()`: a version segment `0000-0000` equals no version ("To avoid confusion, a V-ISAN version
  segment of 0 (i.e. 0000-0000) shall be treated as an ISAN without a version segment.", ISO 15706-2:2023, 4.4). So
  `B159D8FA01240000`, `B159D8FA01240000K`, `B159D8FA0124000000000000` and `B159D8FA01240000K00000000E` are now equal
  (1.x: the two with a version were not equal to the two without). The check characters are still ignored, and a check
  character that is present must still be right. The stored form, `__toString()`, JSON, `version()` (`00000000`),
  `hasVersion()`, `formatted()` and `MAX_LENGTH` (26) keep the form given.

### Removed

- `Transport\MmsiType`, the 0.x name of `Transport\MmsiStationType` (deprecated alias since 1.0.0), with the
  `autoload.files` entry that declared it.

### Upgrading from 1.x

- `Transport\MmsiType` → `Transport\MmsiStationType` in `use` statements, type hints, `instanceof`, Doctrine and ODM
  `enumType: MmsiType::class` mappings and framework attributes (Symfony `#[Assert\Choice(callback: [MmsiType::class,
  'cases'])]`); the cases and backed values are the same, so stored rows need nothing. Any remaining use of the old
  name is now an unknown class (`Error` at runtime, an error in static analysis): search for `MmsiType` outside
  `vo-identifiers-doctrine`, whose `MmsiType` is the Doctrine type of `Mmsi` and is unaffected.
- Enum cases serialized under 0.x by PHP `serialize()` (`E:57:"MSSTC4PHP\ValueObject\Identifiers\Transport\MmsiType:Ship";`
  in caches, Messenger messages, sessions, serialized columns) no longer resolve: `unserialize()` emits a warning and
  returns `false`. 1.x already wrote the new name, so only payloads written by 0.x and never rewritten are affected.
  Before upgrading, read them back under 1.x and serialize them again, or rewrite the class name in the payload: the
  name grows by 7 bytes, so the `E:<length>:` prefix grows by 7 too (`E:57:"…\MmsiType:Ship"` →
  `E:64:"…\MmsiStationType:Ship"`); for a payload nested in another serialized string, the outer lengths change as
  well, so re-serializing is the safer route. Caches can be cleared instead.
- `Isan::equals()`: code that relied on a `0000-0000` version and no version being different (deduplication,
  `in_array(…, true)` over `equals()`, change detection) now sees one ISAN. Stored rows are unchanged, so a UNIQUE
  column or a string-keyed lookup can hold or miss the other form, as with ISBN-10/13 and the two IMEI forms: query
  every form (`WHERE isan IN ('B159D8FA01240000K', 'B159D8FA01240000K00000000E')`), or normalise before storing (cut
  a value whose `version()` is `00000000` to its first 16 or, with check characters, 17 characters).
- Composer: `"msstc4php/vo-identifiers": "^2.0"` (or `"^1.0 || ^2.0"` for code that uses neither of the above). The
  Doctrine bridge needs a release that allows `^2.0`; its column types and lengths are unchanged.
- Static BC checkers (Roave) report the removed `MmsiType` against 1.2.0; that is the expected break.

## [1.2.0] - 2026-11-24

Nothing that 1.1 accepted is refused; `equals()`, the canonical forms and `MAX_LENGTH` are unchanged.

### Added

- `Publication\Iswc::hasValidCheckDigit()`: reports the ISO 15707 Annex B check digit as given in the project leader's text,
  ISO/TC 46/SC 9 N 268 (a committee document); report-only, a wrong check digit is still accepted.
- `Transport\Wmi::region(): ?WmiRegion` and the enum `Transport\WmiRegion` (`Africa`, `Asia`, `Europe`, `NorthAmerica`,
  `Oceania`, `SouthAmerica`) from ISO 3780:2009 Annex A, Table A.1; null for the reserved first characters `D` to `H`, `7`
  and `0`. No country decoding.
- `Energy\Mpan::core()`, `distributorId()` and `uniqueReference()` (Elexon BSC guidance, Ofgem R0083) for the 13- and the
  21-digit form; the check digit is still not verified and the top line is not decoded.
- `Telecom\Iccid::hasE118Length()`: true for the 19 digits of ITU-T E.118 (09/2025); report-only.
- `Publication\Isan::isPrivateVersion()`: true for a version segment `F0000000` to `FFFFFFFF` (ISO 15706-2:2023, 4.6).

### Changed

- `Transport\AirWaybillNumber` accepts one space between the fourth and fifth serial digits (`020-1234 5675`, IATA
  Resolution 600a) and stores the eleven digits as before; `TransportIdentifierFactory` resolves such a value to it alone.
- Source notes: IATA Resolution 600a, ITU-T E.118 (09/2025), ISO 3780:2009 Annex A, ISO 15706-2:2023 and the EIC Reference
  Manual v5.2 are now quoted (`docs/sources/IDENTIFIERS.md`, ruling 14).

## [1.1.0] - 2026-11-24

### Added

- `PublicationIdentifierFactory::tryFromString()`, `TransportIdentifierFactory::tryFromString()` and
  `TelecomIdentifierFactory::tryFromString()`: null where `fromString()` throws a value refusal (vo-base
  `InvalidValueObjectException`, including `AmbiguousValueException`).
- `Transport\Iso15459Identifier::hasValidDodUiiLength()`: true when the value has at most 50 characters (DoD UID Guide
  v2.5, Business Rule 19); report-only, a longer value is still accepted.

## [1.0.0] - 2026-11-23

First stable release: from here on a breaking change needs 2.0.

Backward compatibility (README "Backward compatibility"): 1.x covers public classes, methods, parameter names and return
types, the JSON and string forms, the `value` serialization key and the canonical stored strings, enum backed values and
case names, `MAX_LENGTH`, and the exception classes with their parents. A minor never refuses a value that an earlier 1.x
accepted (a check sourced later is only reported by `hasValid…(): bool`), may accept more only within `MAX_LENGTH`, adds a
class to a factory only in the next major, and may add enum cases and AIM register editions. Not covered: message
wording, `Internal\` and `@internal`, and `masked()` hiding more. CI runs PHP 8.4 and 8.5.

### Changed (BC)

- Requires `msstc4php/vo-gs1 ^1.0` (was `^0.1`; the `toGtin()` methods return its `Key\Gtin`) and `msstc4php/vo-base ^1.4`
  (was `^1.3`).
- `Transport\DataIdentifierField` follows the value-object API: a public `$value` (data identifier and data together),
  `data()` instead of `value()`, the constructor parameter `$data` instead of `$value`, and `tryFrom()` and `isValid()`.
- `Transport\DataIdentifierString::serial()` is `serialNumber()`, as on the other classes; `fromFields()` takes a
  `non-empty-list`.
- `Transport\Vin::modelYear()` is `modelYears()` (it returns the candidates); `Transport\UicVehicleNumber::countryCode()`
  is `uicCountryCode()` (the UIC numeric code, not ISO letters).
- `Publication\Doi` cuts a resolver URL at a raw `?` or `#` (query and fragment were kept as part of the DOI), so such a
  URL gives a shorter DOI than in 0.x. When the part before the raw `?` or `#` is no DOI (`https://doi.org/10.1000/?x`),
  the whole rest is decoded as before, so every URL 0.x accepted is still accepted.
- `Publication\Issn` refuses seven digits (they were completed with a computed check character); `Issn::fromParts()` does
  that on request. The same applies to `Issn::tryFrom()` (null), `Issn::isValid()` (false) and
  `PublicationIdentifierFactory::fromString()` (`InvalidPublicationIdentifierException` instead of an `Issn`).
- `Publication\Isan::equals()` ignores the check characters, so the forms with and without them are equal.
- The six `Exception\Invalid*IdentifierException` classes are `final`; the three factories have a private constructor.
- `Trade\TariffCode` messages start "Tariff code" (was "tariff code").

### Changed

- `Transport\MmsiType` is `Transport\MmsiStationType` (the bridge's `MmsiType` is the Doctrine type of `Mmsi`); cases and
  backed values are unchanged. `Transport\MmsiType` stays as a class alias, so this is no break before 2.0.
- `Transport\Iso15459Identifier` accepts `-` and `/` after the first character (DoD UID Guide v2.5, Business Rule 21 and
  footnote 88: significant in a serial number carried under `25S`); a `J` license plate keeps capital letters and digits.
- `Publication\Doi` accepts `doi: ` with one space. Resolver URLs on `doi.org`, `dx.doi.org`, `www.doi.org` and
  `www.dx.doi.org` stay accepted (frozen lenience; the source note cites the DOI Handbook 2025).

### Deprecated

- `Transport\MmsiType`, an alias of `Transport\MmsiStationType`; removed in 2.0.

### Added

- `Publication\Isbn::toIsbn13()`, `Publication\Issn::fromParts()`.
- `Internal\Reason` holds all five failure reasons; README "Backward compatibility" and "Canonical forms"; class
  docblocks link the source notes on GitHub; the README says how the framework bridges map the value objects (unreleased
  docs commit after 0.2.0).

### Upgrading from 0.2

- `DataIdentifierField::value()` → `data()`; `new DataIdentifierField(identifier: …, value: …)` → `data: …`;
  `(string) $field` or `$field->value` for the whole field.
- `DataIdentifierString::serial()` → `serialNumber()`.
- `Transport\MmsiType` → `Transport\MmsiStationType` in `use` statements, type hints, Doctrine and ODM
  `enumType: MmsiType::class` mappings and framework attributes (Symfony `#[Assert\Choice(callback: [MmsiType::class,
  'cases'])]`). Until 2.0 the old name is a deprecated alias, declared when Composer's autoloader loads (so type
  checks and `instanceof` see it too), so these keep working, and enum cases serialized by 0.x
  (`E:…\Transport\MmsiType:Ship`, in caches, Messenger messages, sessions) unserialize to `MmsiStationType` cases;
  `serialize()` writes the new name. Static analysis sees `MmsiType` as a separate deprecated enum, so code that compares
  its cases with `Mmsi::type()` should switch to the new name. Rows are unaffected (the backed values are the same).
- `Vin::modelYear()` → `modelYears()`; `UicVehicleNumber::countryCode()` → `uicCountryCode()`.
- A seven-digit ISSN: `Issn::fromParts($sevenDigits)` instead of `new Issn($sevenDigits)`.
- A seven-digit ISSN through `Issn::tryFrom()`, `Issn::isValid()` or `PublicationIdentifierFactory`: complete it with
  `Issn::fromParts()` first.
- A DOI stored from a resolver URL with a query or fragment under 0.x keeps it (`10.1000/x?a=b`), while 1.0 builds
  `10.1000/x` from the same URL, so equality lookups built from such URLs no longer match those rows until they are
  cleaned. One-off cleanup: list the candidates (`WHERE doi LIKE '%?%' OR doi LIKE '%#%'`), check each against its source
  URL (a DOI may itself hold a `?` or `#` that the URL percent-encoded), and rewrite the affected rows to
  a bare `Doi` built from the stored value cut at its first `?` or `#` (`new Doi(\strtok($stored, '?#'))`; not through a
  resolver URL, which would percent-decode the already decoded value a second time).
- Code that extended an exception class: catch it instead, or extend vo-base `InvalidValueObjectException`.
- Code that instantiated a factory: call `fromString()` statically.
- Serialized payloads and stored strings of 0.2 read unchanged (the `value` key and the canonical forms are the same).

## [0.2.0] - 2026-11-23

### Changed

- Requires `msstc4php/vo-base ^1.3` (was `^1.2`). vo-base 1.3 runs `parse()` again on `unserialize()` for every string
  value object, so `Internal\ReparsesOnUnserialize` is removed and the identifiers rely on the base. Behaviour is the
  same except the messages: a payload without a string value now fails with "A serialized <class> must hold its value
  as a string." (was "A serialized identifier must hold its value, as a string.") and `__unserialize()` on a built
  object with "A value object is immutable; __unserialize() runs only on a new object." (was "An identifier is
  immutable; ..."). `Transport\DataIdentifierField`, a composite, keeps its own methods and uses the same messages.
- `^1.3` and not `^1.3.1` on purpose: 1.3.1 fixes vo-base's composite value objects, which this package does not use.

## [0.1.0] - 2026-11-22

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
