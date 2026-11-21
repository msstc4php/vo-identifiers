# Integrations

- `msstc4php/vo-base ^1.2` (`dev-main as 1.2.0` in `composer-local.json`) and `msstc4php/vo-gs1 ^0.1` (tag v0.1.0, `dev-main as
  0.1.0` in `composer-local.json`); both reached through their GitHub repositories in `composer-ci.json`.
- Bridge: `msstc4php/vo-identifiers-doctrine` (types `id_<snake_name>`).
- Source documents: WebFetch for most hosts; PDFs come back as saved binaries (read with `pdftotext`); the Internet Archive is
  reachable with `curl` (allowed domain) and is the way to read pages that answer 403 to WebFetch (ORCID, ECHA, ABF, the old ISBN manual).
