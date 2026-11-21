# Architecture

`src/Internal/` holds `@internal final` check-digit helpers, one per distinct algorithm (a helper per source algorithm; two
sources that share an algorithm share a helper): `Luhn`, `Ean13CheckDigit`, `Mod11CheckDigit`, `Iso7064Mod11_2`,
`Iso7064Mod37_36`, `Iso7064Mod97`, `RorCheckDigits`, `VinCheckDigit`, `S10CheckDigit`, `AirWaybillCheckDigit`,
`CasCheckDigit`, `EcNumberCheckDigit`, `EicCheckDigit`. The value object areas (`Publication\`, `Transport\`, `Telecom\`,
`Substance\`, `Trade\`, `Energy\`, `Exception\`) follow in later tasks of the wave plan. `deptrac.yaml` forbids every framework
namespace; Doctrine lives in `vo-identifiers-doctrine`.
