# Domain model

Every value object is a string value object (`AbstractStringValueObject`) holding the compact canonical form of its identifier.
Exceptions are `Exception\Invalid*IdentifierException`; messages never carry the input. Per-class rules with their quoted sources
are in `docs/sources/IDENTIFIERS.md` ("Outcome per class"); the README class table lists each class, source and check.

- Enforced only when an official source is quoted; otherwise format only (README "Format only" list, with reasons).
- `Imei::equals()` compares the first 14 digits, `Isbn::equals()` the ISBN-13 forms and `Isan::equals()` root, episode and version (since 2.0 a `0000-0000` version counts as
  none, ISO 15706-2:2023 4.4), while the stored string keeps the form given. A string-keyed index or a UNIQUE column therefore sees two values
  for one `equals()`-identical identifier (a 14- and a 15-digit IMEI; ISBN-10 and ISBN-13; an ISAN with and without a zero version). `Doi::equals()` folds ASCII case, `Ark::equals()` ignores hyphens: same caveat.
- Registry data decodes and never refuses (`Iso15459Identifier::issuingAgencyCode()` is null for an unregistered code, the value is still valid).
- Maskable: `Imei`, `Imeisv`, `Iccid`, `Imsi`, `Eid` (leading part kept, last 2 digits only when 6+ stay masked, length kept).
- Factories report `AmbiguousValueException` for overlaps (bare ISNI/ORCID/check-less ISAN; three letters that are WMI and IATA; short digit
  strings that are IMEI and IMSI; `89`-prefixed numbers).
- `Transport\DataIdentifierField` is a composite (not on the string base): `identifier()`, `data()`, public `$value` (both together, the
  string and serialized form), `tryFrom()`, `isValid()`. `DataIdentifierString::serialNumber()` is the data of DI `S`.
- Enums: `Transport\MmsiStationType` (renamed from `MmsiType` in 1.0, to keep the bridge's `<Vo>Type` names free), `Energy\EicObjectType` and `Transport\WmiRegion` (1.2); cases may
  be added in a minor, backed values are frozen. The 0.x alias `Transport\MmsiType` (a `class_alias` loaded by `autoload.files`) was removed in 2.0.
- Report-only since 1.2: `Iswc::hasValidCheckDigit()`, `Iccid::hasE118Length()`, `Isan::isPrivateVersion()`; `Mpan::core()`/`distributorId()`/`uniqueReference()`.
- `Vin::modelYears()` (candidates), `UicVehicleNumber::uicCountryCode()` (UIC numeric), `Isrc::countryCode()` (ISO 3901 letters, unchecked).
- Canonical forms and the BC promise: README "Canonical forms" and "Backward compatibility".
