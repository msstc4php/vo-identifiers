# Identifier sources

Every rule that `vo-identifiers` enforces is quoted here from an official source: the issuing body or registration
authority, a regulator's or intergovernmental body's publication, a standard cited by number (a publisher's unmodified
preview counts), or an unmodified Internet Archive copy of one. An aggregator counts only "as quoted from". What could
not be quoted is marked **unreachable** or **not confirmed**; for it the outcome is **format only** (the class checks
length and character set, never a check digit or a table no official source gives). No test vector comes from Wikipedia
or a library.

Research date: 2026-11-21 (UTC); 1.2 sources re-read 2026-11-24 (UTC). Status words: **reached** (read the source text), **preview** (publisher's unmodified
preview of a paid standard), **archive** (Internet Archive copy of an official page), **unreachable**, **not confirmed**
(only a secondary statement). Quotes are short; the full text is at the link.

## Outcome per class

| Class | Rule | Outcome |
|---|---|---|
| Isbn | ISBN-13 check (EAN, weights 1 3) | enforced: ISBN Users' Manual 5th ed. 2005, 6th ed. 2012, 7th ed. 2017 |
| Isbn | ISBN-10 check (mod 11, X) | enforced: agency's own 4th-edition online manual, Internet Archive copy 2005-12-28 (no current manual states it) |
| Isbn | `formatted()` from RangeMessage | **not built** (ruling 6): the agency's terms forbid republishing the range message |
| Issn | check (mod 11, weights 8 to 2, X) | enforced: ISSN Manual Dec 2024 |
| Issn | `toGtin()` (977) | enforced: issn.org page on the EAN-13 (French) |
| Ismn | check (EAN weights), `M` form | enforced: ISMN Users' Manual 2016 |
| Isrc | 12 characters, no check digit | enforced (structure): IFPI ISRC pages |
| Iswc | `T` + 9 digits + check | structure enforced (ISO 15707:2022 preview); **check digit format only** (ruling 3); since 1.2 `hasValidCheckDigit()` reports it per the project leader's Annex B text in ISO/TC 46/SC 9 N 268 (ruling 14.1), never refuses |
| Isan | MOD 37,36 check characters | enforced: ISAN-IA "ISAN Check Characters Calculation" v2.0; second character's coverage (R, E, V; not X) quoted from ISO 15706-2:2023 preview; `isPrivateVersion()` per its 4.6 (ruling 14.6) |
| Isni, Orcid | MOD 11-2 check | enforced: ISO 27729:2012 preview (Annex A), ORCID page |
| Doi | `10.` prefix, `/`, suffix; case-insensitive | enforced (structure): DOI Handbook |
| Ror | `0` + 6 Crockford base32 + 2 digits | **enforced** (ruling 3): ROR cites ISO/IEC 7064 with two numeric check digits, which is the MOD 97-10 system; input is the decimal value of the six characters (see the ROR section) |
| Ark | `ark:` NAAN `/` name | format only (check character optional): draft-kunze-ark-40 |
| Vin | 17 characters, set without I O Q | enforced: 49 CFR 565.13(g) |
| Vin | position 9 check | `hasValidCheckDigit()` per 49 CFR 565.15(c); **never a reason to refuse** (ruling 4); `modelYears(): list<int>` always returns the candidates |
| Wmi | region and country tables | `region(): ?WmiRegion` per ISO 3780:2009 Annex A Table A.1 (preview; ruling 14.2), null for reserved first characters; **country not built** (ruling 14.2: ISO copyright on the cell table, SAE reallocates cells) |
| ContainerNumber | structure | enforced: BIC |
| ContainerNumber | check digit | **format only** (ruling 3): the ISO 6346 algorithm text is not reachable; matching BIC's example `BICU 123456 5` is not a quote |
| ImoNumber | `IMO` + 7 digits | enforced (structure): IMO A.1215(34) |
| ImoNumber | check digit | **format only** (ruling 2): no IMO text gives the weights; source A.1215(34); no `ImoCompanyNumber` |
| Mmsi | structure by leading digits | enforced: ITU-R M.585-10 |
| Mmsi | MID to country | **not built** (ruling 6): ITU copyright, permission needed; `mid()` gives the digits only |
| S10ItemIdentifier | check (weights 8 6 4 2 3 5 9 7) | enforced: UPU S10-12 |
| AirWaybillNumber | serial mod 7 | enforced: IATA Resolution 600a (reproduced in IATA's Cargo Agent's Handbook, Res. 807 Ed. 48) and the Australian Border Force; since 1.2 the optional space between the 4th and 5th serial digits is accepted (ruling 14.4) |
| UicVehicleNumber | Luhn on 11 digits | enforced: Commission Decision 2007/756/EC, Appendix 6 part 3 |
| UnLocode | 2 letters + 3 characters | format only: UNECE Rec. 16 |
| IataAirportCode, IcaoAirportCode | 3 and 4 letters | format only; `IcaoAirportCode::region()` **not built** (ruling 6): Doc 7910 nationality letters unreachable (the document is sold) |
| Iso15459Identifier | IAC + company ID + serial | structure per MH10.8.2 `25S`; IAC list is public (AIM register), no licence text in it |
| DataIdentifierString | format 06 envelope | enforced: ISO/IEC 15434:2005 preview; DI is `\d{0,3}[A-Z]` with a null description (ruling 7): ANSI MH10.8.2-2021 clause 4; **DI table not embeddable** |
| Imei | 14 digits, Luhn check | enforced: 3GPP TS 23.003 V17.7.0 |
| Imeisv | 14 + 2 digits | enforced: 3GPP TS 23.003 |
| Iccid | `89` prefix, Luhn | `89`, Luhn and IIN at most 7 digits per ITU-T E.118 (09/2025, reached 2026-11-24); at most 20 digits per ETSI TS 102 221; 6 to 20 digits accepted, the minimum derived (ruling 11); `hasE118Length()` reports E.118's 19 digits, never refuses (ruling 14.5) |
| Imsi | up to 15 digits | enforced: 3GPP TS 23.003 |
| Eid | 32 digits, MOD 97-10 check pair | enforced: GSMA SGP.29 v1.1; **no `89` prefix rule** (ruling 1) |
| CasRegistryNumber | check (weights 1 up from the right, mod 10) | enforced: CAS page (formula is an image) |
| EcNumber | check (weights 1 to 6, mod 11) | enforced: ECHA |
| UnNumber | `UN` + 4 digits | format only (no check exists) |
| HsCode, TariffCode | 6, 8, 10 digits | format only |
| EnergyIdentificationCode | check character | enforced, validation only (ruling 5): ENTSO-E EIC Data Exchange IG v1.2; `objectType(): ?EicObjectType` from the ENTSO-E Short Guide letters (reached in Task 5, ruling 9), the seven letters also in the EIC Reference Manual v5.2 (caveat: ruling 14.7) |
| Mpan | 13 or 21 digits | 13 or 21 digits; the core (distributor 2, unique reference 10, check digit) per Elexon BSC guidance and Ofgem R0083: `core()`, `distributorId()`, `uniqueReference()` since 1.2 (ruling 14.3); **check digit format only** (weights not reached); top line not decoded (field widths not quoted) |

## Controller rulings (2026-11-21 UTC)

Owner rule: a check digit or structure is enforced only when quoted from an official source; otherwise format only. The rulings override the plan.

