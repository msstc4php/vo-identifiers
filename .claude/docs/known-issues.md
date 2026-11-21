# Known issues

Source gaps and the controller rulings of 2026-11-21 (UTC) (`docs/sources/IDENTIFIERS.md`, "Controller rulings"); the rulings override the plan.

- EID: no `89` prefix rule (SGP.29 forbids it for its own EIDs; E.118-based EIDs start with 89); 32 digits, MOD 97-10 pair (ruling 1).
- IMO number: format only, no company class; A.1215(34) revokes A.1117(30) (ruling 2).
- ISWC and container number: format only, the algorithm text is not reachable and matching an example is not a quote; `Iso6346CheckDigit` deleted (ruling 3).
- ROR: enforced; ROR names only "ISO/IEC 7064:2003" with two numeric check digits, i.e. MOD 97-10, over the decoded base32 integer (ruling 3).
- VIN: position 9 is never a reason to refuse; `hasValidCheckDigit()` per 49 CFR 565.15(c); `modelYear()` always returns the candidates (ruling 4).
- EIC: validation only (ruling 5). `objectType(): string` unless Task 5 reaches the letter table (ruling 9).
- Not built: `Wmi::region()`/`country()`, `Mmsi::country()`, `Isbn::formatted()`, `IcaoAirportCode::region()` (ruling 6).
- MH10.8.2 DI table is copyrighted: DIs are `\d{0,3}[A-Z]` with a null description (ruling 7).
- MPAN: Task 5 re-checks; core format only, top line not decoded without a reached text (ruling 9).
- ICCID: `89` and Luhn; at most 20 digits; Task 4 re-checks E.118 (ruling 11).
- ISAN second check character: round trips only (ruling 10).
- The ISO 3779/3780, ISO 15706 and ISO 15707 Annex texts are paid; only previews were reachable.
- Publication decisions: `Isbn` refuses 979-0 (ISMN Manual 2.1.1) and the factory returns `Ismn` for it; `Isbn::equals()` compares ISBN-13 forms; `Isan` accepts 16/17/24/26 characters and enforces the check characters that are present (a bare 16-digit value is also an ISNI, so the factory reports Isan, Isni and Orcid ambiguous); `Orcid` is stored hyphenated, `Isni` compact; the ORCID blocks are not enforced; `Ark` drops a leading NMA URL, keeps hyphens in the stored Name and ignores them in `equals()`; `Doi` takes any numeric directory indicator and always needs a registrant (`<dir>.<registrant>/`; `1234/5678` is no DOI), has no length cap (no source states one; the Doctrine type is text), percent-decodes only the resolver-URL form (RFC 3986; the Handbook's URL-encoding text was not reachable) and refuses leading/trailing whitespace and control characters; `Issn::toGtin()` throws `InvalidArgumentException` for a bad variant; labels `ISBN`, `ISBN-13`, `ISBN-10`, `ISSN`, `ISMN`, `ISRC`, `ISNI`, `ISAN`, `ISWC` are dropped only when a colon or a space follows (an ISRC may start with `ISRC`).
- Transport decisions: `ImoNumber` takes the `IMO` prefix or bare seven digits and stores `IMO<digits>`; `Mmsi::type()` classifies only by the M.585 leading-digit rules quoted in `docs/sources/IDENTIFIERS.md` (handheld and 979 AMRD numbers are `Unclassified`; `mid()` is null for them and for distress devices); S10 service indicators starting `J K S T W` are refused (footnote 1 of S10-12: reserved, cannot be assigned); `Vin::modelYear()` returns two years or `[]` for position-10 codes `0`, `U`, `Z`; `Iso15459Identifier` is a `[A-Z0-9]{3,}` string (only a `J` field is capped at 35), decodes the issuing agency by longest match against `Internal\IssuingAgencyRegister` (AIM register 2026-06-16; prefix-free in this edition, asserted by a test; update the register and that test together) and is not a factory candidate; `Wmi` is a factory candidate, so every 3-letter value without `I`, `O`, `Q` is ambiguous with `IataAirportCode`; `DataIdentifierString` accepts only a single format-06 envelope (`[)>` RS 06 GS field ... RS EOT), fields need a non-empty DI and may have empty data, data is UTF-8 without RS, GS, EOT.
- `Gtin`, `Gs1CheckDigit`, `KeyRules` (vo-gs1) cannot be `#[UsesClass]` targets: PHPUnit warns (not a valid coverage target) and Infection refuses the run.
- PHPStan max rejects `non-empty-string` on `substr`/`explode` results and the briefing forbids `assert()` to narrow: accessors return `string`.

## Infection, 2026-11-21 (UTC)

Covered MSI 99.2% over `src/Internal`. The five escaped mutants are equivalent: `S10CheckDigit` digit offset (the weights add up to
44, a multiple of 11, so shifting every digit by a constant cannot change the sum modulo 11, and the plus/minus mutant likewise);
`AirWaybillCheckDigit` `(int)` cast (PHP converts the numeric string itself); `Iso7064Mod97::isValid` empty-string guard (an
empty value fails the remainder test anyway).
