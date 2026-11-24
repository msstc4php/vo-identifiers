# Known issues

Source gaps and the controller rulings of 2026-11-21 (UTC) (`docs/sources/IDENTIFIERS.md`, "Controller rulings"); the rulings override the plan.

- EID: no `89` prefix rule (SGP.29 forbids it for its own EIDs; E.118-based EIDs start with 89); 32 digits, MOD 97-10 pair (ruling 1).
- IMO number: format only, no company class; A.1215(34) revokes A.1117(30) (ruling 2).
- ISWC and container number: format only, the algorithm text is not reachable and matching an example is not a quote; `Iso6346CheckDigit` deleted (ruling 3). Since 1.2 `Iswc::hasValidCheckDigit()` reports the ISWC check by the SC 9 N 268 committee text (ruling 14.1), never refuses.
- ROR: enforced; ROR names only "ISO/IEC 7064:2003" with two numeric check digits, i.e. MOD 97-10, over the decoded base32 integer (ruling 3).
- VIN: position 9 is never a reason to refuse; `hasValidCheckDigit()` per 49 CFR 565.15(c); `modelYears()` always returns the candidates (ruling 4).
- EIC: validation only (ruling 5); `objectType(): ?EicObjectType` from the Short Guide letters (ruling 9, reached in Task 5).
- Not built: `Wmi::country()`, `Mmsi::country()`, `Isbn::formatted()`, `IcaoAirportCode::region()` (ruling 6). `Wmi::region()` exists since 1.2 (ISO 3780:2009 Table A.1 row groups, ruling 14.2).
- MH10.8.2 DI table is copyrighted: DIs are `\d{0,3}[A-Z]` with a null description (ruling 7).
- MPAN: 13 or 21 digits, digits only. Since 1.2 `core()` (last 13), `distributorId()`, `uniqueReference()` (Elexon BSC guidance V4.0, ruling 14.3); still no `profileClass()`/MTC/LLFC (no quoted widths) and no check-digit test (weights unreached). 22 digits (R0083 SSC Id) stay refused.
- Chemistry/trade: `EicObjectType` has the seven Short Guide letters, another letter is accepted (`objectType()` null). `CasRegistryNumber` drops leading zeros of the first part; CAS and EC take hyphens all or none and no spaces; `UnNumber` takes no spaces; `HsCode`/`TariffCode` strip only the quoted print forms (WCO point, EU spaces, USITC points; a TARIC print form was not reached). `HsCode`/`TariffCode` use `InvalidTradeIdentifierException`; the three chemistry/trade/energy exceptions are built by `Internal\CommodityRules`. CN is 8 digits, TARIC 10 (Regulation 2658/87 Art. 3).
- ICCID: `89` and Luhn; E.118 (09/2025) reached in 1.2 and says 19 digits, but leaves previous assignments unaffected, so only `hasE118Length()` reports it (ruling 14.5); 6 to 20 digits (minimum derived from the structure, ETSI maximum 20; ruling 11). Short `89` numbers overlap Imei/Imsi/Imeisv in the factory.
- Telecom decisions: `Imei` drops spaces and hyphens, 14 digits are valid, `Imeisv::toImei()` gives the 14-digit form; `Imsi` takes 6 to 15 digits and `mnc()`/`msin()` need the MNC length from the caller; `Imei::equals()` compares the first 14 digits; the five Maskable classes use `Internal\TelecomRules::mask()` (leading part, last 2 only when 6+ stay masked, length kept); `TelecomIdentifierFactory` reports 14/15 digit IMEI+IMSI overlaps as ambiguous (every plausible short digit string is an IMSI).
- ISAN second check character: round trips only (ruling 10); coverage R+E+V quoted from ISO 15706-2:2023 in 1.2. `isPrivateVersion()` (4.6). Clause 4.4 says version `0000-0000` equals no version, but `equals()` keeps them apart: frozen for 1.x, 2.0 candidate.
- The ISO 3779/3780, ISO 15706 and ISO 15707 Annex texts are paid; only previews were reachable.
- Publication decisions: `Isbn` refuses 979-0 (ISMN Manual 2.1.1) and the factory returns `Ismn` for it; `Isbn::equals()` compares ISBN-13 forms; `Isan` accepts 16/17/24/26 characters and enforces the check characters that are present (a bare 16-digit value is also an ISNI, so the factory reports Isan, Isni and Orcid ambiguous); `Orcid` is stored hyphenated, `Isni` compact; the ORCID blocks are not enforced; `Ark` drops a leading NMA URL, keeps hyphens in the stored Name and ignores them in `equals()`; `Doi` takes any numeric directory indicator and always needs a registrant (`<dir>.<registrant>/`; `1234/5678` is no DOI), has no length cap (no source states one; the Doctrine type is text), percent-decodes only the resolver-URL form (RFC 3986; DOI Handbook 2025 §4.4.4) and refuses leading/trailing whitespace and control characters; `Issn::toGtin()` throws `InvalidArgumentException` for a bad variant; labels `ISBN`, `ISBN-13`, `ISBN-10`, `ISSN`, `ISMN`, `ISRC`, `ISNI`, `ISAN`, `ISWC` are dropped only when a colon or a space follows (an ISRC may start with `ISRC`).
- Transport decisions: `ImoNumber` takes the `IMO` prefix or bare seven digits and stores `IMO<digits>`; `Mmsi::type()` classifies only by the M.585 leading-digit rules quoted in `docs/sources/IDENTIFIERS.md` (handheld and 979 AMRD numbers are `Unclassified`; `mid()` is null for them and for distress devices); S10 service indicators starting `J K S T W` are refused (footnote 1 of S10-12: reserved, cannot be assigned); `Vin::modelYears()` returns two years or `[]` for position-10 codes `0`, `U`, `Z`; `Iso15459Identifier` is a `[A-Z0-9][A-Z0-9/-]{2,}` string (only a `J` field is capped at 35 and kept to `[A-Z0-9]`), decodes the issuing agency by longest match against `Internal\IssuingAgencyRegister` (AIM register 2026-06-16; prefix-free in this edition, asserted by a test; update the register and that test together) and is not a factory candidate; `Wmi` is a factory candidate, so every 3-letter value without `I`, `O`, `Q` is ambiguous with `IataAirportCode`; `DataIdentifierString` accepts only a single format-06 envelope (`[)>` RS 06 GS field ... RS EOT), fields need a non-empty DI and may have empty data, data is UTF-8 without RS, GS, EOT.
- **Every value object re-validates on unserialize** (wave 3 review CR-001): since 0.2.0 vo-base `^1.3` does it for every string class (serializes `value` alone, re-runs `parse()`, ignores other keys, refuses re-entry on a built object; a missing or non-string value throws vo-base `InvalidValueObjectException` "A serialized <class> must hold its value as a string.", never a family exception). 0.1.0 had its own `Internal\ReparsesOnUnserialize` for this, removed with the move to `^1.3`. `DataIdentifierField` (not a string VO) has its own `__serialize`/`__unserialize` with the same contract and messages. `Tests\Unit\IdentifierContractTest` fails when a value object is missing from its provider.
- **Messages** are `<type> <reason>.` with the five reasons in `Internal\Reason` (`has an invalid format`, `has a wrong check digit`, `has a wrong check character`, `has a reserved service indicator`, `is not carried by data identifier 25S or J`); do not word a failure any other way, and add a new reason there first. Wording is outside the 1.x BC promise.
- **`MAX_LENGTH`** on every bounded class is the Doctrine column length; `IdentifierContractTest` asserts the longest form has that length and that one character more is a *format* error (not a checksum one). Change both together.
- `TelecomRules::mask()` throws `LogicException` when `$head` leaves nothing masked (unreachable from the VOs' minimum lengths).
- `Gtin`, `Gs1CheckDigit`, `KeyRules` (vo-gs1) cannot be `#[UsesClass]` targets: PHPUnit warns (not a valid coverage target) and Infection refuses the run.
- PHPStan max rejects `non-empty-string` on `substr`/`explode` results and the briefing forbids `assert()` to narrow: accessors return `string`.

- **The AIM IAC register is prefix-free by inspection** (the register text does not say so). A test asserts it; when the register is updated
  (`Internal\IssuingAgencyRegister`, new edition = minor release) update that test with it, and re-check longest-match decoding if a code
  becomes the beginning of another.
- **`Imei`/`Isbn` equality spans forms, the string does not** (see domain-model.md): never use the string as a dedup key without normalising.
- **Source reachability for later sessions:** www.elexon.co.uk/MRA/DCUSA (MPAN) answer 403 or are blocked (bscdocs.elexon.co.uk works), ISO 6346 / ISO 15707
  Annex B / ISO 3779 texts are paid; E.118, IATA Res. 600a, ISO 3780:2009 and ISO 15706-2:2023 previews were reached in 1.2 (integrations.md); do not
  enforce rules from Wikipedia or libraries.

## Infection, 2026-11-21 (UTC)

Covered MSI 99.2% over `src/Internal`. The five escaped mutants are equivalent: `S10CheckDigit` digit offset (the weights add up to
44, a multiple of 11, so shifting every digit by a constant cannot change the sum modulo 11, and the plus/minus mutant likewise);
`AirWaybillCheckDigit` `(int)` cast (PHP converts the numeric string itself); `Iso7064Mod97::isValid` empty-string guard (an
empty value fails the remainder test anyway).

## 1.0 release decisions, 2026-11-23 (UTC)

- `Doi` accepts resolver URLs on `doi.org`, `dx.doi.org` and both `www.` forms (review CR-002: the owner rule "never refuse a still-valid value" beats
  the Handbook-only host list; frozen lenience) and cuts at a raw `?`/`#` before decoding. If the cut leaves no DOI (`https://doi.org/10.1000/?x`), the
  whole rest is decoded as in 0.x, so nothing 0.x accepted is refused; the `s` flag of the URL regex matters there (a newline after the `?` must reach
  the final check). A DOI stored from a URL with a query under 0.x keeps it (no migration in the package; CHANGELOG has the cleanup hint).
- `Ark` keeps `?`/`??` inflections in the Name: no quote of the draft's inflection text is in `docs/sources`. Stripping them changes stored forms and
  `equals()`, so it waits for 2.0 even if a quote is found.
- `Iso15459Identifier` is `[A-Z0-9][A-Z0-9/-]{2,}` since review CR-001 (DoD UID Guide v2.5, Rule 21 and footnote 88, reached via the UNT archive); no
  50-character DoD cap (25S is generic ISO/IEC 15459; a report-only `hasDodUiiLength()` was declined for 1.0). A `J` field keeps `[A-Z0-9]` (footnote 88
  does not list `J`), checked in `fromField()`.
- `Transport\MmsiType` alias: `class_alias()` at file scope plus an enum declaration under `if (!\enum_exists(MmsiType::class, false))`, which is
  false at runtime. `if (false)` does not work: PHPStan reports `if.alwaysFalse`. PHPStan treats `MmsiType` as its own enum, so tests reach the alias
  through a `class-string<\BackedEnum>` data provider (a direct `MmsiType::Ship` vs `MmsiStationType::Ship` assertion is `impossibleType`).
- `Issn` no longer completes seven digits (`fromParts()` does). `TelecomIdentifierFactory` stays: bare 14/15 digits are always ambiguous (documented).
- Infection after the 1.0 review fixes (2026-11-23 UTC, `--threads=8`): MSI 98.43 % (1848 mutants, 29 escaped, 0 uncovered; was 98.40 %, 1835, 29); the escaped mutants are the pre-1.0 ones (regex `D` flags and carets that the compaction or
  a later check makes equivalent, the helpers listed below, `Imei::masked()` thresholds, `explode` limits); none in code changed for 1.0.
- `Transport\MmsiType` (deprecated alias of `MmsiStationType` until 2.0) is loaded through composer `autoload.files`:
  PHP does not autoload for parameter/property type checks or `instanceof`, so a PSR-4-only alias would make
  `MmsiStationType::Ship instanceof MmsiType` false and an `MmsiType` parameter throw `TypeError` until something
  named the alias. The stub enum inside the file is for static analysis only.

## 1.2.0 decisions, 2026-11-24 (UTC)

- Every 1.2 addition is report-only or an accessor (ruling 14 in `docs/sources/IDENTIFIERS.md`); the only accepted-set change is the AWB serial
  space (`020-1234 5675`), checked unambiguous in the transport factory; `MAX_LENGTH` stays 11 (stored form), so the bridge is unchanged.
- `WmiRegion` backed values are lowercase kebab strings (`north-america`); reserved first characters (`D`-`H`, `7`, `0`) give null, not a case, so
  SAE allocating them later only adds cases.
- `Mpan::core()` is `substr(-13)` for both forms: Ofgem R0083 puts the MPAN core on the bottom line, after the top line.
- Infection (`--git-diff-lines`, 2026-11-24 UTC): 125 mutants, 2 escaped, both equivalent in `IswcCheckDigit::compute`: `$i = -1` adds
  `d[-1] * 0`; `ord + 48` adds `96 * 45 = 4320`, a multiple of 10.
