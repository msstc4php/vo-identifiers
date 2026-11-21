# Known issues

Source gaps and contradictions with the plan, found on 2026-11-21 (UTC); the full text is in `docs/sources/IDENTIFIERS.md`,
"Controller rulings needed".

- EID: GSMA SGP.29 v1.1 forbids a leading `89` for the EIDs it defines (it is reserved for the E.118 based scheme); the plan says prefix `89`.
- IMO number: no official IMO text gives the check-digit weights; A.1215(34) (3 Dec 2025) revokes A.1117(30).
- ISWC: ISO 15707 Annex B (the weights) is not in the preview; the formula is only on Wikipedia.
- ROR: only "ISO/IEC 7064:2003", no modulus; `98 - (n * 100 mod 97)` reproduces real IDs.
- Container number: ISO 6346 is paywalled; BIC's example `BICU 123456 5` verifies the common algorithm.
- VIN: 49 CFR 565.15(c) binds vehicles by market, not by WMI region; the model-year letter is resolved by position 7 for cars,
  MPVs and light trucks. No official WMI country table is reachable.
- EIC: ENTSO-E restricts the algorithm to validation.
- ISBN RangeMessage and ITU MID: terms forbid embedding; MH10.8.2 DI table is copyrighted.
- MPAN: check-digit weights not reached; format only.
- ICCID: E.118 text not reached; length 18-20 of the plan is unconfirmed (ETSI TS 102 221: at most 20 digits).
- The ISO 6346, ISO 3779/3780, ISO 15706 and ISO 15707 Annex texts are paid; only previews were reachable.

## Infection, 2026-11-21 (UTC)

Covered MSI 99.2% over `src/Internal`. The five escaped mutants are equivalent: `S10CheckDigit` digit offset (the weights add up to
44, a multiple of 11, so shifting every digit by a constant cannot change the sum modulo 11, and the plus/minus mutant likewise);
`AirWaybillCheckDigit` `(int)` cast (PHP converts the numeric string itself); `Iso7064Mod97::isValid` empty-string guard (an
empty value fails the remainder test anyway).
