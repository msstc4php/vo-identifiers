# Identifier sources

Every rule that `vo-identifiers` enforces is quoted here from an official source: the issuing body or registration
authority, a regulator's or intergovernmental body's publication, a standard cited by number (a publisher's unmodified
preview counts), or an unmodified Internet Archive copy of one. An aggregator counts only "as quoted from". What could
not be quoted is marked **unreachable** or **not confirmed**; for it the outcome is **format only** (the class checks
length and character set, never a check digit or a table no official source gives). No test vector comes from Wikipedia
or a library.

Research date: 2026-11-21 (UTC). Status words: **reached** (read the source text), **preview** (publisher's unmodified
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
| Iswc | `T` + 9 digits + check | structure enforced (ISO 15707:2022 preview); **check digit format only** (ruling 3): Annex B is not reachable and matching the standard's example is not a quote |
| Isan | MOD 37,36 check characters | enforced: ISAN-IA "ISAN Check Characters Calculation" v2.0 |
| Isni, Orcid | MOD 11-2 check | enforced: ISO 27729:2012 preview (Annex A), ORCID page |
| Doi | `10.` prefix, `/`, suffix; case-insensitive | enforced (structure): DOI Handbook |
| Ror | `0` + 6 Crockford base32 + 2 digits | **enforced** (ruling 3): ROR cites ISO/IEC 7064 with two numeric check digits, which is the MOD 97-10 system; input is the decimal value of the six characters (see the ROR section) |
| Ark | `ark:` NAAN `/` name | format only (check character optional): draft-kunze-ark-40 |
| Vin | 17 characters, set without I O Q | enforced: 49 CFR 565.13(g) |
| Vin | position 9 check | `hasValidCheckDigit()` per 49 CFR 565.15(c); **never a reason to refuse** (ruling 4); `modelYear(): list<int>` always returns the candidates |
| Wmi | region and country tables | **not built** (ruling 6): ISO 3780 text and SAE J1044 unreachable; `Wmi` keeps its 3-character format and raw accessors |
| ContainerNumber | structure | enforced: BIC |
| ContainerNumber | check digit | **format only** (ruling 3): the ISO 6346 algorithm text is not reachable; matching BIC's example `BICU 123456 5` is not a quote |
| ImoNumber | `IMO` + 7 digits | enforced (structure): IMO A.1215(34) |
| ImoNumber | check digit | **format only** (ruling 2): no IMO text gives the weights; source A.1215(34); no `ImoCompanyNumber` |
| Mmsi | structure by leading digits | enforced: ITU-R M.585-10 |
| Mmsi | MID to country | **not built** (ruling 6): ITU copyright, permission needed; `mid()` gives the digits only |
| S10ItemIdentifier | check (weights 8 6 4 2 3 5 9 7) | enforced: UPU S10-12 |
| AirWaybillNumber | serial mod 7 | enforced on a regulator's publication (Australian Border Force); IATA Res. 600a **unreachable** |
| UicVehicleNumber | Luhn on 11 digits | enforced: Commission Decision 2007/756/EC, Appendix 6 part 3 |
| UnLocode | 2 letters + 3 characters | format only: UNECE Rec. 16 |
| IataAirportCode, IcaoAirportCode | 3 and 4 letters | format only; `IcaoAirportCode::region()` **not built** (ruling 6): Doc 7910 nationality letters unreachable (the document is sold) |
| Iso15459Identifier | IAC + company ID + serial | structure per MH10.8.2 `25S`; IAC list is public (AIM register), no licence text in it |
| DataIdentifierString | format 06 envelope | enforced: ISO/IEC 15434:2005 preview; DI is `\d{0,3}[A-Z]` with a null description (ruling 7): ANSI MH10.8.2-2021 clause 4; **DI table not embeddable** |
| Imei | 14 digits, Luhn check | enforced: 3GPP TS 23.003 V17.7.0 |
| Imeisv | 14 + 2 digits | enforced: 3GPP TS 23.003 |
| Iccid | `89` prefix, Luhn | `89` and Luhn as the ITU circular states; at most 20 digits per ETSI TS 102 221; E.118 text not reached: Task 4 re-checked: still not reached; 6 to 20 digits accepted, the minimum derived from the circular's structure (ruling 11) |
| Imsi | up to 15 digits | enforced: 3GPP TS 23.003 |
| Eid | 32 digits, MOD 97-10 check pair | enforced: GSMA SGP.29 v1.1; **no `89` prefix rule** (ruling 1) |
| CasRegistryNumber | check (weights 1 up from the right, mod 10) | enforced: CAS page (formula is an image) |
| EcNumber | check (weights 1 to 6, mod 11) | enforced: ECHA |
| UnNumber | `UN` + 4 digits | format only (no check exists) |
| HsCode, TariffCode | 6, 8, 10 digits | format only |
| EnergyIdentificationCode | check character | enforced, validation only (ruling 5): ENTSO-E EIC Data Exchange IG v1.2; `objectType()` is a string unless Task 5 reaches the letter table (ruling 9) |
| Mpan | 13-digit core | format only (ruling 9): the check weights and the 21-digit layout were not reached; the top line is not decoded unless Task 5 reaches the text |

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
   Canadian, Mexican, Chinese or EU text reached makes it mandatory. `hasValidCheckDigit(): bool` follows 565.15(c); `modelYear(): list<int>` always returns
   the candidates (Table VII), never narrowed by position 7.
5. **Eic.** Validation only: ENTSO-E allows the algorithm only "for the purpose of checking the validity of an allocated EIC code". `EicCheckDigit` has a
   public `isValid()` and no method that computes a check character.
6. **Not built:** `Wmi::region()` and `country()` (no official table), `Mmsi::country()` (ITU copyright), `Isbn::formatted()` (RangeMessage terms),
   `IcaoAirportCode::region()` (Doc 7910 not reached). `Wmi` keeps its 3-character format and raw accessors.
7. **Data identifiers.** `\d{0,3}[A-Z]`, null description (MH10.8.2 clause 4 says "preceded by one, two, or three digits" while Table 1 lists bare `S`, `J`, `P`).
8. **vo-identifiers-doctrine** covered MSI is 66.67 % on the empty registrar; fixed in Task 6; nothing is pushed before the repositories exist.
9. **EicObjectType** is not an enum unless Task 5 reaches the letter table (otherwise `objectType(): string`). **Mpan**: Task 5 re-checks the sources; without a
   reached layout and check text the 13-digit core is format only and the 21-digit top line is not decoded.
10. **ISAN version check:** round trips only (no official vector with a version segment was reached).
11. **Iccid:** `89` prefix and Luhn as the ITU circular states; lengths only as reached (ETSI TS 102 221: at most 20 digits); Task 4 re-checks E.118 and never
    refuses a length a reached source allows.
12. This file and the README "format only" list record each ruling with its reason.

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

### ISWC (ISO 15707)

- ISO 15707:2022 preview (iteh), clause 4: "An ISWC consists of a prefix element followed by nine digits and a check digit";
  4.2: first element "shall be the letter T" (the registration authority may designate another alphanumeric character);
  4.3: "The work identifier is a 9-digit numeric code ... 000000001 to 999999999"; 4.4: "calculated on a weighted modulus 10 ...
  shown in Annex B". Example `ISWC T-034.524.680-1`; hyphens and dots only for readability. Annex B is not in the preview.
  CISAC business rules (https://cisaciswcuat.developer.azure-api.net/content/SPE_20190806_ISWC_EDI_FileFormat.pdf) require a
  valid check digit "as per Annex B of ISO 15707" and print no formula.
- Format only (ruling 3). Not confirmed from an official text: `S = 1 + sum(i * d_i)` for i = 1..9, `C = (10 - S mod 10) mod 10` (`T` counted as 1). For
  `T-034.524.680`: 1 + 178 = 179, C = 1, equal to the standard's own example, which is not a quote of Annex B.

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
- Resolver-URL decoding: the Handbook's URL-encoding section was **not reachable** (www.doi.org handbook paths answer 404, the Internet Archive copies
  of the old `doi_handbook/*.html` pages hold no text). A resolver URL (`http(s)://[www.][dx.]doi.org/...`) is therefore percent-decoded as RFC 3986
  section 2.1 defines percent-encoding (a standard cited by number); the stored form is the decoded bare DOI. A bare or `doi:` value is never decoded
  (`%` is a legal DOI character). Decoded invalid UTF-8 or control characters are refused.
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
  range 2010-2039"; otherwise 1980-2009. `modelYear(): list<int>` returns the candidates 30 years apart and is never narrowed by position 7 (ruling 4).
- 565.15(a): "A "9" shall be placed in the third position of the VIN if the manufacturer identifier is six characters" (WMI of low-volume
  makers uses positions 1-3 plus 12-14). NSAI (Irish WMI issuer, ISO 3780): "A WMI is the first 3 characters of a vehicle Identification
  Number".
- ISO 3779 and ISO 3780 texts: **unreachable** (2009 editions paid; the 1983 iteh previews are scans without a text layer). The country and
  region allocation of the first two WMI characters is therefore not quoted from any official text: see ruling 8.
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
  and a check digit". IATA Resolution 600a: **unreachable** (members only).

### UicVehicleNumber

- Commission Decision 2007/756/EC (CCS and conventional rail wagon number), Appendix 6 part 3, https://legislation.gov.uk/eudn/2007/756/annex/appendix/6/part/3/2020-12-31
  (UK copy of the EU act; EUR-Lex original not readable here; Implementing Decision (EU) 2018/1614 restates it): "the digits in the even positions of the basic number
  (counting from the right) are taken at their own decimal value"; odd positions are doubled (factors 2 1 2 1 ...), the digits of the products are added, "units digit", the
  check digit is the complement to 10 ("If the units digit is 0, the check digit is also 0"). Examples: basic number 33844796100 gives 33 84 4796 100-8 (digit sum 52); 31513320198 gives
  31 51 3320 198-0 (sum 40). Structure, from the headings of the same Decision's Appendix 6 (legislation.gov.uk/eudn/2007/756/annex/appendix/6/2020-12-31; the Agency for Railways keeps the EVN registers under Regulation (EU) 2016/796, so this is a regulator's act): "PART 4 - CODING OF THE COUNTRIES IN WHICH THE VEHICLES ARE REGISTERED (DIGITS 3-4 AND ABBREVIATION)"; "PART 6 - INTEROPERABILITY CODES USED FOR WAGONS (DIGITS 1-2)"; "PART 3 - RULES FOR THE DETERMINATION OF THE CHECK-DIGIT (DIGIT 12)"; "PART 9 - STANDARD NUMERICAL MARKING OF WAGONS (DIGITS 5 TO 8)".
  The vehicle-type and country tables of Appendix 6 parts 9-13 are ERA downloads and were not read: `vehicleType()` and `countryCode()` are raw digits.

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
- Iso15459Identifier: `[A-Z0-9]{3,}`; carried under DI `25S` or `J` only, the two DIs quoted above. The an..35 length of the quoted `J` definition is the only cap, applied to `J` fields only; `25S` has no quoted length. `issuingAgencyCode()` and `issuingAgencyName()` read the code from the start by the AIM register (version 2026-06-16, section 2, code to agency name; no terms of use are printed in it, contact details are not copied). The register text does not state that codes are prefix-free; in this edition they are (0-9, `D`, `J` and two- and three-letter codes, none the beginning of another; a unit test asserts it), so the longest match is the only match. A value starting with no entry decodes to null and is accepted.
- Mmsi: group, coast, AIS aid to navigation, SAR aircraft and craft-associated forms are classified only when the MID digit after the fixed prefix is 2 to 7 (the MID table runs 201 to 775); otherwise `Unclassified`. No separators are stripped: M.585 prints nine contiguous digits.
- AirWaybillNumber: only the hyphen after the prefix is accepted, as ABF and IATA print it (`020-12345675`); spaces are not stripped.
- DataIdentifierString: one format-06 envelope only. ISO/IEC 15434 Table 1 and 4.1 (preview) give the header, GS after the format header, RS format trailer and EOT message trailer; the preview ends before the 4.3.7 data rules, so a field is a DI followed by UTF-8 data without RS, GS, EOT (MH10.8.2 clause 5).
- Wmi is a factory candidate and collides with `IataAirportCode` on three letters without I, O, Q.

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
- The E.118 Recommendation text (09/2025) itself was **not reached** (PDF 404, summary only): the minimum length, the 19-digit PAN maximum and whether 18-digit numbers without a check digit exist are
  not confirmed. Ruling 11: `89` prefix and Luhn as the circular states; at most 20 digits (ETSI); Task 4 re-checked E.118 (itu.int rec page and PDF, 2026-11-21 UTC): the text is still not reachable. Implemented: `89`, Luhn, 6 to 20 digits. 20 is from ETSI (BCD "padded with 'F'", so shorter numbers exist). Neither source gives a minimum; 6 is **derived, not quoted**, from the circular's structure: MII `89`, country code, issuer, individual account (at least one digit each) and the check digit. The first 7 digits are returned raw as `issuerIdentifier()` (the IIN maximum). Consequence: short `89`-prefixed numbers also fit `Imei`, `Imsi` and `Imeisv`, and the factory reports them ambiguous.

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
  (5x1) + (4x0) + (3x7) + (2x0) + (1x7) = 33, 33/10 = 3 + 3/10, R = 3. The weight is 1 for the digit next to the check digit, increasing leftwards.

