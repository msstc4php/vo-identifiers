# Architecture

`src/` has one directory per family, all built: `Publication\`, `Transport\`, `Telecom\`, `Substance\`, `Trade\`, `Energy\`,
`Exception\` (one `final` `Invalid*IdentifierException` per family) and `Internal\`. A family with several candidate types has a factory
(`PublicationIdentifierFactory`, `TransportIdentifierFactory`, `TelecomIdentifierFactory`, static, private constructor) that throws
`AmbiguousValueException` when more than one type accepts the value; the candidate sets are frozen for 1.x (a new class joins in 2.0); `Iso15459Identifier`, the chemistry, trade and energy classes have none.

`src/Internal/` holds `@internal final` helpers:
- check-digit helpers, one per distinct algorithm (two sources that share an algorithm share a helper): `Luhn`, `Ean13CheckDigit`,
  `Mod11CheckDigit`, `Iso7064Mod11_2`, `Iso7064Mod37_36`, `Iso7064Mod97`, `RorCheckDigits`, `VinCheckDigit`, `S10CheckDigit`,
  `AirWaybillCheckDigit`, `IswcCheckDigit` (report-only use), `CasCheckDigit`, `EcNumberCheckDigit`, `EicCheckDigit`;
- per-family rules (`PublicationRules`, `TransportRules`, `TelecomRules` incl. `mask()`, `CommodityRules` for substance/trade/energy):
  `fail`, `invalidFormat`, `compact` strip the printed label and separators;
- `IssuingAgencyRegister` (the one embedded table, AIM IAC register 2026-06-16);

`deptrac.yaml` forbids every framework namespace; Doctrine lives in `vo-identifiers-doctrine`.
