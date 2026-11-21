# msstc4php/vo-identifiers

Value objects for publication and media identifiers, transport and logistics identifiers, telecom identities and chemistry,
trade and energy identifiers. Built on [`msstc4php/vo-base`](https://github.com/msstc4php/vo-base) and
[`msstc4php/vo-gs1`](https://github.com/msstc4php/vo-gs1); PHP >= 8.4.

Every check digit and structural rule cites its official source; the notes, with quotes, are in
[`docs/sources/IDENTIFIERS.md`](docs/sources/IDENTIFIERS.md). What a class cannot check from a published source is checked for
format only.

## Status

Scaffold: the source notes and the check-digit helpers (`src/Internal/`) exist; the value object classes follow.

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
