# Integrations

- `msstc4php/vo-base ^1.4` (`dev-main as 1.4.0` in `composer-local.json`) and `msstc4php/vo-gs1 ^1.0` (`dev-main as 1.0.0` in
  `composer-local.json` and `composer-ci.json`; release order vo-gs1 1.0 → vo-identifiers 1.0 → the bridge, and the lowest-deps
  CI job resolves only once vo-gs1 1.0.0 is tagged); both reached through their GitHub repositories in `composer-ci.json`.
- DOI Handbook chapter 3 is readable at `www-old.doi.org/doi_handbook/3_Resolution.html`; the DOI resolution factsheet on
  `www.doi.org` holds the mandatory URL encoding table. The DoD UID Guide (acq.osd.mil, its UNT and Internet Archive copies)
  and dla.mil manuals were not reachable on 2026-11-23 (UTC).
- Bridge: `msstc4php/vo-identifiers-doctrine` (types `id_<snake_name>`).
- Source documents: WebFetch for most hosts; PDFs come back as saved binaries (read with `pdftotext`); the Internet Archive is
  reachable with `curl` (allowed domain) and is the way to read pages that answer 403 to WebFetch (ORCID, ECHA, ABF, the old ISBN manual).
- Embedded table: AIM register of issuing agency codes for ISO/IEC 15459, edition 2026-06-16 (public PDF, no terms of use in it); decode-only.
  Tables not embedded and why: README "Registry and table updates" and `docs/sources/IDENTIFIERS.md` ("Table terms of use summary").
