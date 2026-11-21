# Domain model

Every value object is a string value object (`AbstractStringValueObject`) holding the compact canonical form of its identifier.
Exceptions are `Exception\Invalid*IdentifierException`; messages never carry the input. Per-class rules with their quoted sources
are in `docs/sources/IDENTIFIERS.md` ("Outcome per class"); the README class table lists each class, source and check.

- Enforced only when an official source is quoted; otherwise format only (README "Format only" list, with reasons).
- `Imei::equals()` compares the first 14 digits and `Isbn::equals()` the ISBN-13 forms, while the stored string keeps the form
  given. A string-keyed index or a UNIQUE column therefore sees two values for one `equals()`-identical identifier (a 14- and a 15-digit
  IMEI; ISBN-10 and ISBN-13). `Doi::equals()` folds ASCII case, `Ark::equals()` ignores hyphens: same caveat.
- Registry data decodes and never refuses (`Iso15459Identifier::issuingAgencyCode()` is null for an unregistered code, the value is still valid).
- Maskable: `Imei`, `Imeisv`, `Iccid`, `Imsi`, `Eid` (leading part kept, last 2 digits only when 6+ stay masked, length kept).
- Factories report `AmbiguousValueException` for overlaps (bare ISNI/ORCID/check-less ISAN; three letters that are WMI and IATA; short digit
  strings that are IMEI and IMSI; `89`-prefixed numbers).
