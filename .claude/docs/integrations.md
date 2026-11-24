# Integrations

- `msstc4php/vo-base ^1.4` (`dev-main as 1.4.0` in `composer-local.json`) and `msstc4php/vo-gs1 ^1.0` (`dev-main as 1.0.0` in
  `composer-local.json` and `composer-ci.json`; release order vo-gs1 1.0 → vo-identifiers 1.0 → the bridge, and the lowest-deps
  CI job resolves only once vo-gs1 1.0.0 is tagged); both reached through their GitHub repositories in `composer-ci.json`.
- DOI Handbook 2025 is a PDF at `www.doi.org/doi-handbook/DOIHandbook_2025.pdf` (section numbers of the body differ from its table of
  contents: percent-encoding is 4.7 in the body, 3.7 in the TOC); the 2017 chapter 3 is at `www-old.doi.org/doi_handbook/3_Resolution.html`;
  the DOI resolution factsheet on `www.doi.org` holds the mandatory URL encoding table. The DoD UID Guide v2.5 is reachable as a UNT web-archive
  copy (`webarchive.library.unt.edu/web/20130223193613id_/http://www.acq.osd.mil/dpap/UID/attachments/DoDUIDGuideVer2_5.pdf`); acq.osd.mil and
  dla.mil were not (2026-11-23 UTC).
- IFPI ISRC: the Handbook 4th ed. is `isrc.ifpi.org/images/downloads/ISRC_Handbook.pdf` (pdftotext reads it); the agency code allocation list
  is `.../Valid-Characters-in-the-ISRC-Prefix.pdf` (edition October 2026, not embedded).
- Bridge: `msstc4php/vo-identifiers-doctrine` (types `id_<snake_name>`).
- Source documents: WebFetch for most hosts; PDFs come back as saved binaries (read with `pdftotext`); the Internet Archive is
  reachable with `curl` (allowed domain) and is the way to read pages that answer 403 to WebFetch (ORCID, ECHA, ABF, the old ISBN manual).
- Embedded table: AIM register of issuing agency codes for ISO/IEC 15459, edition 2026-06-16 (public PDF, no terms of use in it); decode-only.
  Tables not embedded and why: README "Registry and table updates" and `docs/sources/IDENTIFIERS.md` ("Table terms of use summary").
- 1.2 sources (2026-11-24 UTC), all fetched with `curl` and an `allowed_domains` entry, PDFs read with `pdftotext -layout`: iteh previews on
  `cdn.standards.iteh.ai` (ISO 3780:2009 FR with Annex A, ISO 15706-2:2023 EN with clause 4), ITU-T E.118 (09/2025) on `www.itu.int/rec/dologin_pub.asp`,
  IATA Cargo Agent's Handbook (Res. 600a, 606) on `www.iata.org/contentassets/...`, Elexon guidance on `bscdocs.elexon.co.uk` (the HTML embeds the
  text as JSON-escaped blocks), ISO/TC 46/SC 9 N 268 on `groups.niso.org`.
