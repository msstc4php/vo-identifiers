# msstc4php/vo-identifiers

Value objects for publication and media identifiers, transport and logistics identifiers, telecom identities and chemistry,
trade and energy identifiers. Built on [`msstc4php/vo-base`](https://github.com/msstc4php/vo-base) and
[`msstc4php/vo-gs1`](https://github.com/msstc4php/vo-gs1); PHP >= 8.4.

Every check digit and structural rule cites its official source; the notes, with quotes, are in
[`docs/sources/IDENTIFIERS.md`](docs/sources/IDENTIFIERS.md). What a class cannot check from a published source is checked for
format only.

## Status

Initial 0.1.0 content is complete (unreleased): the publication and media, transport and logistics, telecom, chemistry,
trade and energy identifiers and the check-digit helpers they use. Exceptions are one per family
(`Exception\Invalid{Publication,Transport,Telecom,Substance,Trade,Energy}IdentifierException`); no message carries the input.
A message is `<type> <reason>.` with one wording per failure: `has an invalid format`, `has a wrong check digit`, `has a wrong
check character`, `has a reserved service indicator` (`S10ItemIdentifier`) and `is not carried by data identifier 25S or J`
(`Iso15459Identifier::fromField()`). The wording is not covered by the backward-compatibility promise. Every class with a bounded length has a `MAX_LENGTH` constant (characters of the longest stored form; the
Doctrine columns are sized from it). The three factories return a native union of their candidate classes.

## Serialization

Every value object serializes as its value alone (`O:...:{s:5:"value";...}`) and validates it again on `unserialize()`: a
forged or stale payload throws the class's own exception, a payload without a string `value` throws vo-base
`InvalidValueObjectException`, any other key is ignored, and calling `__unserialize()` on a built object throws
`LogicException`.

## Classes

| Class | Source | Value |
|---|---|---|
| `Publication\Isbn` | ISBN Users' Manual 5th-7th ed. (ISBN-13); agency 4th-ed. manual, Internet Archive (ISBN-10); ISMN Manual 2.1.1 (979-0) | ISO 2108; ISBN-13 (978, 979-1 to 979-9) and ISBN-10 both accepted, 979-0 refused (it is an ISMN), the stored form keeps the length given; `isbn13()`, `isbn10()` (null for 979), `isIsbn10()`, `toGtin()`; `equals()` compares the ISBN-13 forms |
| `Publication\Issn` | ISSN Manual Dec 2024; issn.org EAN-13 page | ISO 3297, mod 11 enforced, stored `NNNN-NNNC`; seven digits are refused, `fromParts($sevenDigits)` adds the check character; `toGtin($variant = '00')` gives the 977 GTIN (`InvalidArgumentException` for a bad variant) |
| `Publication\Ismn` | ISMN Users' Manual 2016 | ISO 10957, EAN check enforced, the old `M` form is converted to `979-0...`; `toGtin()` |
| `Publication\Isrc` | IFPI ISRC structure page | ISO 3901, 12 characters, no check digit; `countryCode()` (ISO 3901's term; not checked against ISO 3166), `registrant()`, `year()`, `designation()`, `formatted()` |
| `Publication\Iswc` | ISO 15707:2022 preview | ISO 15707, `T` and 10 digits, format only; `formatted()` |
| `Publication\Isan` | ISAN International Agency, check characters calculation v2.0 | ISO 15706, check characters optional (16, 17, 24 or 26 characters), enforced when present, ignored by `equals()`; `root()`, `episode()`, `version()`, `hasCheckCharacters()`, `formatted()` |
| `Publication\Isni` | ISO 27729:2012 preview | ISO 27729, MOD 11-2 enforced; `formatted()` |
| `Publication\Orcid` | ISO 27729:2012 preview; ORCID identifier page | ISNI-compatible; bare, hyphenated or as `https://orcid.org/...`, stored hyphenated; `uri()`, `toIsni()` |
| `Publication\Doi` | DOI Handbook (ISO 26324); RFC 3986 for the resolver URL | ISO 26324; any numeric directory indicator, registrant always required; bare, `doi:` or resolver URL (percent-decoded), stored bare as given, no length cap; `equals()` folds ASCII case; `prefix()`, `suffix()` |
| `Publication\Ror` | ROR identifier page (cites ISO/IEC 7064) | MOD 97-10 pair enforced, bare or `https://ror.org/...`, stored lowercase bare; `uri()` |
| `Publication\Ark` | draft-kunze-ark-40 | draft-kunze-ark-40, format only; `equals()` ignores hyphens; `naan()`, `name()` |
| `Publication\PublicationIdentifierFactory` | - | `fromString()` builds the one type that accepts the value; several (a bare ISNI/ORCID value, which is also a check-less ISAN) throw `AmbiguousValueException`; none throws `InvalidPublicationIdentifierException` |
| `Transport\Vin` | ISO 3779 preview; 49 CFR 565.15(c) | ISO 3779, 17 characters without `I`, `O`, `Q`, uppercased; position 9 never refuses, `hasValidCheckDigit()` follows 49 CFR 565.15(c); `wmi()`, `vds()`, `vis()`, `modelYears(): list<int>` (the candidates 30 years apart, empty for `0`, `U`, `Z`) |
| `Transport\Wmi` | ISO 3780 preview (format only) | ISO 3780, 3 characters; raw `allocation()`, `manufacturer()`, `isSmallManufacturer()` (a `9` in position 3); no region or country decoding |
| `Transport\ContainerNumber` | BIC (bic-code.org) | ISO 6346 / BIC, owner code, category `U`/`J`/`Z`, 6 digits, check digit (not verified); spaces accepted, stored compact; `formatted()` |
| `Transport\ImoNumber` | IMO Assembly resolution A.1215(34); IMO FAQ | `IMO` + 7 digits (the prefix is optional on input), check digit not verified; `number()`, `formatted()` |
| `Transport\Mmsi`, `MmsiStationType` | ITU-R M.585 | ITU-R M.585, 9 digits; `type()` from the leading digits and the MID digit rule (ship, group ship, coast station, SAR aircraft, AIS aid to navigation, craft associated, distress device, unclassified), `mid()` digits only |
| `Transport\S10ItemIdentifier` | UPU S10-12 | UPU S10, check digit enforced, the reserved service indicators `J K S T W` refused; `serviceIndicator()`, `serialNumber()`, `countryCode()`, `country(): ?CountryCode` |
| `Transport\AirWaybillNumber` | Australian Border Force algorithm; IATA PAWB specification (format) | 3-digit prefix, 7-digit serial, serial mod 7 enforced; the hyphen after the prefix is optional, stored 11 digits, `formatted()` gives `020-12345675` |
| `Transport\UicVehicleNumber` | Commission Decision 2007/756/EC, Appendix 6 part 3 | 12 digits, Luhn enforced; printed (`31 51 3320 198-0`) or compact, stored compact; `vehicleType()` and `uicCountryCode()` are raw digits (the UIC numeric code, not ISO 3166) |
| `Transport\UnLocode` | UNECE Recommendation 16 | UNECE Rec. 16, 2 letters + 3 of letters and 2-9, format only; `country(): ?CountryCode` |
| `Transport\IataAirportCode`, `IcaoAirportCode` | none (format only) | 3 and 4 letters, format only |
| `Transport\Iso15459Identifier` | ISO/IEC 15459 (AIM register 2026-06-16); ANSI MH10.8.2 DI `25S`, `J` | at least 3 capital letters and digits (IAC + company number + serial); `issuingAgencyCode()` and `issuingAgencyName()` from the AIM register (null when unregistered, still accepted); `fromField()` for data identifiers `25S` and `J` (a `J` plate is at most 35 characters) |
| `Transport\DataIdentifierString`, `DataIdentifierField` | ISO/IEC 15434:2005 preview; ANSI MH10.8.2-2021 | ISO/IEC 15434 format `06` envelope, kept byte-exact (RS, GS, EOT included); `fields()`, `field()`, typed `serial()` (DI `S`), `fromFields()`; DIs are `\d{0,3}[A-Z]` with no description |
| `Transport\TransportIdentifierFactory` | - | `fromString()` builds the one type that accepts the value; three letters that are both a WMI and an IATA code throw `AmbiguousValueException`, and any seven digits are an `ImoNumber`; none throws `InvalidTransportIdentifierException` |
| `Telecom\Imei` | 3GPP TS 23.003 V17.7.0 | 3GPP TS 23.003: 14 digits, or 15 with a Luhn check digit; spaces and hyphens dropped; `equals()` compares the first 14 digits, so both forms of one IMEI are equal; `tac()`, `serialNumber()`, `checkDigit()`, `withCheckDigit()`; `masked()` |
| `Telecom\Imeisv` | 3GPP TS 23.003 V17.7.0 | 3GPP TS 23.003: 16 digits, no check digit; `softwareVersion()`, `toImei()`; `masked()` |
| `Telecom\Iccid` | ITU-T Circular TSB 136; ETSI TS 102 221 | ITU-T Circular 136 and ETSI TS 102 221: `89` prefix, Luhn, 6 to 20 digits (the minimum is derived, not quoted); `issuerIdentifier()` raw; `masked()` |
| `Telecom\Imsi` | 3GPP TS 23.003 V17.7.0 | 3GPP TS 23.003: 6 to 15 digits; `mcc()`, `mnc(int)`, `msin(int)` (the caller gives the MNC length); `masked()` |
| `Telecom\Eid` | GSMA SGP.29 v1.1 | GSMA SGP.29: 32 digits with the MOD 97-10 pair, no prefix rule; `masked()` |
| `Telecom\TelecomIdentifierFactory` | - | `fromString()` builds the one type that accepts the value; a value that fits several types (an IMEI and an IMSI, an `89`-prefixed number that is also an ICCID, IMEISV or IMSI) throws `AmbiguousValueException`; the return type is the union of the five classes; none throws `InvalidTelecomIdentifierException` |
| `Substance\CasRegistryNumber` | CAS check-digit page | CAS: 2 to 7 digits, 2 digits, check digit enforced; hyphens all or none, stored hyphenated; leading zeros of the first part dropped (`0050-00-0` is `50-00-0`); `checkDigit()` |
| `Substance\EcNumber` | ECHA EC and list numbers page | ECHA EC or list number, `NNN-NNN-N`, mod 11 check digit enforced; hyphens all or none |
| `Substance\UnNumber` | UNECE Model Regulations Rev. 24 | UNECE Model Regulations Rev. 24, 4 digits, `UN` optional on input and always stored, no spaces; `number()`; format only |
| `Trade\HsCode` | WCO HS FAQ and Convention | WCO HS FAQ and Convention, 6 digits, unbroken or as printed by the WCO (`0103.10`) or the EU (`1806 10`); `chapter()`, `heading()`, `subheading()`; format only |
| `Trade\TariffCode` | EU Commission CN page; Regulation 2658/87 Art. 3; USITC HTS guide | EU CN 8 digits, TARIC 10, US HTS 8 or 10 (other national lengths are not quoted and are refused); unbroken, spaced as the EU prints (`1806 10 15`) or dotted as the USITC prints (`9801.00.10.10`); `hsSubheading(): HsCode`; format only |
| `Energy\EnergyIdentificationCode`, `EicObjectType` | ENTSO-E EIC Data Exchange IG v1.2; EIC Short Guide V3 | ENTSO-E EIC, 16 characters, check character validated; `issuingOffice()`, `objectType(): ?EicObjectType` (null for a letter outside the Short Guide's seven), `objectTypeCode()`, `checkCharacter()` |
| `Energy\Mpan` | none reached (format only) | GB MPAN, 13 digits or 21, digits only, format only; neither form is decoded (no layout reached) |

## Equality across forms

`Imei::equals()` compares the first 14 digits, so the 14-digit and the 15-digit form of one IMEI are equal. `Isbn::equals()`
compares the ISBN-13 forms, so an ISBN-10 equals its ISBN-13. `Isan::equals()` compares root, episode and version, so
the forms with and without check characters are equal. The stored string keeps the form that was given, so a
string-keyed index or a UNIQUE column sees two values for what `equals()` calls one. Normalise first (`Imei::withCheckDigit()`
or the 14 digits, `Isbn::isbn13()`) when a store has to deduplicate. `Doi::equals()` folds ASCII case and `Ark::equals()`
ignores hyphens, with the same caveat.

## Separators

Each class strips only the separators its source prints (the quote is in `docs/sources/IDENTIFIERS.md`): `Imei` and `Imeisv`
drop spaces and hyphens; `Imsi`, `Iccid`, `Eid` and `Mpan` accept digits only, because no reached source prints them
grouped; `Mmsi` and `UnNumber` take no spaces, `AirWaybillNumber` only the hyphen after the prefix.

## Registry and table updates

Registry data decodes and never refuses: a code missing from an embedded table gives `null` and the value is still
accepted, so an outdated table never makes a valid identifier invalid. The only embedded table is the AIM register of
ISO/IEC 15459 issuing agency codes, edition 2026-06-16 (`Internal\IssuingAgencyRegister`, used by
`Iso15459Identifier::issuingAgencyCode()` and `issuingAgencyName()`). A new register edition ships as a minor release.
Tables whose owners forbid republishing (ISBN RangeMessage, ITU MID, MH10.8.2 data identifiers, WCO nomenclature, ICAO
Doc 7910) are not embedded. Terms of each table: `docs/sources/IDENTIFIERS.md`, "Table terms of use summary".

## Format only

A rule is enforced only when an official source is quoted for it; otherwise the class checks length and character set. The reasons are in
[`docs/sources/IDENTIFIERS.md`](docs/sources/IDENTIFIERS.md) ("Controller rulings").

- `ImoNumber`: `IMO` plus seven digits. No IMO text (A.1215(34), the FAQ) gives the check-digit weights; there is no company class.
- `ContainerNumber`: the check digit of ISO 6346 is not enforced; the standard is paid and BIC prints no algorithm (matching an example is not a quote).
- `Iswc`: the check digit is not enforced; ISO 15707 Annex B is not reachable.
- `Vin`: position 9 is never a reason to refuse; `hasValidCheckDigit()` follows 49 CFR 565.15(c), which binds vehicles by the market they are sold in, not by anything in the VIN.
- `Mpan`: 13 or 21 digits, neither decoded and the check digit not enforced: the layout and the check weights appear only in Wikipedia and its mirrors, and the Elexon, MRA, Ofgem and DCUSA pages were not reachable (Elexon answers 403). Digits only; no separator is quoted.
- `Iccid`: `89` and Luhn as the ITU circular states; at most 20 digits (ETSI TS 102 221). Neither source gives a minimum: 6 digits is derived from the structure (`89`, country code, issuer, account of at least one digit each, check digit), not quoted. E.118 itself was not reached.
- `HsCode`, `TariffCode`: no nomenclature is embedded (WCO licence); EU CN has 8 digits and TARIC 10 (Regulation 2658/87 Article 3).
- `Ark`, `UnLocode`, `IataAirportCode`, `IcaoAirportCode`, `UnNumber`, `HsCode`, `TariffCode`: no check exists or no list may be embedded.
- `AirWaybillNumber`: the rule rests on the Australian Border Force's published algorithm; IATA Resolution 600a was not reached.
- Data identifiers (`DataIdentifierString`): `\d{0,3}[A-Z]` with no description, because the MH10.8.2 table is copyrighted.
- `Eid`: 32 digits with the MOD 97-10 pair and no prefix rule (SGP.29 forbids `89` for its own EIDs, E.118-based ones start with it).
- `EnergyIdentificationCode`: validation only; ENTSO-E allows its algorithm for no other use.

## Not built

- `Wmi::region()` and `country()`: no official table (ISO 3780, SAE J1044 are not reachable).
- `Mmsi::country()`: the ITU MID table is under ITU copyright; `mid()` gives the digits only.
- `Isbn::formatted()`: the International ISBN Agency's terms forbid republishing the RangeMessage.
- `IcaoAirportCode::region()`: the nationality letters are in the sold Doc 7910.

## Install

Not on Packagist yet; register the GitHub repositories:

```json
{
    "repositories": [
        { "type": "vcs", "url": "https://github.com/msstc4php/vo-base" },
        { "type": "vcs", "url": "https://github.com/msstc4php/vo-gs1" },
        { "type": "vcs", "url": "https://github.com/msstc4php/vo-identifiers" }
    ],
    "require": {
        "msstc4php/vo-identifiers": "^0.1"
    }
}
```

Runtime requirements: `msstc4php/vo-base ^1.3` and `msstc4php/vo-gs1 ^0.1`. No framework.

## With the bridges

Every class is a string value object (`Transport\DataIdentifierField` included), so `ValueObjectMapper` (vo-base)
and every [framework bridge](https://github.com/msstc4php/vo-base#framework-integration) (Symfony, Laravel, Yii 2,
CodeIgniter 4, Cycle, Spiral) validates, binds and stores it with no codec: the stored value is the canonical string,
in the form given where a class keeps several (see "Equality across forms"). Type a column, a property or a parameter
with the concrete class; the `*IdentifierFactory` classes are for input whose kind is not known in advance, and their
results are stored under their own class. For Doctrine use
[`msstc4php/vo-identifiers-doctrine`](https://github.com/msstc4php/vo-identifiers-doctrine) (one ORM and ODM type
per class).

## License

MIT, see [`LICENSE`](LICENSE).