1. **Eid.** No `89` prefix rule. GSMA SGP.29 v1.1 clause AE.R02 forbids `89` for EIDs it defines ("such values are reserved for the ITU-T E.118
   based scheme") and EID.P06 drops ISO/IEC 7812-1 compatibility, so both families exist. 32 digits with the MOD 97-10 pair of clause 10.
2. **ImoNumber.** Format only (`IMO` + 7 digits), no `ImoCompanyNumber`. The IMO FAQ says only that the seventh digit is a check digit; no IMO text
   gives the weights (they come from Wikipedia and libraries). A.1215(34) (3 Dec 2025) revokes A.1117(30); cite A.1215(34).
3. **ContainerNumber and Iswc format only; Ror enforced.** ISO 6346 and ISO 15707 Annex B are not reachable, and matching an example is not a quote;
   `Iso6346CheckDigit` is deleted. ROR cites "ISO/IEC 7064:2003" with two numeric check digits, which is the MOD 97-10 system (the 7064 system with a
   two-digit numeric check pair), so Ror is enforced; the input decoding is in the ROR section.
4. **Vin.** Position 9 is never a reason to refuse: 49 CFR 565.15 binds vehicles by the market they are sold in (565.11), which a VIN does not carry, and no
   Canadian, Mexican, Chinese or EU text reached makes it mandatory. `hasValidCheckDigit(): bool` follows 565.15(c); `modelYears(): list<int>` always returns
   the candidates (Table VII), never narrowed by position 7.
5. **Eic.** Validation only: ENTSO-E allows the algorithm only "for the purpose of checking the validity of an allocated EIC code". `EicCheckDigit` has a
   public `isValid()` and no method that computes a check character.
6. **Not built:** `Wmi::country()` (ISO 3780 Table A.1 assigns countries cell by cell under ISO copyright, as of 2009, reallocated since; `region()` is built since 1.2, ruling 14.2), `Mmsi::country()` (ITU copyright), `Isbn::formatted()` (RangeMessage terms),
   `IcaoAirportCode::region()` (Doc 7910 not reached). `Wmi` keeps its 3-character format and raw accessors.
7. **Data identifiers.** `\d{0,3}[A-Z]`, null description (MH10.8.2 clause 4 says "preceded by one, two, or three digits" while Table 1 lists bare `S`, `J`, `P`).
8. **vo-identifiers-doctrine** covered MSI is 66.67 % on the empty registrar; fixed in Task 6; nothing is pushed before the repositories exist.
9. **EicObjectType** is an enum: Task 5 reached the letter table (Short Guide V3). **Mpan**: Task 5 re-checked the sources; without a
   reached layout and check text the 13-digit core is format only and the 21-digit top line is not decoded.
10. **ISAN version check:** round trips only (no official vector with a version segment was reached).
11. **Iccid:** `89` prefix and Luhn as the ITU circular states; lengths only as reached (ETSI TS 102 221: at most 20 digits); Task 4 re-checks E.118 and never
    refuses a length a reached source allows.
12. This file and the README "format only" list record each ruling with its reason.
13. **1.0 rulings (2026-11-23 UTC).** `Doi` cuts a resolver URL at a raw `?` or `#` and accepts the hosts the Handbook names, `doi.org` and
    `dx.doi.org` (DOI section); `Issn` refuses seven digits and `Issn::fromParts()` completes them on request (ISSN section); `Ark` keeps
    inflections, no quote (ARK section); `Iso15459Identifier` keeps `[A-Z0-9]` (Iso15459Identifier section; corrected by ruling 14.7: the 1.0 review then reached the DoD UID Guide v2.5 and the class accepts `-` and `/` after the first character, see "Character set");
    `EnergyIdentificationCode` keeps `-` allowed in every position but the last (deliberate lenience, frozen for 2.x as it was for 1.x); a check that gets an
    official source after 1.0 (`ImoNumber`, `ContainerNumber`, `Iswc`, `Mpan`) is only reported by a `hasValid…(): bool` (2.0 refuses none of them either).
14. **1.2 rulings (2026-11-24 UTC).** No new refusal anywhere (constructor, `tryFrom()`, `isValid()`, factories); `equals()`, canonical forms and
    `MAX_LENGTH` unchanged.
    1. `Iswc`: ISO/TC 46/SC 9 N 268 (the CISAC project leader's Annex B text, a committee document; the 2022 Annex B itself was not reached) is the
       source of the report-only `hasValidCheckDigit()` (helper `IswcCheckDigit`); its worked example `T-034.524.680-1` is the test vector.
    2. `Wmi::region(): ?WmiRegion` from ISO 3780:2009 Annex A Table A.1 (row groups; null for the reserved `D`-`H`, `7`, `0`). No `country()`: the
       country cells are ISO copyright, date from 2009 and are reallocated by SAE since.
    3. `Mpan::core()`, `distributorId()` (2 digits), `uniqueReference()` (10 digits) for both forms; the core is the last 13 digits. No top-line
       accessor: Elexon and Ofgem name the fields (profile class, MTC, LLFC) but give no widths (the `00` profile class is one value, not a width rule;
       21 = 8 + 13 is from search snippets only). The check digit stays format only. 22 digits (an R0083 Supply Number with a 4-digit SSC Id) are not
       accepted: the length is inferred, not quoted.
    4. `AirWaybillNumber`: IATA Resolution 600a reached; the optional space between the 4th and 5th serial digits is accepted and dropped (canonical
       form unchanged, 11 digits). The factory stays unambiguous for it: no other transport candidate accepts three digits, an optional hyphen and
       two groups of four digits (tested).
    5. `Iccid::hasE118Length()` reports E.118 (09/2025)'s 19 digits; nothing refused (ETSI allows 20, and "Previous assignments ... are not affected").
    6. `Isan::isPrivateVersion()`: ISO 15706-2:2023 4.6, read in the publisher's preview (not a snippet). Clause 4.4 (a `0000-0000` version equals no
       version) is **not** applied in 1.x, as it changes `equals()`: applied in 2.0 (ruling 15.1). The support.isan.org vector (snippet only) is not used.
    7. Docs: EIC Reference Manual v5.2 caveat on `objectType()` (EIC section); ruling 13's DoD IUID wording corrected.
15. **2.0 rulings (owner, 2026-11-27 UTC).** No new refusal; canonical forms and `MAX_LENGTH` unchanged.
    1. `Isan::equals()` applies clause 4.4 (quoted in the ISAN section): a version `0000-0000` equals no version. Only equality changes: the stored
       form, `__toString()`, JSON, `version()` and `MAX_LENGTH` keep the form given (no data dropped), so two rows can hold one ISAN in two forms,
       as for ISBN-10/13 and IMEI. The check characters stay ignored by `equals()` and enforced when present.
    2. `Transport\MmsiType`, the deprecated 0.x alias of `MmsiStationType`, is removed.

## ISBN, ISSN, ISMN, ISRC, ISWC, ISAN, ISNI, ORCID, DOI, ROR, ARK

### ISBN (ISO 2108)

- ISBN Users' Manual, 7th edition (2017), https://www.isbn-international.org/sites/default/files/ISBN%20International%20Users%20Manual%20-%207th%20edition_absolutely_final.docx, A1.1:
  "Each of the first 12 digits of the ISBN is alternately multiplied by 1 and 3. The check digit is equal to 10 minus the
  remainder resulting from dividing the sum of the weighted products of the first 12 digits by 10 with one exception. If
  this calculation results in an apparent check digit of 10, the check digit is 0." Worked example `978-92-95055-12-4`:
  sum 126, "Check digit = 4". 6th edition (2012) and 5th edition (2005): same text, example `978-0-11-000222-4` (sum 56).
- Prefix, 4.1: "Prefixes that have already been made available by GS1 are 978 and 979". 4.5 (5th/6th ed.): "calculated
  using a modulus 10 algorithm". 6th ed., 4: "Since 1 January 2007, national ISBN agencies only provide ISBNs that consist of
  13 digits". Hyphens: "The use of hyphens or spaces has no lexical significance and is purely to enhance readability."
- ISBN-10, archive: Internet Archive copy (2005-12-28) of the agency's online ISBN Users' Manual, ch. 4.4,
  http://web.archive.org/web/20051228025615/http://www.isbn-international.org:80/en/userman/chapter4.html:
  "It is calculated on a modulus 11 with weights 10-2, using X in lieu of 10 where ten would occur as a check digit.
  ... each of the first nine digits of the ISBN - excluding the check digit itself - is multiplied by a number ranging
  from 10 to 2 and that the resulting sum of the products, plus the check digit, must be divisible by 11 without a
  remainder." Example `ISBN 0-8436-1072-7` (agency's own). The Review Focus value `0-306-40615-2` is the textbook
  example; it satisfies the rule (sum 130, remainder 9 per weights, check 2) but no agency text prints it.
- Conversion: ISMN manual 2016, intro: ISMN conversion keeps the check digit, "This differs from the conversion of an
  old ten-digit ISBN into a 13-digit one which can only be done by recalculating the check digit."
- 979-0 belongs to the ISMN (ISMN manual 2.1.1: "The prefix «979-0» distinguishes the ISMN from the ISBN."): a 979-0
  value is an ISMN, not an ISBN: `Isbn` refuses `9790...` and the factory returns `Ismn` for it (no ambiguity).
- Labels: the manuals print `ISBN 978-...`, `ISBN-13: ...` / `ISBN-10 ...` (the agency's online manual), `ISSN 0317-8471` and `ISMN 979-0-...`; the
  classes drop such a label when a colon or a space follows it.
- **RangeMessage terms**, https://www.isbn-international.org/range_file_generation: "You must not republish material
  from our site (whether in digital or physical form) without our written permission."; "We do not authorise or licence any
  other sites to reproduce our range message in the same or other formats." Terms and conditions,
  https://www.isbn-international.org/node/6, 5.4/5.5: no reproduction, retransmission or distribution of downloaded
  allocation files. **Outcome: ranges are not embedded; `formatted()` is not built.** 7th-edition 5.5 also says the
  five elements are separated by hyphens or spaces "when displayed" only.

### ISSN (ISO 3297)

- ISSN Manual, ISSN International Centre, edition December 2024 (compiled 2025-05-15),
  https://www.issn.org/wp-content/uploads/2025/05/Manual-ISSN_ENG-marc21_May2025.pdf, 5.1.1: "The check digit used in the
  ISSN is calculated on a Modulus 11 basis using weights 8 to 2"; "If the remainder is 10, substitute an upper case X in
  the check digit position. If there is no remainder, put a zero in the check digit position."; the check digit is
  11 minus the remainder. Example `0317-8471`: digits 0317847, sum 120, remainder 10, check digit 1 (the Library of Congress
  excerpt https://www.loc.gov/issn/check.html prints the same table). Structure (field 022 text): "The check digit shall be
  calculated from the other seven digits on a modulus 11 basis with weights 8 to 2, using X in lieu of 10".
- Seven digits (1.0 ruling S2, 2026-11-23 UTC): the Manual defines eight characters and quotes no completion of a value typed without its check
  character, and a truncated ISSN would become another valid ISSN. `Issn` refuses seven digits (0.x completed them); `Issn::fromParts()` computes
  the check character for seven digits on an explicit call, by the 5.1.1 algorithm above.
- Review Focus value `0378-5955` satisfies the rule (weights 8 to 2 over 0378595: sum 160, remainder 6, check digit 5), but no ISSN
  text prints it.
- GTIN, issn.org French page https://www.issn.org/fr/comprendre-lissn/politique-dattribution/lidentification-avec-le-code-a-barres-ean-13/:
  prefix 977, then the seven digits of the ISSN "sans le tiret et sans le caractère de contrôle final", two variant
  digits (00 for a normal title; GS1 General Specifications), EAN check digit. Optional 2- or 5-digit add-on.

### ISMN (ISO 10957)

- ISMN Users' Manual, 5th revised edition 2016, https://ismn-international.org/web_ismn_users_manual_2016/ (PDF),
  2.1.4: "The check digit is calculated on a modulus 10 algorithm with alternating weights of 1 and 3 assigned from left to
  right beginning with the «9»." Example `979-0-3452-4680-5` (sum 95 plus check 5 = 100).
- 2: "An International Standard Music Number (ISMN) consists of the prefix «979-0» followed by nine digits"; printed
  preceded by "ISMN"; four elements separated by hyphen or space; registrant plus item always 8 digits.
- 10: "To turn a ten-digit ISMN into a thirteen-digit ISMN, replace the «M» by «979-0»."; "An old ten-digit ISMN ... keeps
  the same check digit." So `M-3452-4680-5` equals `979-0-3452-4680-5`.

### ISRC (ISO 3901)

- IFPI, https://isrc.ifpi.org/isrc-standard/isrc-structure: "12 alphanumeric characters, which should be presented with the
  4-character prefix 'ISRC' when displayed in printed form"; prefix code (the page's term for country + registrant): "The
  prefix code comprises 2 letters followed by 3 alphanumeric characters."; year of reference: "the last two digits of that
  year"; "The Designation Code consists of five digits assigned by the Registrant." Example `ISRC AA-6QZ-20-00047`. No check
  digit is mentioned. ISRC Handbook 4th edition 2021 (CC BY-ND 4.0) at the same site.
- Prefix letters (1.0 review CR-006, 2026-11-23 UTC). ISRC Handbook, 4th edition 2021, https://isrc.ifpi.org/images/downloads/ISRC_Handbook.pdf,
  3.3.2 "Prefix Code": "This comprises 2 letters followed by 3 alphanumeric characters. This is for compatibility with previous versions of the ISRC
  standard where the two letters represented the country of assignment, and the three alphanumeric characters were allocated to the particular
  Registrant. New allocations remove this distinction and a single block of five characters is allocated, though users may find that the ISRC
  Registration Agency in their country still provides a prefix with a country code in the initial position." IFPI "ISRC Agency Code Allocations -
  October 2026", https://isrc.ifpi.org/images/downloads/Valid-Characters-in-the-ISRC-Prefix.pdf ("First two Characters of ISRC Prefix", agency,
  territory): for example `QM`, `QT`, `QZ` RIAA, United States; `BC`, `BK`, `BP`, `BX` Pro-música Brazil; `CP`, `QN`, `VV`, `ZZ` International ISRC
  Registration Authority, "Worldwide". So `Isrc::countryCode()` returns the two letters unchecked; the allocation list (which changes) is not embedded.

### ISWC (ISO 15707)

- ISO 15707:2022 preview (iteh), clause 4: "An ISWC consists of a prefix element followed by nine digits and a check digit";
  4.2: first element "shall be the letter T" (the registration authority may designate another alphanumeric character);
  4.3: "The work identifier is a 9-digit numeric code ... 000000001 to 999999999"; 4.4: "calculated on a weighted modulus 10 ...
  shown in Annex B". Example `ISWC T-034.524.680-1`; hyphens and dots only for readability. Annex B is not in the preview.
  CISAC business rules (https://cisaciswcuat.developer.azure-api.net/content/SPE_20190806_ISWC_EDI_FileFormat.pdf) require a
  valid check digit "as per Annex B of ISO 15707" and print no formula.
- Format only (ruling 3). Not confirmed from an official text: `S = 1 + sum(i * d_i)` for i = 1..9, `C = (10 - S mod 10) mod 10` (`T` counted as 1). For
  `T-034.524.680`: 1 + 178 = 179, C = 1, equal to the standard's own example, which is not a quote of Annex B.
- 1.2 (ruling 14.1, read 2026-11-24 UTC). ISO/TC 46/SC 9 N 268, 1999-05-11 (ISO/TC 46/SC 9/WG 2 N 11), "Draft Responses to Comments on ISO Committee
  Draft 15707, International Standard Work Code (ISWC)", by the Project Leader (CISAC), distributed by the SC 9 Secretariat, NISO copy
  https://groups.niso.org/higherlogic/ws/public/download/2544/sc9n268.pdf. Response to comment 32 (Annex B): "Calculation of the value of the check
  digit / di : one of the nine digits of the work identifier (i=1 to 9). wi : weight of digit di. dc : check digit." "sumprod = 1 + Σ(i=1..9) di wi"
  "rem = sumprod - 10 x Int(sumprod/10)" "If rem = 0 then dc = 0 else dc = 10 – rem". Worked example: "ISWC T 0 3 4 5 2 4 6 8 0 1 / Weight 1 1 2 3 4 5 6 7
  8 9 1", "sumprod = 179", "rem = 179 – 10 x 17 = 9", "dc = 10 – rem = 1", "T-034.524.680-1 is a valid ISWC". Response to comment 13: "We propose not to
  change the algorithm." This is a CD-stage committee text, not the 2022 Annex B (informative per the DIN/iteh TOC), so the check is only reported by
  `Iswc::hasValidCheckDigit()`; a wrong check digit is still accepted.

### ISAN (ISO 15706)

- ISAN International Agency, "ISAN Check Characters Calculation" version 2.0, 23 Feb 2007, https://isan.org/docs/isan_check_digit_calculation_v2.0.pdf:
  "the first check character for an ISAN shall be calculated over the first 16 hexadecimal digits of the ISAN according to a
  MOD 37,36 system specified in accordance with ISO 7064"; the second "over the 16 hexadecimal digits of its ISAN element
  and the 8 hexadecimal digits of its version segment"; the check character is "one alphanumeric character using Arabic numerals
  0 through 9 and letters A through Z". "the first check character shall be added as the 17th character"; with a version "the
  resulting string of 26 characters incorporates two check characters: one as the 17th character and one as the 26th".
  Validation of a version-bearing ISAN "shall require both correct check characters".
- Check-less forms (fix round 1): the check characters are *added* to the 16 digits ("the first check character shall be added as the 17th character"),
  so the 16-digit root plus episode and the 24-digit form with a version are ISAN forms without their check characters. `Isan` accepts 16, 17, 24
  and 26 characters, enforces every check character that is present, and refuses 25 (a version needs both or neither). A bare 16-digit value is
  also an ISNI: the factory throws `AmbiguousValueException`.
- Procedure (steps 1-7): start with 36; add the digit value (hex digit 0-15); if the sum is 36 or more subtract 36, and if the
  result is 0 use 36; multiply by 2; if 37 or more subtract 37; carry on to the next digit; at the end "If the Adjusted Product
  for the 16th digit of the ISAN equals 1, use 0 for the check character. Otherwise ... subtract it from 37." Annex 1 worked
  example: `ISAN B159-D8FA-0124-0000-K` (K = 20, 37 minus 17). The second check "is similar as for the first" (ruling 10: round trips only).
  No official example with a version segment was reached; the helper is tested with the official root example and, for the
  second character, by round trips (a third-party vector `1881-66c7-3420-0000-3-9f3a-0245-q` exists and is not used).
- 1.2 (ruling 14.6, read 2026-11-24 UTC). ISO 15706-2:2023 preview (iteh),
  https://cdn.standards.iteh.ai/samples/83124/272e013e46454d6ea497aedb2456770d/ISO-15706-2-2023.pdf, 4.3: "“Y” represents the check digit for that
  V-ISAN. It is computed over the positions labelled “R”, “E” and “V”. The position labelled “X” is not included in the computation of “Y”." (confirms
  ruling 10's coverage). 4.4: "To avoid confusion, a V-ISAN version segment of 0 (i.e. 0000-0000) shall be treated as an ISAN without a version
  segment." 4.5: "V-ISAN version segment range 1 (0000 0001 in hexadecimal) to 4 026 531 839 (EFFF FFFF in hexadecimal) are public numbers". 4.6:
  "V-ISAN version segment range 4 026 531 840 (F000 0000 in hexadecimal) to 4 294 967 295 (FFFF FFFF in hexadecimal) are reserved for private numbers and
  shall not be issued from within the V-ISAN register. This corresponds to version segments where the first four bits are 1111 (i.e., hexadecimal value
  F)." So `isPrivateVersion()` is true for a version starting with `F`. 4.4 ("treated as an ISAN without a version segment") changes
  `equals()`, which was frozen for 1.x: applied in 2.0 (ruling 15.1). Annex B (the V-ISAN check) is not in the preview; no official vector with a version was read.

### ISNI (ISO 27729) and ORCID

- ISO 27729:2012 preview (iteh), clause 4: "An ISNI shall consist of 16 digits ... 15 decimal digits, and a check character."
  4.3: written as "ISNI" plus space plus "four blocks of four digits", example `ISNI 1422 4586 3573 0476`; 4.4: check "either a
  decimal digit or the character X ... calculated using the preceding 15 decimal digits in accordance with the ISO/IEC 7064,
  MOD11-2 algorithm". Annex A: "Strings including the check character are valid if the sum of these products is congruent to
  1 (mod 11)"; weight `w_i = 2^(i-1) (mod 11)`, i counted from the right, so the 16 weights are 10 5 8 4 2 1 6 3 7 9 10 5 8 4 2 1;
  the 15 base digits of `1422 4586 3573 047` give 314, plus 6 is 320 = 29 * 11 + 1, check character 6. If the number is 10 it is
  written X.
- ORCID, https://support.orcid.org/hc/en-us/articles/360006897674 (archive copy 2024): "In accordance with ISO/IEC 7064:2003,
  MOD 11-2, this checksum must be "0" - "9" or "X"". Code: `total = (total + digit) * 2` per digit; `remainder = total % 11`;
  `result = (12 - remainder) % 11`; 10 is `X`. Format: "expressed as an https URI ... A hyphen is inserted every 4 digits"; stored
  "https://orcid.org/xxxx-xxxx-xxxx-xxxx". iDs assigned "between 0000-0001-5000-0007 and 0000-0003-5000-0001, or between
  0009-0000-0000-0000 and 0009-0010-0000-0000". Examples: `0000-0002-1825-0097`, `0000-0001-5109-3700`, `0000-0002-1694-233X`.
- An ISNI and an ORCID are the same form (ORCID is "compatible with ISO 27729"): bare 16 characters are ambiguous unless the
  ORCID URL form is given or the value is outside the ORCID blocks.

### DOI (ISO 26324)

- DOI Handbook (archive copies of https://www.doi.org/doi-handbook/HTML/...): "The DOI syntax shall be made up of a DOI prefix and
  a DOI suffix separated by a forward slash."; "The DOI name is case-insensitive and can incorporate any printable characters from
  the legal graphic characters of Unicode."; NOTE "The exact definition of case-insensitivity is expected to be clarified in an
  upcoming revision of the International Standard." Prefix: "<directoryIndicator>.<registrantCode>"; "The directory indicator can
  contain only numeric values. The directory indicator is usually 10"; "The registrant code can contain only numeric values and one or
  several full stops"; "If the directory indicator is 10 then a registrant code is mandatory." Suffix: "Any Unicode 2.0 character can be
  used in the suffix"; examples `10.1000/xyz-123`, `10.1000/123456`, `10.1038/issn.1476-4687`. `doi:` and `https://doi.org/` forms are
  resolver conventions, not Handbook syntax. ASCII folding for `equals()` is the plan's choice within the open definition.
- Directory indicator (fix round 1): the Handbook text quoted above says only that it "can contain only numeric values" and "is usually 10", so
  `Doi` accepts any numeric directory indicator. The prefix is always `<directoryIndicator>.<registrantCode>` (the structure quoted above), so the
  registrant is mandatory for every directory indicator (fix round 2): `1234/5678` and `2020/01/05` are not DOIs.
- Resolver-URL decoding (1.0 review CR-009, 2026-11-23 UTC): DOI Handbook 2025 (https://www.doi.org/doi-handbook/DOIHandbook_2025.pdf), 4.7
  "Percent-Encoding" (listed as 3.7 in its table of contents): "The percent-encoding algorithm specified at RFC 3986 is applied whenever a DOI name is
  used in the path component of a URL, such as in the HTTP Proxy Form"; every byte other than ALPHA, DIGIT and `- . _ ~ ! $ & ' ( ) * + ; = : @` is
  percent-encoded; "EXAMPLE: The DOI name 10.1000/456#789 is percent-encoded to "10.1000/456%23789". Thus the web browser does not encounter the bare
  "#", which it would normally treat as the end of the URL and the start of a fragment". A resolver URL is therefore percent-decoded (RFC 3986
  section 2.1); the stored form is the decoded bare DOI. A bare or `doi:` value is never decoded (`%` is a legal DOI character). Decoded invalid UTF-8
  or control characters are refused.
- Resolver hosts (1.0 review CR-002, owner rule "never refuse a still-valid value", 2026-11-23 UTC). DOI Handbook 2025, 4.4.4 "HTTP proxy form": "the
  URL shall be created by percent-encoding the result of concatenating the string "https://doi.org/" with the percent-encoded DOI name"; "Other HTTP
  proxy forms starting with the string "https://dx.doi.org" are deprecated." 6.3.1, footnote: "An earlier proxy address (https://dx.doi.org) is
  deprecated and its use is discouraged." The Handbook does not name `www.doi.org` or `www.dx.doi.org` as proxies and forbids neither; both answer
  with a redirect to `https://doi.org/<doi>` (302 and 301, checked 2026-11-23 UTC), and 0.1 and 0.2 accepted them. A URL on any of the four hosts
  wraps a valid DOI and the stored form is the bare DOI, so `Doi` accepts `doi.org`, `dx.doi.org` (deprecated, still resolving) and their `www.` forms
  as frozen lenience for 1.x (README "Backward compatibility"). `hdl.handle.net`, a generic Handle proxy, is not accepted and can be added in a minor.
  History: the 2017 edition, chapter 3 (https://www-old.doi.org/doi_handbook/3_Resolution.html), 3.7.3: "https://doi.org is preferred, but the
  earlier syntax dx.doi.org remains fully supported".
- Resolver URL query and fragment (1.0 ruling B4, 2026-11-23 UTC). DOI resolution documentation
  (https://www.doi.org/the-identifier/resources/factsheets/doi-resolution-documentation), 2 "Encoding DOIs for use in URIs", Table 1 "Mandatory
  Encoding": `%` (%25), `"` (%22), `#` (%23), SPACE (%20), `?` (%3F); "#test will be treated as a fragment and removed by the web browser before it
  gets to the proxy." So in a resolver URL a raw `?` or `#` starts the query or fragment (RFC 3986 3.4, 3.5) and is cut before percent-decoding;
  `%3F` and `%23` decode to characters of the DOI. When the part before the raw `?` or `#` is no DOI (`https://doi.org/10.1000/?x`: the suffix
  would be empty), the whole rest is decoded as 0.x did, so no URL that 0.x accepted is refused (it stores `10.1000/?x`, as 0.x).
- `doi:` label: the 2025 Handbook, 4.4.1, prints `doi:10.1006/jmbi.1998.2354` without a space; `doi: ` with one space is accepted as a common print
  form (ruling N9; 0.x refused it), more spaces are refused.
- Length: no source states a maximum length for a DOI name, so `Doi` has no cap; `vo-identifiers-doctrine` will map it to a text type.

### ROR

- https://ror.readme.io/docs/identifier: "The unique string consists of 9 characters: a zero, 6 characters that can be either
  lower-case letters or integers, ... The ROR ID pattern uses base 32 Crockford encoding, which excludes letters "I", "L", "O", and
  "U". The last 2 digits of the ROR identifier are a checksum that follows the ISO/IEC 7064:2003 standard." Regexes
  `^0[a-z|0-9]{6}[0-9]{2}$` and `^0[a-hj-km-np-tv-z|0-9]{6}[0-9]{2}$` (the pipe inside the class is a typo of theirs). Forms:
  `https://ror.org/02mhbdp94`, `ror.org/02mhbdp94`, `02mhbdp94`. **The modulus is not named, and ruling 3 enforces it:** "ISO/IEC 7064" with two numeric check digits is MOD 97-10. Input exactly: `n` is the
  decimal integer value of the six Crockford base32 characters after the leading `0` (alphabet `0123456789abcdefghjkmnpqrstvwxyz`, case-insensitive; the characters are
  decoded, not converted letter by letter to digit pairs as in `Iso7064Mod97` for an LEI), and the check pair is `98 - ((n * 100) mod 97)`, which is MOD 97-10
  of the decimal string of `n` (the `RorCheckDigits` helper does the decoding and feeds `Iso7064Mod97`). The decoding is the reading of ROR's "base 32 Crockford encoding"; it gives the last two digits of `02mhbdp94` (94),
  `01an7q238` (38), `03yrm5c26`, `05gq02987`, `0168r3w48` (decoded `n` of `2mhbdp` is 88649142). Tests use these published IDs.

### ARK

- The ARK Identifier Scheme, draft-kunze-ark-40 (November 2024), https://datatracker.ietf.org/doc/html/draft-kunze-ark-40:
  "[https://NMA/]ark:[/]NAAN/Name[Qualifiers]"; "There is a new form of the label, "ark:", and an old form, "ark:/", both of which
  must be recognized in perpetuity."; NAAN: "opaque strings of one or more "betanumeric" characters", set `bcdfghjkmnpqrstvwxz0123456789`,
  "Since 2001, every assigned NAAN has consisted of exactly five digits"; the Name "is also required". Check character: "If a check
  character is present in an ARK, by convention it is the right-most character of the Base Name" (optional, not mandated). Normalization:
  "ark:" lowercase, NAAN letters lowercase, otherwise case-sensitive; "Hyphens are considered to be insignificant and are always
  ignored in ARKs." Format only.
- Inflections (1.0 ruling S12, 2026-11-23 UTC): the draft's `?` and `??` inflections (requests for metadata) are not quoted in these notes, and
  the quotes above do not say that a trailing `?` is outside the identifier. `Ark` therefore keeps the 0.x behaviour: `?`, `??` and an NMA URL's
  query stay in the stored Name, so `ark:12345/x5wf6pv?` and `ark:12345/x5wf6pv` are unequal. Stripping them needs a quote of the draft's
  inflection text first; until then nothing that 0.x accepted is refused or rewritten. Changing the stored form later is a 2.0 change (G1).

## Transport and logistics

### VIN (ISO 3779) and WMI (ISO 3780)

- 49 CFR 565.13(g) (current CFR, govinfo 2014 edition and eCFR): "Each character in each VIN shall be one of the letters in the set:
  [ABCDEFGHJKLMNPRSTUVWXYZ] or a numeral in the set: [0123456789]" (no I, O, Q). 565.11: applies to vehicles "manufactured on or after
  October 27, 2008 whose VINs have a letter "A" or "B" in the 10th position" and to those "manufactured on or after April 30 2009
  that are not identified by their manufacturer as model year 2009 or earlier".
- 565.15(a)-(d): first section positions 1-3; second positions 4-8; "(c) The third section shall consist of one character, which
  occupies position nine (9) in the VIN. This section shall be the check digit"; fourth positions 10-17. Check: letters per Table III
  (A=1 B=2 C=3 D=4 E=5 F=6 G=7 H=8; J=1 K=2 L=3 M=4 N=5 P=7 R=9; S=2 T=3 U=4 V=5 W=6 X=7 Y=8 Z=9); Table IV weights for positions 1-17:
  8 7 6 5 4 3 2 10 (none) 9 8 7 6 5 4 3 2; "Add the resulting products and divide the total by 11"; Table V: remainder 0 to 9 is the digit,
  remainder 10 is "X". Table VI sample: VIN `1G4AH59H?5G118341`: products sum 411, 411 mod 11 = 4, check digit 4, so `1G4AH59H45G118341`.
- Model year, 565.15(d)(1), Table VII: 2009 = 9, 2010 = A ... 2030 = Y, 2031 = 1 ... 2039 = 9 (and 1980-2009 the same letters and digits one
  cycle earlier). "Note to Table VII: For passenger cars, and for multipurpose passenger vehicles and trucks with a gross vehicle weight
  rating of 4536 kg (10,000 lb) or less, ... If position 7 is alphabetic, the Model Year in Position 10 of the VIN refers to a year in the
  range 2010-2039"; otherwise 1980-2009. `modelYears(): list<int>` returns the candidates 30 years apart and is never narrowed by position 7 (ruling 4).
- 565.15(a): "A "9" shall be placed in the third position of the VIN if the manufacturer identifier is six characters" (WMI of low-volume
  makers uses positions 1-3 plus 12-14). NSAI (Irish WMI issuer, ISO 3780): "A WMI is the first 3 characters of a vehicle Identification
  Number".
- ISO 3779 and ISO 3780 texts: **unreachable** (2009 editions paid; the 1983 iteh previews are scans without a text layer). The country and
  region allocation of the first two WMI characters is therefore not quoted from any official text: see ruling 8. Superseded for the region by
  the 2009 French preview (next item).
- 1.2 (ruling 14.2, read 2026-11-24 UTC). ISO 3780:2009(F) publisher preview,
  https://cdn.standards.iteh.ai/samples/45844/6ed6570f8f37401fa4598643cdaae8ac/ISO-3780-2009.pdf (edition confirmed in 2023). 5.1: "Le premier caractère
  du code WMI doit être une lettre ou un chiffre désignant une zone géographique. ... L'attribution globale du premier caractère est spécifiée dans
  l'Annexe A." 5.2: "Le deuxième caractère du code WMI doit être une lettre ou un chiffre désignant un pays à l'intérieur d'une zone géographique
  spécifique". 5.4.2: "L'organisme national doit utiliser le chiffre «9» comme troisième caractère pour identifier un constructeur produisant moins de
  500 véhicules par an. Dans ce cas, les 3e, 4e et 5e caractères de l'indicateur (VIS) doivent être attribués par l'organisme national" (the ISO text
  for `isSmallManufacturer()`). Annex A (normative): "L'agence internationale responsable de la maintenance des codes WMI ... est la SAE"; "Le Tableau
  A.1 spécifie les premier et deuxième caractères de la répartition globale des codes WMI." Table A.1 "Distribution globale des codes WMI", rows by
  zone géographique: `A B C` "Afrique"; `D E F G H` "Codes en réserve"; `J K L M N P R` "Asie"; `S T U V W X Y Z` "Europe"; `1 2 3 4 5` "Amérique du
  Nord"; `6` "Oceanie"; `7` "Codes en réserve"; `8 9` "Amérique du Sud"; `0` "Codes en réserve". `Wmi::region()` maps exactly these rows to
  `WmiRegion` and returns null for the reserved ones (a reserved first character is still accepted). The second-character country cells
  ("© ISO 2009 – Tous droits réservés", maintained and reallocated by SAE since) are not embedded: no `country()`.
- Pre-1981 VINs and non-17-character numbers are outside ISO 3779.

### ContainerNumber (ISO 6346, BIC)

- BIC, https://www.bic-code.org/bic-codes/: "An owner/operator code of 3 letters; A fourth letter used as equipment identifier; A serial
  number of 6 Arabic numerals; A seventh digit (check digit) providing a means of validating the recording and/or transmission
  accuracy of the data." Example "(theoretical- for a container): BICU 123456 5". Equipment identifier letters U, J, Z per the BIC check
  digit page (https://www.bic-code.org/check-digit-calculator/: "U for freight containers, J for detachable freight container-related
  equipment, and Z for trailers and chassis"). That page prints no algorithm ("calculated using an algorithm that takes into account
  both the BIC code and serial number").
- **Format only (ruling 3); the algorithm is not quoted from any official text.** ISO 6346 and ISO 6346:2022 are paid (iso.org/standard/90821.html). The usual description
  (value of A = 10, B = 12, skipping 11, 22, 33; ten characters weighted by 2^0 ... 2^9; sum mod 11; 10 becomes 0) reproduces BIC's own
  `BICU 123456 5` (sum 5494, 5494 mod 11 = 5). `CSQU3054383` (Review Focus 1) gives 3 by the same method; no official text prints it.

### ImoNumber

- IMO Resolution A.1215(34), 3 December 2025 (Integrated IMO Identification Number Scheme), para 5: "The IMO ship identification number is made
  up of the three letters "IMO" in front of seven digits (e.g. IMO 8712345)"; para 24: company and registered owner: "the three letters "IMO",
  followed by either "Company" or "Registered Owner", in front of seven digits"; operative para 4 "REVOKES resolution A.1117(30)".
  https://wwwcdn.imo.org/localresources/en/KnowledgeCentre/IndexofIMOResolutions/AssemblyDocuments/A.1215(34).pdf
- IMO Ship Number FAQ: "It is a six-digit sequential unique number followed by a check digit. The integrity of an IMO number can be verified
  using its check digit." No algorithm. See ruling 2. The Review Focus value `IMO 9074729` is not printed by any IMO text reached.

### Mmsi

- ITU-R M.585-10 (04/2026), https://www.itu.int/dms_pubrec/itu-r/rec/m/R-REC-M.585-10-202604-I!!PDF-E.pdf, Annex 1: ship station "9-digit unique ship
  station identity in the format M1I2D3X4X5X6X7X8X9 where in the first three digits represent the maritime identification digits (MID) ...
  The MID denotes the administration having jurisdiction over the ship station"; group ship call `01M2I3D4X5...X9`, that is `0` + MID + 5 digits;
  coast station `0102M3I4D5X6...X9`, that is `00` + MID + 4 digits (00MID1XXX coast, 2XXX port, 3XXX pilot, 4XXX AIS repeater, 5XXX AIS base);
  aircraft `111213M4I5D6X7X8X9`, that is `111` + MID + 3 digits (111MID1XX fixed wing, 5XX helicopters); AIS aids to navigation `99MIDXXXX`
  (99MID1XXX physical, 6XXX virtual, 8XXX mobile); craft associated with a parent ship `9182M3I4D5X6...X9`, that is `98` + MID + 4 digits;
  Annex 2: "970 to indicate AIS SART, 972 to indicate man overboard device (MOB), 974 to indicate EPIRB-AIS, and 979 for AMRD Group B devices".
  Handheld VHF with DSC use the ship station format (recommends 3). For AIS-SART the MID is not a nationality.
- MID table, https://itu.int/gladapp/Allocation/MIDs ("Table of Maritime Identification Digits"): about 330 rows, 201 to 775, with an .xlsx
  version; footer "Copyrights © 2022 All Rights Reserved by ITU". ITU copyright page https://www.itu.int/en/Pages/copyright.aspx: "ITU holds
  copyright in the information available on this Web site, unless otherwise stated."; "Request for permission to reproduce the ITU
  materials available on this Web site should be sent to jur@itu.int." **Outcome: the table is not embedded; `mid()` gives the digits only.**

### S10ItemIdentifier

- UPU S10-12 "Identification of postal items - 13-character identifier", approved 17 Oct 2017, https://upu.int/UPU/media/upu/files/postalSolutions/programmesAndServices/standards/S10-12.pdf:
  components: characters 1-2 service indicator (a2), 3-10 serial number (n8), 11 check digit (n1), 12-13 country code (a2). 5.4: "multiply the digits
  in the serial number by the weighting factors 8 6 4 2 3 5 9 7 ... divide this sum by 11 ... subtract the remainder from 11; if the result falls within
  the range 1 to 9, use the result as the check digit; if the result is 10, use 0 ...; if the result is 11, use 5". Example serial 47312482: sum 200,
  remainder 2, check digit 9, "Complete self-checking number: 473124829". Footnote 1 to 5.2, verbatim: "To reduce the risk of confusion between S10 identifier barcodes and barcodes which make
  use of ISO/IEC 15418 data identifiers [9], character combinations JA-JZ; KA-KZ; SA-SZ; TA-TZ and WA-WZ are reserved and cannot be assigned as valid service
  indicator values." The text gives the identifier as 13 characters with "The human-readable component may include spaces for readability, but this is recommended rather than being required."
  **Ruling (round 2): the five reserved ranges are refused** (a structural rule quoted from the issuing body: no such identifier can be validly issued); the message names only the type.

### AirWaybillNumber

- Australian Border Force (regulator), Integrated Cargo System software developers guide, "Air Waybill Number Validation" v1.1 (16 June 2002),
  https://www.abf.gov.au/sdg-subsite/Pages/Check-Digit-Algorithms/Air-Waybill-Number-Validation.aspx (archive copy): "The Air Waybill comprises three
  components: the Airline Code Number which is the issuing carrier's three-digit IATA airline code; a seven digit serial number; a check digit. The check
  digit is based on an unweighted modulus 7 calculation. The seven-digit serial number is simply divided by 7 and the resulting remainder is the check
  digit." Example serial 1234567: remainder 5. IATA's PAWB functional specification (iata.org) says "a carrier prefix, a hyphen followed by 7 number serial number
  and a check digit". IATA Resolution 600a: **unreachable** (members only) until 1.2.
- 1.2 (ruling 14.4, read 2026-11-24 UTC). IATA "Cargo Agent's Handbook, Resolution 807 - China, Edition 48, Effective 1 October 2025", which reproduces
  Resolution 600a, https://www.iata.org/contentassets/e9cb5a72b88f4f68a5cfc572a50b60c9/cah807-en-final.pdf. Attachment 'A' 5.1.2: "the air waybill
  number shall consist of the issuing carrier's three-digit IATA airline code number and a serial number of eight digits including a check digit placed
  in the extreme right hand position"; 5.1.3: "the check digit shall be determined by using the unweighted Modulus 7 system"; 5.1.5: "a separating
  hyphen placed between the airline code number and the serial number shall be used for the number shown in the upper right corner and in the lower
  right corner"; 5.2.1 (carrier's option): "a space may be inserted in the serial number of the airline air waybill between the fourth and fifth
  digits". Attachment 'B' 2.1.3.3: "A space may be inserted in the serial number between the fourth and fifth digits." Appendix 'D': "The check digit
  shall be determined by using the unweighted Modulus 7 system, which divides the first seven digits of the serial number by seven and uses the
  remainder for verification and as the eighth digit." "Assume the first seven digits of the serial number are ‘1234567’" ... "The complete serial
  number shall read as ‘12345675’." Resolution 606 bar code examples: "air waybill 777-12345675", "air waybill 777-76543213", "air waybill
  777-32176546" (test vectors). `AirWaybillNumber` accepts one optional space between the 4th and 5th serial digits (`020-1234 5675`) and stores the
  11 digits; a space anywhere else is still refused. `MAX_LENGTH` (stored form) stays 11.

### UicVehicleNumber

- Commission Decision 2007/756/EC (CCS and conventional rail wagon number), Appendix 6 part 3, https://legislation.gov.uk/eudn/2007/756/annex/appendix/6/part/3/2020-12-31
  (UK copy of the EU act; EUR-Lex original not readable here; Implementing Decision (EU) 2018/1614 restates it): "the digits in the even positions of the basic number
  (counting from the right) are taken at their own decimal value"; odd positions are doubled (factors 2 1 2 1 ...), the digits of the products are added, "units digit", the
  check digit is the complement to 10 ("If the units digit is 0, the check digit is also 0"). Examples: basic number 33844796100 gives 33 84 4796 100-8 (digit sum 52); 31513320198 gives
  31 51 3320 198-0 (sum 40). Structure, from the headings of the same Decision's Appendix 6 (legislation.gov.uk/eudn/2007/756/annex/appendix/6/2020-12-31; the Agency for Railways keeps the EVN registers under Regulation (EU) 2016/796, so this is a regulator's act): "PART 4 - CODING OF THE COUNTRIES IN WHICH THE VEHICLES ARE REGISTERED (DIGITS 3-4 AND ABBREVIATION)"; "PART 6 - INTEROPERABILITY CODES USED FOR WAGONS (DIGITS 1-2)"; "PART 3 - RULES FOR THE DETERMINATION OF THE CHECK-DIGIT (DIGIT 12)"; "PART 9 - STANDARD NUMERICAL MARKING OF WAGONS (DIGITS 5 TO 8)".
  The vehicle-type and country tables of Appendix 6 parts 9-13 are ERA downloads and were not read: `vehicleType()` and `uicCountryCode()` are raw digits.

### UnLocode, IATA, ICAO

- UNECE Recommendation 16, https://vocab-locode-40898c.opensource.unicc.org/recommendation16: "UN/LOCODE is a five-character geographic coding scheme"; "The first two
  characters of the UN/LOCODE code are the ISO 3166-1 alpha2 Country Code"; "The 3-character code element for the location name will normally comprise three letters."; "where
  all permutations available for a country have been exhausted, the numerals 2-9 may also be used."; 0 and 1 are not used; `XZ` is for installations with no country. "All UN/CEFACT
  standards are free to use under CC By 4.0 license" (not stated for the directory). "The Secretariat has no means of verifying the accuracy of the contents of UN/LOCODE". Not embedded.
- ICAO, https://www.icao.int/operational-safety/Designators-and-indicators: Doc 7910 is "a listing of four-letter location indicators", "published quarterly", sold by ICAO
  (data on request); the four-letter indicators correspond "where available" to "three-character International Air Transport Association (IATA) location identifier codes". The first-letter
  nationality table is in Doc 7910 and was not reached: no `region()`. No list embedded for IATA or ICAO.

### Iso15459Identifier and DataIdentifierString

- AIM Registration Authority for ISO/IEC 15459, https://www.aimglobal.org/registration-authority-iso-iec-15459/ and the register "Register of Issuing Agency Codes for ISO/IEC 15459, version
  2026-06-16" (CEN/ISO/AIM), https://www.aimglobal.org/wp-content/uploads/2026/06/Register-IAC-15459-2-20260616.pdf: "Issuing agencies are authorized and registered by the Registration
  Authority; they are identified by an Issuing Agency Code which is assigned by the Registration Authority, and which is part of the unique identifier for the transport unit."
  "The Registration Authority publishes the register of Issuing Agencies and Issuing Agency Codes." Codes in the register are 1 to 3 characters (for example `LN`, `VBS`, `ND`, `XID`
  for the DOI Foundation). The register carries no terms of use. ISO/IEC 15459-1 to -6 texts were not reached beyond this introduction; the structure IAC + company identification number
  + serial is stated by ANSI MH10.8.2 for DI `25S`: "structured as a sequence of 3 concatenated data elements: IAC, followed by CIN, followed by the supplier assign serial number that
  is unique within the CIN holder's domain" (25S is the data identifier; the license plate `J`: "Unique license plate number assigned to a transport unit", an..35).
- ANSI MH10.8.2-2021 "Data Identifiers" (MHI, approved 11 Feb 2021, updated 9 Sep 2025), clause 4: "The format for DIs shall be a capital letter, preceded by one, two, or three
  digits. The allowable alphanumeric (an) character set shall be A-Z and 0-9." (Table 1 also lists bare-letter DIs such as `S`, `J`, `P`: the clause text and the table disagree;
  accept `\d{0,3}[A-Z]`.) Clause 5: characters after the DI "shall include all characters in UTF-8". `S` "Serial number or code assigned by the Supplier to an entity for its lifetime",
  `1P` item identification code assigned by supplier, `P` by customer, `Q` quantity, `V` supplier code assigned by customer. **Copyright: "No part of this publication may be
  reproduced in any form without prior written permission of the publisher." The DI table is not embedded; DIs parse with no description.**
- ISO/IEC 15434:2005 preview (iteh), 4.1: message header "The complete Message Header is: [)>RS"; "The Compliance Indicator shall be the first three characters ... [)> (left
  bracket, right parenthesis, and greater than)"; 4.1.1.2 format trailer "the non-printable ISO 646 character "RS""; 4.1.2 message trailer "the End Of Transaction character, "EOT""; Table 1:
  format `06` is "Data using ASC MH10 Data Identifiers", format header `06` followed by the data element separator `GS`, format trailer `RS`. The ISO 646 byte values (RS 0x1E, GS 0x1D,
  EOT 0x04) are in Annex A, which the preview does not include; they are the standard ASCII control characters. The 15434 preview ends before 4.3.7 (format 06 data rules).

### Task 3 implementation notes (2026-11-21 UTC)

- S10: the service indicators `JA-JZ`, `KA-KZ`, `SA-SZ`, `TA-TZ`, `WA-WZ` are refused (footnote 1: "cannot be assigned as valid service indicator values"; ruling of round 2).
- Mmsi: only the leading-digit rules quoted above classify; the handheld-VHF format is not quoted precisely enough (the note says "recommends 3") and `979` (AMRD Group B) has no type of its own, so both are `Unclassified`.
- Iso15459Identifier: `[A-Z0-9]{3,}` (1.0 adds `-` and `/`, see "Character set" below); carried under DI `25S` or `J` only, the two DIs quoted above. The an..35 length of the quoted `J` definition is the only cap, applied to `J` fields only; `25S` has no quoted length. `issuingAgencyCode()` and `issuingAgencyName()` read the code from the start by the AIM register (version 2026-06-16, section 2, code to agency name; no terms of use are printed in it, contact details are not copied). The register text does not state that codes are prefix-free; in this edition they are (0-9, `D`, `J` and two- and three-letter codes, none the beginning of another; a unit test asserts it), so the longest match is the only match. A value starting with no entry decodes to null and is accepted.
- Mmsi: group, coast, AIS aid to navigation, SAR aircraft and craft-associated forms are classified only when the MID digit after the fixed prefix is 2 to 7 (the MID table runs 201 to 775); otherwise `Unclassified`. No separators are stripped: M.585 prints nine contiguous digits.
- AirWaybillNumber: only the hyphen after the prefix is accepted, as ABF and IATA print it (`020-12345675`); spaces are not stripped. Since 1.2 also the space between the 4th and 5th serial digits (ruling 14.4).
- DataIdentifierString: one format-06 envelope only. ISO/IEC 15434 Table 1 and 4.1 (preview) give the header, GS after the format header, RS format trailer and EOT message trailer; the preview ends before the 4.3.7 data rules, so a field is a DI followed by UTF-8 data without RS, GS, EOT (MH10.8.2 clause 5).
- Wmi is a factory candidate and collides with `IataAirportCode` on three letters without I, O, Q.
- Character set (1.0 ruling N6, review CR-001, 2026-11-23 UTC). MH10.8.2 clause 4: "The allowable alphanumeric (an) character set shall be A-Z and
  0-9", the `an` set of the `25S` and `J` definitions. DoD Guide to Uniquely Identifying Items v2.5 (15 Sep 2012), appendix "Business Rules (Version
  4.1)", UNT web-archive copy of acq.osd.mil:
  https://webarchive.library.unt.edu/web/20130223193613id_/http://www.acq.osd.mil/dpap/UID/attachments/DoDUIDGuideVer2_5.pdf. Rule 21, "When
  constructing the UII": "All special characters shall be deleted from the enterprise identifier." "All special characters, except for dashes (-) and
  forward slashes (/) shall be deleted from the original part number, lot or batch number, and serial number." "The UII shall only contain uppercase
  English alphabet characters A through Z, numeric characters 0 through 9, and the special characters "-" and "/"." Footnote 88: "For application in
  IUID the use of dashes (-) and slashes (/) is permitted in DIs as significant characters for part numbers, lot or batch numbers, and serial numbers,
  and in DIs that are composed from these numbers (i.e., S, 18S, 25S, 1P, 30P, 1T, and 30T)." So `Iso15459Identifier` accepts A-Z, 0-9, `-` and `/`
  (at least 3 characters). The first character must be a letter or digit: the value starts with the issuing agency code (codes in the AIM register are
  letters and digits) and Rule 21 removes special characters from the enterprise identifier, so `-` and `/` only occur in the part, lot or
  serial number that follows. Footnote 41: a 25S value is "the concatenation of the IAC, enterprise identifier, and
  unique serial number"; footnote 88 permits `-` and `/` only "as significant characters" of those numbers, not as
  separators between the components. Rule 19 (metadata requirements): "The UII is a non-parsable field, not to exceed 50 characters in length (excludes
  overhead characters). Overhead characters in the encoded data string, such as syntax and data qualifiers, are not part of the UII and are eliminated when
  the UII is constructed." That is a DoD profile of the generic ISO/IEC 15459 identifier carried under `25S`, so no cap is applied; since 1.1.0
  `hasValidDodUiiLength()` only reports whether the value has at most 50 characters (the value is the UII itself, no overhead characters). Footnote 88 does not list `J`, so a `J` license plate keeps the `an..35` set of its MH10.8.2
  definition (0.x accepted only that set). `fromField()` and `parse()` uppercase the data; with these sets the uppercasing loses nothing.

## Telecom

### Imei, Imeisv, Imsi (3GPP TS 23.003 V17.7.0, 2022-10, ETSI TS 123 003)

- 2.2 IMSI: "Not more than 15 digits": MCC three digits, MNC "two or three digits ... The length of the MNC (two or three digits) depends on the value of the MCC"; MSIN the rest.
  The IMSI is composed per ITU-T E.212 (the MCC list is an ITU Operational Bulletin annex, ITU copyright, not embedded).
- 6.2.1 IMEI: "Type Allocation Code (TAC). Its length is 8 digits"; "Serial Number (SNR) ... Its length is 6 digits"; "Check Digit (CD) / Spare Digit (SD)"; "The Check Digit is not
  part of the digits transmitted when the IMEI is checked ... The Check Digit is defined according to the Luhn formula, as defined in annex B." The Check Digit "is not applied to the
  Software Version Number". 6.2.2 IMEISV: TAC 8, SNR 6, SVN 2 digits, 16 digits. Annex B: Luhn over the 14 digits (IMEI labelled D14 ... D1): "Step 1: Double the values of the odd labelled
  digits D1, D3, D5 ... D13", add the digits of the products to the even labelled digits, "If the number obtained in Step 2 ends in 0, then set CD to be 0 ... otherwise ... subtract it from the next
  higher number which does end in 0". Example IMEI `26053179 311383`: Step 2 sum 53, CD = 60 - 53 = 7, so `260531793113837`. (B.1 cites ISO/IEC 7812 Annex B for the Luhn method.)
- So a 14-digit IMEI is valid (the check digit is not transmitted) and a 15-digit one must pass Luhn. The Review Focus value `490154203237518` is the usual textbook IMEI; it passes Luhn
  (checked) but no 3GPP text prints it.

### Iccid (ITU-T E.118, ETSI TS 102 221)

- ITU-T Circular TSB 136 (2025), https://www.itu.int/dms_pub/itu-t/md/25/tsb/cir/T25-TSB-CIR-0136!!MSW-E.docx: the IIN "Together with the MII and the CC it shall have a variable number of digits with a maximum of
  7. The MII "89" is assigned by ISO/IEC to ITU-T for telecommunication purposes in accordance with ISO/IEC 7812-1."; "the parity check digit computed according to the Luhn formula (see ISO/IEC 7812-1,
  Annex B)". ITU IIN page: "Together with MII and CC, it shall have a maximum of 7 digits".
- ETSI TS 102 221 V17.0.0 clause 13.2 EF_ICCID: "File size: 10 bytes"; "Contents: according to Recommendation ITU-T E.118"; "Coding: BCD, left justified and padded with 'F'": so at most 20 digits.
- 1.2 (ruling 14.5, read 2026-11-24 UTC): ITU-T E.118 (09/2025), https://www.itu.int/rec/T-REC-E.118-202509-I/en (free PDF). Clause 1: "In the case
  of a subscriber identity module (SIM) and embedded SIM (eSIM), a PAN is also known as an integrated circuit card identifier (ICCID) number";
  "Previous assignments of E.118 numbering resources are not affected by the amendments made to this Recommendation." Clause 6: "The PAN shall be 19
  digits long."; INoA "length is a variable number of digits with a maximum of 7"; "Major industry identifier (MII) "89" is assigned by ISO/IEC to ITU-T
  for telecommunication purposes in accordance with [ISO/IEC 7812-1]"; "Country code (CC) – variable from 1 to 3 digits in accordance with [ITU-T
  E.164]"; IIN "Together with MII and CC, it shall have a maximum of 7 digits"; "Parity check digit computed according to the Luhn formula (see Annex B
  of [ISO/IEC 7812−1])"; "Administrations should foresee only the generation of PANs with a length of 19 digits." E.118 (05/2006) 4.1: "The maximum
  length of the visible card number (primary account number) shall be 19 characters". Neither edition gives a minimum. Since 19 is stricter than the
  1.x range and earlier assignments and ETSI's 20 digits remain valid, nothing is refused: `Iccid::hasE118Length()` reports exactly 19 digits.
- Before 1.2: the E.118 Recommendation text (09/2025) itself was **not reached** (PDF 404, summary only): the minimum length, the 19-digit PAN maximum and whether 18-digit numbers without a check digit exist are
  not confirmed. Ruling 11: `89` prefix and Luhn as the circular states; at most 20 digits (ETSI); Task 4 re-checked E.118 (itu.int rec page and PDF, 2026-11-21 UTC): the text is still not reachable. Implemented: `89`, Luhn, 6 to 20 digits. 20 is from ETSI (BCD "padded with 'F'", so shorter numbers exist). Neither source gives a minimum; 6 is **derived, not quoted**, from the circular's structure: MII `89`, country code, issuer, individual account (at least one digit each) and the check digit. The first 7 digits are returned raw as `issuerIdentifier()` (the IIN maximum). Consequence: short `89`-prefixed numbers also fit `Imei`, `Imsi` and `Imeisv`, and the factory reports them ambiguous.

### Separators (review CR-014, 2026-11-21 UTC)

The rule is "strip only what the source prints", per class, so the difference between the telecom classes is deliberate:

- `Imei`, `Imeisv`: spaces and hyphens dropped. TS 23.003 annex B prints the example IMEI grouped (`26053179 311383`);
  the hyphen is a Task 4 decision (`.claude/docs/known-issues.md`, "Telecom decisions"), not quoted. Neither changes the digits.
- `Imsi`: digits only. TS 23.003 2.2 defines the IMSI as a digit string ("Not more than 15 digits") and prints no grouped form.
- `Iccid`: digits only. The TSB 136 circular and ETSI TS 102 221 (BCD coding) define a digit string; no grouped form is quoted.
- `Eid`: digits only. SGP.29 EID.R01 defines 32 digits and prints no example, grouped or not.
- `Mpan`: digits only (see "Mpan": no separator is quoted).

### Equality across forms

Ruling (Task 4): `Imei::equals()` compares the first 14 digits (the check digit is derived, not part of the identity). `Isbn::equals()` compares the ISBN-13 forms. Both stored strings keep the form given, so a string-keyed index or a UNIQUE column sees two values.

### Masking

Same style as vo-finance `PaymentCardNumber::masked()` (leading digits, `*`, the length kept). The leading part and the last 2 digits stay visible only when at least 6 digits remain masked, otherwise only the leading part. Leading part: `Imei` and `Imeisv` the 8-digit TAC, `Iccid` the first 4 digits (`89` + 2), `Imsi` the first 5, `Eid` the first 8. So a 14- or 15-digit IMEI shows the TAC only, a 16-digit IMEISV the TAC and the last 2, and an IMSI of up to 12 digits its first 5. `Imeisv` is Maskable because it holds the whole IMEI.

### Eid (GSMA SGP.29 v1.1, 22 March 2024)

- https://www.gsma.com/solutions-and-impact/technologies/esim/wp-content/uploads/2024/03/SGP.29-v1.1.pdf: EID.R01 "The length of the EID SHALL be 32 digits."; 10: "The Check Digits: 2 digits ...
  1. Set the two check digits to 0, 2. Using the resulting 32 digits as a decimal integer, compute the remainder of that number on division by 97, 3. Subtract the remainder from 98, and use the
  decimal result for the two check digits, if the result is one digit long, its value SHALL be prefixed by one digit of 0. Verification: ... compute the remainder of that number on division by 97.
  If the remainder of the division is 1, the verification is successful". That is ISO/IEC 7064 MOD 97-10 on digits (SGP.29 does not name 7064). EID.P06: not required to stay compatible
  with ISO/IEC 7812-1 ("limit the first 2 digits to 89"); AE.R02: EIDs "SHALL not use EIDs that start with 89". No example EID is printed. SGP.02 and SGP.22 were tried (gsma.com, 2026-11-21 UTC) and are not reachable (HTTP 403), so the EID test vectors are self-computed with the clause 10 algorithm, not quoted.
  Copyright notice 2024 GSM Association; the document is non-confidential.

## Chemistry, trade and energy

### CasRegistryNumber

- CAS, https://www.cas.org/support/documentation/chemical-substances/checkdig (page text, and the formula image `checkdig_image_small3.png` read from an Internet Archive copy): "A CAS Registry Number
  includes up to 10 digits which are separated into 3 groups by hyphens. The first part ... has 2 to 7 digits; the second part has 2 digits. The final part consists of a single check digit." Formula:
  `N_i ... N4 N3 - N2 N1 - R`, "iN_i + ... + 4N_4 + 3N_3 + 2N_2 + 1N_1" divided by 10 gives "Q + R/10", "Q represents an integer which is discarded". Example `107-07-3`:
  (5x1) + (4x0) + (3x7) + (2x0) + (1x7) = 33, 33/10 = 3 + 3/10, R = 3. The weight is 1 for the digit next to the check digit, increasing leftwards. Leading zeros weigh nothing and CAS prints none, so `CasRegistryNumber` drops them from the first part (minimum 2 digits, `0050-00-0` is `50-00-0`) and one number has one stored form; hyphens are all or none and no other separator is accepted.

### EcNumber

- ECHA, https://echa.europa.eu/about-ec-and-list-numbers (archive copy): "EC and list numbers historically share the same format of six numerical characters and one calculated check digit."
  "The check digit for the EC or list number is calculated using the ISBN method in both numerical and alphanumerical format." `NNN-NNN-R`, `R=(N1+2N2+3N3+4N4+5N5+6N6) mod 11`; letters A=10 ... Z=35 (I, L, O not
  used in list numbers). Sources: "2xx-xxx-x" and "3xx-xxx-x" EINECS, "4xx-xxx-x" ELINCS, "5xx-xxx-x" NLP; list numbers assigned by ECHA start with 1, 6, 7, 8, 9 or a letter. Examples `100-000-1`, `A00-001-5`
  (10 + 6 = 16, 16 mod 11 = 5). ECHA does not say what happens when R would be 10 (such numbers are simply not issued); keep refusing it. Hyphens are all or none; no other separator is quoted, so spaces are refused.

### UnNumber, HsCode, TariffCode

- UNECE Model Regulations (Orange Book) Rev. 24, https://unece.org/sites/default/files/2025-09/ST_SG_AC10_1_Rev24e_Vol_II_1.pdf: "Goods considered to be dangerous for transport are listed in the Dangerous Goods
  List and assigned a name and a four-digit UN number"; "The UN number preceded by the letters "UN"" on documents. No space is quoted, so `UN 1203` is refused. No check exists. No substance list embedded.
- WCO HS, https://www.wcoomd.org/en/topics/nomenclature/overview/harmonized_system_faq.aspx and the HS Convention: "The first two digits of a heading identify its chapter. The last two give the heading's
  position within that chapter"; headings are four digits, subheadings six; "A zero in a digit position means that level was not subdivided"; parties "must use the six-digit codes of the Harmonized System, or a more
  detailed national extension of them". The nomenclature itself is under WCO licence: not embedded.
- National extensions: EU Commission, Combined Nomenclature page https://taxation-customs.ec.europa.eu/customs/common-customs-tariff-cct/tariff-classification-goods/combined-nomenclature_en: CN code is 8 digits, the
  first six digits are the HS; TARIC adds the 9th and 10th digits (Regulation 2658/87 art. 3 in its 1987 text speaks of "10th and 11th digits" and "In the absence of a Community subdivision, the 10th and 11th digits shall be
  '00'": the current numbering was not reconciled with it; reconciled in Task 5: CN is 8 digits and TARIC 10, see below). US: USITC HTS external user guide, https://www.usitc.gov/documents/hts/hts_external_user_guide.pdf: 8-digit heading/subheading plus a 2-digit
  statistical suffix gives the 10-digit HTS number. Format only: 8 or 10 digits.
- Task 5 reconciliation, Regulation (EEC) No 2658/87 consolidated 2016-06-01 (https://legislation.gov.uk/eur/1987/2658/pdfs/eur_19872658_2016-06-01_en.pdf), Article 3: "1. Each CN subheading shall have an eight digit code number:
  (a) the first six digits shall be the code numbers relating to the headings and subheadings of the harmonized system nomenclature; (b) the seventh and eighth digits shall identify the CN subheadings. ...
  2. The Taric subheadings shall be identified by the 9th and 10th digits which, together with the code numbers referred to in paragraph 1, form the Taric code numbers. In the absence of a Community subdivision, the 9th and 10th digits shall be '00'."
  So CN is 8 digits, TARIC 10; the "10th and 11th digits" wording is the 1987 text. `TariffCode` accepts 8 or 10; `hsSubheading()` is the first 6. Print forms (Task 5 fix round): the Commission's CN page (https://taxation-customs.ec.europa.eu/customs/common-customs-tariff-cct/tariff-classification-goods/combined-nomenclature_en) prints "1806 10 15" (CN, 8 digits) and "1806 10" (HS subheading) with spaces between pairs; the WCO HS FAQ prints a point after the heading, "0205.00", "0103.10"; the USITC's own search pages print HTS numbers with points, "9801.00.10" and "9801.00.10.10", "9903.01.32" (search results of hts.usitc.gov, 2026-11-21 UTC; the HTS external user guide itself answers HTTP 403 here). A TARIC print form was not reached (the Commission page has no TARIC example), so TARIC is taken to follow the CN grouping with a further pair, "0101 21 00 10"; this is **not quoted**. `HsCode` accepts unbroken, "NNNN.NN" and "NNNN NN"; `TariffCode` unbroken, "NNNN NN NN" and "NNNN.NN.NN" with an optional further pair; the separators are stripped and a mix, or any other grouping, is refused. The UK Trade Tariff manual (not an official source here) says spaces and dots in commodity codes are presentation only.
- Exceptions: `HsCode` and `TariffCode` throw `InvalidTradeIdentifierException` (added; Substance's exception would misname them). Helper `Internal\CommodityRules` builds the three exceptions' messages.

### EnergyIdentificationCode (ENTSO-E)

- ENTSO-E, EIC Data Exchange Implementation Guide v1.2, https://eepublicdownloads.entsoe.eu/clean-documents/EDI/Library/cim_based/EIC_Data_Exchange_IG_v1.2.pdf, section "The ENTSO-E check character algorithm": "EIC
  codes are based on a 16-character alphanumeric code. The last character of the coding scheme is the check character that is calculated from the other characters using the ENTSO-E algorithm."
  Steps: replace characters by values (0-9 as is, A=10 ... Z=35, "-" = 36); weights 16 down to 2 for the 15 characters; sum the products; "(36 - MOD ((2107-1), 37))" (the example sum is 2107); the
  result is the check character, a value of 10 or more is converted back to a letter "using the same mechanism"; "If the check character generated is the "-" character (result of the calculation equal to
  36), one of the characters in the proposed EIC code shall be changed". Worked example: `11XRWENET12345-` gives `11XRWENET12345-2`. Also `11Y123456789012T` is given as an example of an area code.
  Structure (ENTSOG EIC manual page https://entsog.eu/eic-codes/manual-of-procedures, through search): 2-character local issuing office, 1 object-type character, 12 characters (A-Z, 0-9, "-"), check character.
  **Terms:** "The algorithm deriving from this document may only be used for the purpose of checking the validity of an allocated EIC code, unless used by an authorised LIO when allocating EIC codes. Any
  other use of the ENTSO-E algorithm is expressly prohibited." (ruling 5). Object-type letters, reached in Task 5 (ruling 9): ENTSO-E, EIC Short Guide and FAQ V3, April 2016 (https://eepublicdownloads.entsoe.eu/clean-documents/EDI/Library/EIC_Short_Guide_and_FAQ_V3_Approved%20April%202016.pdf): "Market Participants (Parties - object type "X")"; "Areas - object type "Y""; "Measuring Points - object type "Z", Energy Metering points"; "Resource objects - object type "W""; "Tie-lines - object type "T""; "Location - object type "V""; "Substations - object type "A"". Structure: "A 2-character number identifying the Issuing Office assigned by ENTSO-E. One Character identifying the object type ... 12 digits, uppercase characters or minus signs allocated by the issuing office. 1 check character"; "Valid characters of an EIC code are A-Z, 0-9 and "-"". `EicObjectType` has those seven cases; the Reference Manual itself was not reached, so a well-formed code with another letter is accepted and `objectType()` is null. Lenient on purpose: the pattern `[0-9A-Z-]{15}[0-9A-Z]` also lets letters and hyphens into the issuing office (the Short Guide calls it a "2-character number") and a hyphen into the object-type position; a code the check character validates is never refused. Lowercase input is uppercased.
- 1.2 (ruling 14.7, read through phase A 2026-11-24 UTC). ENTSO-E, "The Energy Identification Coding Scheme (EIC) Reference Manual Version 5.2 – Final",
  2017-06-01, ENTSOG copy https://www.entsog.eu/sites/default/files/2018-09/EIC_Reference_Manual.pdf: 4.2 names the same seven object types
  ("EIC object type X (Party)", "Y (Area)", "Z (Measurement point)", "W (Resource object)", "T (Tie-line)", "V (Location)", "EIC object Type A
  (Substations)"); 5.3: "The 2-characters identifying the LIO, as assigned by the CIO." (supports the lenient issuing-office pattern); footnote 4: codes
  "issued before the version 5.0 of the EIC reference manual ... shall remain valid until phased out" (supports accepting another letter). **Caveat:**
  4.1: "The EIC code is to be used as a complete string without trying to extract information from its structure." `objectType()` and
  `issuingOffice()` (1.0 API, kept) extract exactly that; treat them as hints, never as a routing or validation key.

### Mpan

- SSEN (licensed distribution network operator), https://ssen.co.uk/globalassets/what-is-a-mpan/mpan-process-for-demand-customers.pdf: the MPAN "is a unique 13-digit reference"; the core is the 13 digits; "Distributor ID: Informs
  you which Distribution ..."; "Check Digit: Validates the previous 12 digits". Elexon (BSC Guidance Note, found by search; page not readable here) calls an MPAN not conforming to modulus 11 invalid. The weights
  (primes 3 5 7 13 17 19 23 29 31 37 41 43, sum mod 11 mod 10) appear in Wikipedia and its mirrors only; the MRA Data Transfer Catalogue and DCUSA text were **not reached**. **Outcome: format only** (13 digits, 21 with the top
  line); no helper built (ruling 9). The 21-digit layout (profile class 2, MTC 3, LLFC 3 on the top line) is the plan's and was not confirmed from a reached text.
- Task 5 re-check (2026-11-21 UTC): elexon.co.uk (glossary, MSID API guideline) answers HTTP 403, mrasco.com, ofgem.gov.uk and the DCUSA site are blocked or carry no text; the Ofgem DWG glossary (Elexon) has no MPAN entry with a layout. Search snippets from Elexon say a full MPAN is 21 digits, the first two of which are the profile class, and that an MPAN "doesn't conform to modulus 11" is a failure reason, but no weights or layout were reached. Outcome (ruling 9, fix round 1): `Mpan` takes 13 or 21 digits, digits only (no separator is quoted), and decodes neither form: no `core()`, `distributorId()`, `profileClass()`. The check digit is not enforced. The 21-digit basis is Elexon search snippets, not a reached text. No source example of an MPAN is printed in the reached SSEN PDF text, so the tests use made-up digits.
- 1.2 (ruling 14.3, read 2026-11-24 UTC). Elexon, BSC Guidance Note "MSID and MPAN Guidance" V4.0, effective 25/07/2019,
  https://bscdocs.elexon.co.uk/guidance-notes/msid-mpan-guidance (www.elexon.co.uk still 403): "A full MPAN is composed of the following information:"
  1 "Profile Type" ("Non Half Hourly customers only, the Half Hourly profile class is represented as 00"), 2 "Meter Timeswitch Code (MTC)", 3 "Line Loss
  Factor Class (LLFC)", 4 "Distribution Identifier", 5 "Unique Reference Number", 6 "Check Digit" ("A number calculated from the Distribution Identifier and
  Unique Reference Number to provide a check digit that other Systems can use to validate the above identifier and reference numbers"). "The combination of
  items 4, 5 and 6 make up a 13-digit identifier referred to as the MPAN Core". "The BSC defines a SVA Metering System Number as a two digit number to
  identify the Distribution System, a ten digit Unique Reference Number provided by the Distributor and a check digit. This is therefore the same
  composition as the MPAN Core." Ofgem, decision on REC Change Proposal R0083 (2023-11),
  https://www.ofgem.gov.uk/sites/default/files/2023-11/Decision%20to%20approve%20REC%20Change%20Proposal%20R0083%20Changes%20to%20Supply%20Number%20Format%20for%20MHHS%20and%20to%20reject%20R0083A.pdf:
  "The current format of the Supply Number includes: a top line, consisting of the Profile Class Id, Meter Timeswitch Code (MTC) and Line Loss Factor Class
  (LLFC) Id; and a bottom line, which sets out the Meter Point Administration Number (MPAN), comprising the Distributor Id, a Unique Reference number and a
  Check Digit." So the core is the trailing 13 digits of the 21-digit form: `core()`, `distributorId()` (2), `uniqueReference()` (10). No MTC or LLFC
  width is quoted, so the top line is not decoded; under R0083 the SSC Id ("Populate with '0000'") replaces the MTC, so a 22-digit Supply Number may exist,
  but its length is inferred and stays refused. Check weights (REC Energy Market Data Specification, DTC J0003) still not reached: format only.

## Table terms of use summary

| Table | Terms | Embedded |
|---|---|---|
| ISBN RangeMessage | no republishing without written permission; no licence to reproduce the range message | no |
| ITU MID table (and MCC/MNC lists) | ITU holds copyright; permission to reproduce is requested from jur@itu.int | no (MID digits only) |
| ICAO Doc 7910 nationality letters | sold by ICAO; not reached | no |
| AIM ISO/IEC 15459 IAC register | public PDF, no terms in it | the 3-or-fewer-character code list may be decoded from it; the controller decides whether to embed (copyright of the register not stated) |
| ANSI MH10.8.2 DI table | "No part ... may be reproduced in any form without prior written permission" | no |
| UN/LOCODE directory | free of charge, accuracy disclaimed; licence of the data not stated | no (changes twice a year) |
| WCO HS nomenclature | WCO licence | no |
| ENTSO-E EIC algorithm | validity checking only | the algorithm only for `isValid` |
| UIC/ERA type and country tables (Appendix 6 parts 9-13) | not read | no |

## Check-digit helpers (Task 1)

| Helper | Source | Used by |
|---|---|---|
| `Luhn` | 3GPP TS 23.003 annex B; Decision 2007/756/EC Appendix 6; ISO/IEC 7812-1 Annex B | Imei, Iccid, UicVehicleNumber |
| `Ean13CheckDigit` | ISBN and ISMN manuals | Isbn, Ismn, the GTIN of an ISSN |
| `Mod11CheckDigit` (weights n+1 down to 2, X) | ISSN Manual; agency's ISBN-10 text | Issn, Isbn-10 |
| `Iso7064Mod11_2` | ISO 27729 Annex A; ORCID | Isni, Orcid |
| `Iso7064Mod37_36` | ISAN-IA v2.0 | Isan |
| `Iso7064Mod97` | SGP.29 clause 10; ISO 7064 MOD 97-10 | Eid; Ror (through `RorCheckDigits`) |
| `RorCheckDigits` | ROR identifier page, decoding as in the ROR section | Ror |
| `VinCheckDigit` | 49 CFR 565.15(c) | Vin |
| `S10CheckDigit` | UPU S10-12 5.4 | S10ItemIdentifier |
| `AirWaybillCheckDigit` | IATA Resolution 600a Appendix 'D'; Australian Border Force | AirWaybillNumber |
| `IswcCheckDigit` | ISO/TC 46/SC 9 N 268 (Annex B project text; report-only) | Iswc::hasValidCheckDigit() |
| `CasCheckDigit` | CAS | CasRegistryNumber |
| `EcNumberCheckDigit` | ECHA | EcNumber |
| `EicCheckDigit` (`isValid()` only, ruling 5) | ENTSO-E | EnergyIdentificationCode |

Not built (no official source, rulings 2, 3, 6, 9, 14): IMO check, container check, MPAN check, the Wmi country table. The ISWC check exists since 1.2 but only reports (ruling 14.1). ISBN-10 and ISSN share `Mod11CheckDigit` (one algorithm, two lengths).
