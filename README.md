# msstc4php/vo-identifiers

Value objects for publication and media identifiers, transport and logistics identifiers, telecom identities and chemistry,
trade and energy identifiers. Built on [`msstc4php/vo-base`](https://github.com/msstc4php/vo-base) and
[`msstc4php/vo-gs1`](https://github.com/msstc4php/vo-gs1); PHP >= 8.4.

Every check digit and structural rule cites its official source; the notes, with quotes, are in
[`docs/sources/IDENTIFIERS.md`](docs/sources/IDENTIFIERS.md). What a class cannot check from a published source is checked for
format only.

## Status

The check-digit helpers (`src/Internal/`), the publication and media identifiers and the transport and logistics identifiers
exist; telecom, substance and energy identifiers follow.

## Classes

| Class | Value |
|---|---|
| `Publication\Isbn` | ISO 2108; ISBN-13 (978, 979-1 to 979-9) and ISBN-10 both accepted, 979-0 refused (it is an ISMN), the stored form keeps the length given; `isbn13()`, `isbn10()` (null for 979), `isIsbn10()`, `toGtin()`; `equals()` compares the ISBN-13 forms |
| `Publication\Issn` | ISO 3297, mod 11 enforced, stored `NNNN-NNNC`; a 7-digit value is completed; `toGtin($variant = '00')` gives the 977 GTIN (`InvalidArgumentException` for a bad variant) |
| `Publication\Ismn` | ISO 10957, EAN check enforced, the old `M` form is converted to `979-0...`; `toGtin()` |
| `Publication\Isrc` | ISO 3901, 12 characters, no check digit; `countryCode()`, `registrant()`, `year()`, `designation()`, `formatted()` |
| `Publication\Iswc` | ISO 15707, `T` and 10 digits, format only; `formatted()` |
| `Publication\Isan` | ISO 15706, check characters optional (16, 17, 24 or 26 characters), enforced when present; `root()`, `episode()`, `version()`, `hasCheckCharacters()`, `formatted()` |
| `Publication\Isni` | ISO 27729, MOD 11-2 enforced; `formatted()` |
| `Publication\Orcid` | ISNI-compatible; bare, hyphenated or as `https://orcid.org/...`, stored hyphenated; `uri()`, `toIsni()` |
| `Publication\Doi` | ISO 26324; any numeric directory indicator, registrant always required; bare, `doi:` or resolver URL (percent-decoded), stored bare as given, no length cap; `equals()` folds ASCII case; `prefix()`, `suffix()` |
| `Publication\Ror` | MOD 97-10 pair enforced, bare or `https://ror.org/...`, stored lowercase bare; `uri()` |
| `Publication\Ark` | draft-kunze-ark-40, format only; `equals()` ignores hyphens; `naan()`, `name()` |
| `Publication\PublicationIdentifierFactory` | `fromString()` builds the one type that accepts the value; several (a bare ISNI/ORCID value, which is also a check-less ISAN) throw `AmbiguousValueException`; none throws `InvalidPublicationIdentifierException` |
| `Transport\Vin` | ISO 3779, 17 characters without `I`, `O`, `Q`, uppercased; position 9 never refuses, `hasValidCheckDigit()` follows 49 CFR 565.15(c); `wmi()`, `vds()`, `vis()`, `modelYear(): list<int>` (the candidates 30 years apart, empty for `0`, `U`, `Z`) |
| `Transport\Wmi` | ISO 3780, 3 characters; raw `allocation()`, `manufacturer()`, `isSmallManufacturer()` (a `9` in position 3); no region or country decoding |
| `Transport\ContainerNumber` | ISO 6346 / BIC, owner code, category `U`/`J`/`Z`, 6 digits, check digit (not verified); spaces accepted, stored compact; `formatted()` |
| `Transport\ImoNumber` | `IMO` + 7 digits (the prefix is optional on input), check digit not verified; `number()`, `formatted()` |
| `Transport\Mmsi`, `MmsiType` | ITU-R M.585, 9 digits; `type()` from the leading digits and the MID digit rule (ship, group ship, coast station, SAR aircraft, AIS aid to navigation, craft associated, distress device, unclassified), `mid()` digits only |
| `Transport\S10ItemIdentifier` | UPU S10, check digit enforced, the reserved service indicators `J K S T W` refused; `serviceIndicator()`, `serialNumber()`, `countryCode()`, `country(): ?CountryCode` |
| `Transport\AirWaybillNumber` | 3-digit prefix, 7-digit serial, serial mod 7 enforced; the hyphen after the prefix is optional, stored 11 digits, `formatted()` gives `020-12345675` |
| `Transport\UicVehicleNumber` | 12 digits, Luhn enforced; printed (`31 51 3320 198-0`) or compact, stored compact; `vehicleType()` and `countryCode()` are raw digits |
| `Transport\UnLocode` | UNECE Rec. 16, 2 letters + 3 of letters and 2-9, format only; `country(): ?CountryCode` |
| `Transport\IataAirportCode`, `IcaoAirportCode` | 3 and 4 letters, format only |
| `Transport\Iso15459Identifier` | at least 3 capital letters and digits (IAC + company number + serial); `issuingAgencyCode()` and `issuingAgencyName()` from the AIM register (null when unregistered, still accepted); `fromField()` for data identifiers `25S` and `J` (a `J` plate is at most 35 characters) |
| `Transport\DataIdentifierString`, `DataIdentifierField` | ISO/IEC 15434 format `06` envelope, kept byte-exact (RS, GS, EOT included); `fields()`, `field()`, typed `serial()` (DI `S`), `fromFields()`; DIs are `\d{0,3}[A-Z]` with no description |
| `Transport\TransportIdentifierFactory` | `fromString()` builds the one type that accepts the value; three letters that are both a WMI and an IATA code throw `AmbiguousValueException`, and any seven digits are an `ImoNumber`; none throws `InvalidTransportIdentifierException` |
| `Telecom\Imei` | 3GPP TS 23.003: 14 digits, or 15 with a Luhn check digit; spaces and hyphens dropped; `equals()` compares the first 14 digits, so both forms of one IMEI are equal; `tac()`, `serialNumber()`, `checkDigit()`, `withCheckDigit()`; `masked()` |
| `Telecom\Imeisv` | 3GPP TS 23.003: 16 digits, no check digit; `softwareVersion()`, `toImei()`; `masked()` |
| `Telecom\Iccid` | ITU-T Circular 136 and ETSI TS 102 221: `89` prefix, Luhn, 6 to 20 digits (the minimum is derived, not quoted); `issuerIdentifier()` raw; `masked()` |
| `Telecom\Imsi` | 3GPP TS 23.003: 6 to 15 digits; `mcc()`, `mnc(int)`, `msin(int)` (the caller gives the MNC length); `masked()` |
| `Telecom\Eid` | GSMA SGP.29: 32 digits with the MOD 97-10 pair, no prefix rule; `masked()` |
| `Telecom\TelecomIdentifierFactory` | `fromString()` builds the one type that accepts the value; a value that fits several types (an IMEI and an IMSI, an `89`-prefixed number that is also an ICCID, IMEISV or IMSI) throws `AmbiguousValueException`; the return type is the union of the five classes; none throws `InvalidTelecomIdentifierException` |

## Format only

A rule is enforced only when an official source is quoted for it; otherwise the class checks length and character set. The reasons are in
[`docs/sources/IDENTIFIERS.md`](docs/sources/IDENTIFIERS.md) ("Controller rulings").

- `ImoNumber`: `IMO` plus seven digits. No IMO text (A.1215(34), the FAQ) gives the check-digit weights; there is no company class.
- `ContainerNumber`: the check digit of ISO 6346 is not enforced; the standard is paid and BIC prints no algorithm (matching an example is not a quote).
- `Iswc`: the check digit is not enforced; ISO 15707 Annex B is not reachable.
- `Vin`: position 9 is never a reason to refuse; `hasValidCheckDigit()` follows 49 CFR 565.15(c), which binds vehicles by the market they are sold in, not by anything in the VIN.
- `Mpan`: the 13-digit core only; the check weights and the 21-digit layout were not reached.
- `Iccid`: `89` and Luhn as the ITU circular states; at most 20 digits (ETSI TS 102 221). Neither source gives a minimum: 6 digits is derived from the structure (`89`, country code, issuer, account of at least one digit each, check digit), not quoted. E.118 itself was not reached.
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

Runtime requirements: `msstc4php/vo-base ^1.2` and `msstc4php/vo-gs1 ^0.1`. No framework.

## License

MIT, see [`LICENSE`](LICENSE).
