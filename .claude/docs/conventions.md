# Conventions

- `src/Internal/*` helpers are `@internal final`, static `compute(string $body): string` and `isValid(string $value): bool`.
  `compute` throws `\LogicException` on a body outside the helper's alphabet or length; `isValid` returns false instead.
  Whatever the helper's input, it never carries a separator: classes strip hyphens and spaces first.
- `VinCheckDigit::compute` takes the sixteen characters without position 9; `isValid` takes the whole 17-character VIN.
- `EicCheckDigit` has a public `isValid()` only and no method that computes a check character: ENTSO-E allows the algorithm for checking allocated codes only (ruling 5).
- `RorCheckDigits` decodes the six Crockford base32 characters to an integer before MOD 97-10; `Iso7064Mod97` alone cannot check a ROR.
- Test vectors that no source prints (the Review Focus values) are labelled "not printed by the source".
- Tests use the source's own examples and name the source in a comment; a synthetic vector (a remainder that no source
  example reaches) is labelled as such. No test vector comes from Wikipedia or a library.
- Infection needs `#[UsesClass]` for helpers that call other helpers, or PHPUnit marks the tests risky (coverage metadata is strict).
- vo-base `^1.3` serializes a string value object as `value` alone and re-runs `parse()` on unserialize (other keys ignored, re-entry on a
  built object refused), so a class that caches state derived in `parse()` needs nothing more. The package's
  `Internal\ReparsesOnUnserialize` trait did this before 1.3 and was removed in 0.2.0. A composite defines `__serialize()` and
  `__unserialize()` itself (`Transport\DataIdentifierField`) with the base's messages.
- Controller rulings (`docs/sources/IDENTIFIERS.md`, "Controller rulings") override the plan; the README "Format only" and "Not built" lists
  repeat them with the reason. Add a ruling there before changing a rule.