### EcNumber

- ECHA, https://echa.europa.eu/about-ec-and-list-numbers (archive copy): "EC and list numbers historically share the same format of six numerical characters and one calculated check digit."
  "The check digit for the EC or list number is calculated using the ISBN method in both numerical and alphanumerical format." `NNN-NNN-R`, `R=(N1+2N2+3N3+4N4+5N5+6N6) mod 11`; letters A=10 ... Z=35 (I, L, O not
  used in list numbers). Sources: "2xx-xxx-x" and "3xx-xxx-x" EINECS, "4xx-xxx-x" ELINCS, "5xx-xxx-x" NLP; list numbers assigned by ECHA start with 1, 6, 7, 8, 9 or a letter. Examples `100-000-1`, `A00-001-5`
  (10 + 6 = 16, 16 mod 11 = 5). ECHA does not say what happens when R would be 10 (such numbers are simply not issued); keep refusing it.

### UnNumber, HsCode, TariffCode

- UNECE Model Regulations (Orange Book) Rev. 24, https://unece.org/sites/default/files/2025-09/ST_SG_AC10_1_Rev24e_Vol_II_1.pdf: "Goods considered to be dangerous for transport are listed in the Dangerous Goods
  List and assigned a name and a four-digit UN number"; "The UN number preceded by the letters "UN"" on documents. No check exists. No substance list embedded.
