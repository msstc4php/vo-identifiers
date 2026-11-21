# msstc4php/vo-identifiers

Value objects for publication and media identifiers, transport and logistics identifiers, telecom identities and chemistry,
trade and energy identifiers. Built on [`msstc4php/vo-base`](https://github.com/msstc4php/vo-base) and
[`msstc4php/vo-gs1`](https://github.com/msstc4php/vo-gs1); PHP >= 8.4.

Every check digit and structural rule cites its official source; the notes, with quotes, are in
[`docs/sources/IDENTIFIERS.md`](docs/sources/IDENTIFIERS.md). What a class cannot check from a published source is checked for
format only.

## Status

Scaffold: the source notes and the check-digit helpers (`src/Internal/`) exist; the value object classes follow.

## Format only

A rule is enforced only when an official source is quoted for it; otherwise the class checks length and character set. The reasons are in
[`docs/sources/IDENTIFIERS.md`](docs/sources/IDENTIFIERS.md) ("Controller rulings").

- `ImoNumber`: `IMO` plus seven digits. No IMO text (A.1215(34), the FAQ) gives the check-digit weights; there is no company class.
- `ContainerNumber`: the check digit of ISO 6346 is not enforced; the standard is paid and BIC prints no algorithm (matching an example is not a quote).
- `Iswc`: the check digit is not enforced; ISO 15707 Annex B is not reachable.
- `Vin`: position 9 is never a reason to refuse; `hasValidCheckDigit()` follows 49 CFR 565.15(c), which binds vehicles by the market they are sold in, not by anything in the VIN.
- `Mpan`: the 13-digit core only; the check weights and the 21-digit layout were not reached.
- `Iccid`: `89` and Luhn as the ITU circular states; lengths only as far as a source reaches (at most 20 digits).
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
