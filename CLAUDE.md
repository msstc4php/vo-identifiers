# CLAUDE.md

Guidance for Claude Code in this repository. Deep references live under `.claude/docs/`.

## What this is

`msstc4php/vo-identifiers`: value objects for publication and media identifiers (ISBN, ISSN, ISMN, ISRC, ISWC, ISAN, ISNI,
ORCID, DOI, ROR, ARK), transport and logistics identifiers (VIN, WMI, container number, IMO number, MMSI, S10, air waybill,
UIC vehicle number, UN/LOCODE, ISO/IEC 15459 and MH10.8.2 data identifiers), telecom identities (IMEI, IMEISV, ICCID, IMSI,
EID) and chemistry, trade and energy identifiers (CAS, EC, UN number, HS, EIC, MPAN). PHP >= 8.4. Namespace
`MSSTC4PHP\ValueObject\Identifiers\`. Framework-agnostic; Doctrine storage is the separate `vo-identifiers-doctrine` bridge.
Requirements and the plan live in `../vo-base/docs/` (plan `2026-11-21-001-feat-identifiers-wave-plan.md`); the source notes, the
authority for every rule, are in `docs/sources/IDENTIFIERS.md`. Depends on `msstc4php/vo-base ^1.4` and `msstc4php/vo-gs1 ^1.0` (the GTIN form of
ISBN, ISMN and ISSN). Stable since 1.0.0; 2.0.0 (unreleased on main) is the current major: the README "Backward compatibility" section is binding for 2.x.

## Common commands

Local install against the sibling `../vo-base` and `../vo-gs1`: `COMPOSER=composer-local.json composer update
--ignore-platform-req=php`. In CI the install is `composer-ci.json`.

`composer-local.json` is gitignored. Create it from `composer-ci.json`: copy the file and replace the VCS repositories
with `{"type": "path", "url": "../vo-base", "options": {"symlink": true}}` and the same for `../vo-gs1`, and require
`msstc4php/vo-base` as `dev-main as 1.4.0` and `msstc4php/vo-gs1` as `dev-main as 1.0.0`.

- `COMPOSER=composer-local.json make check`, `make test`, `make infection` (`php -d memory_limit=-1` if the reporter
  runs out of memory), `make fix`.

`composer-ci.lock` (written by `make install-ci`) is not ignored, because `.gitignore` is a byte-identical template;
delete it after a CI-profile install and never commit it.

## Standard

Tool configuration is byte-identical to the `php84` templates of `msstc4symfony/bundle-standard`; CI's `templates` job
enforces it. Do not edit `.php-cs-fixer.dist.php`, `rector.php`, `phpstan*.neon`, `phpunit.xml.dist`,
`infection.json5`, `Makefile` or `.gitignore` here. The hex layout of that standard does not apply.

A check digit or structural rule is enforced only when `docs/sources/IDENTIFIERS.md` quotes an official source for it; no
bcmath (digit-by-digit integer arithmetic). Exception messages never contain the input or any part of it, not even its
length; never call `InvalidValueObjectException::excerpt()`. Registry tables decode and never refuse; licensed or
copyright-restricted tables are not embedded (the table terms are in `docs/sources/IDENTIFIERS.md`).

## Gotchas

- `Imei::equals()` (14/15 digits), `Isbn::equals()` (ISBN-10/13) and `Isan::equals()` (check characters; since 2.0 a `0000-0000` version equals none) compare across forms; the stored string keeps the form given, so a
  string-keyed index or UNIQUE column sees two values.
- 2.x (same rules as 1.x): never refuse a value an earlier 2.x accepted (a newly sourced check is a `hasValid…(): bool`), never change a
  canonical form, `MAX_LENGTH` or a factory's candidate set; new reasons go into `Internal\Reason`.
- Every string value object is validated again on unserialize by vo-base `^1.3` (`parse()` runs again); a composite
  one defines `__serialize()`/`__unserialize()` itself, as `Transport\DataIdentifierField` does. A new class must be added to `Tests\Unit\IdentifierContractTest` (with `MAX_LENGTH` when its length is bounded). Messages use `Internal\Reason`.
- The embedded AIM IAC register (edition 2026-06-16) is prefix-free by inspection; the test asserting it is updated together with the register.
- Controller rulings and every source decision: `docs/sources/IDENTIFIERS.md`; format-only and not-built lists with reasons: README.