- WCO HS, https://www.wcoomd.org/en/topics/nomenclature/overview/harmonized_system_faq.aspx and the HS Convention: "The first two digits of a heading identify its chapter. The last two give the heading's
  position within that chapter"; headings are four digits, subheadings six; "A zero in a digit position means that level was not subdivided"; parties "must use the six-digit codes of the Harmonized System, or a more
  detailed national extension of them". The nomenclature itself is under WCO licence: not embedded.
- National extensions: EU Commission, Combined Nomenclature page https://taxation-customs.ec.europa.eu/customs/common-customs-tariff-cct/tariff-classification-goods/combined-nomenclature_en: CN code is 8 digits, the
  first six digits are the HS; TARIC adds the 9th and 10th digits (Regulation 2658/87 art. 3 in its 1987 text speaks of "10th and 11th digits" and "In the absence of a Community subdivision, the 10th and 11th digits shall be
  '00'": the current numbering was not reconciled with it; **not confirmed**). US: USITC HTS external user guide, https://www.usitc.gov/documents/hts/hts_external_user_guide.pdf: 8-digit heading/subheading plus a 2-digit
  statistical suffix gives the 10-digit HTS number. Format only: 8 or 10 digits.

### EnergyIdentificationCode (ENTSO-E)

- ENTSO-E, EIC Data Exchange Implementation Guide v1.2, https://eepublicdownloads.entsoe.eu/clean-documents/EDI/Library/cim_based/EIC_Data_Exchange_IG_v1.2.pdf, section "The ENTSO-E check character algorithm": "EIC
  codes are based on a 16-character alphanumeric code. The last character of the coding scheme is the check character that is calculated from the other characters using the ENTSO-E algorithm."
  Steps: replace characters by values (0-9 as is, A=10 ... Z=35, "-" = 36); weights 16 down to 2 for the 15 characters; sum the products; "(36 - MOD ((2107-1), 37))" (the example sum is 2107); the
  result is the check character, a value of 10 or more is converted back to a letter "using the same mechanism"; "If the check character generated is the "-" character (result of the calculation equal to
  36), one of the characters in the proposed EIC code shall be changed". Worked example: `11XRWENET12345-` gives `11XRWENET12345-2`. Also `11Y123456789012T` is given as an example of an area code.
  Structure (ENTSOG EIC manual page https://entsog.eu/eic-codes/manual-of-procedures, through search): 2-character local issuing office, 1 object-type character, 12 characters (A-Z, 0-9, "-"), check character.
  **Terms:** "The algorithm deriving from this document may only be used for the purpose of checking the validity of an allocated EIC code, unless used by an authorised LIO when allocating EIC codes. Any
  other use of the ENTSO-E algorithm is expressly prohibited." (ruling 5). The object-type letters (X, Y, Z, W, V, T, A) were not read from the Reference Manual: `objectType(): string` unless Task 5 reaches that text (ruling 9); `EicObjectType` needs that text (Task 5).

### Mpan

- SSEN (licensed distribution network operator), https://ssen.co.uk/globalassets/what-is-a-mpan/mpan-process-for-demand-customers.pdf: the MPAN "is a unique 13-digit reference"; the core is the 13 digits; "Distributor ID: Informs
  you which Distribution ..."; "Check Digit: Validates the previous 12 digits". Elexon (BSC Guidance Note, found by search; page not readable here) calls an MPAN not conforming to modulus 11 invalid. The weights
  (primes 3 5 7 13 17 19 23 29 31 37 41 43, sum mod 11 mod 10) appear in Wikipedia and its mirrors only; the MRA Data Transfer Catalogue and DCUSA text were **not reached**. **Outcome: format only** (13 digits, 21 with the top
  line); no helper built (ruling 9). The 21-digit layout (profile class 2, MTC 3, LLFC 3 on the top line) is the plan's and was not confirmed from a reached text.

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
| `AirWaybillCheckDigit` | Australian Border Force | AirWaybillNumber |
| `CasCheckDigit` | CAS | CasRegistryNumber |
| `EcNumberCheckDigit` | ECHA | EcNumber |
| `EicCheckDigit` (`isValid()` only, ruling 5) | ENTSO-E | EnergyIdentificationCode |

Not built (no official source, rulings 2, 3, 6, 9): IMO check, ISWC check, container check, MPAN check, Wmi tables. ISBN-10 and ISSN share `Mod11CheckDigit` (one algorithm, two lengths).
