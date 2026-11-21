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
- A class that caches state derived in `parse()` must use `Internal\ReparsesOnUnserialize`: vo-base does not re-run `parse()` on
  unserialize, so without the trait a tampered payload restores derived state apart from the validated value. The trait serializes `value`
  alone and re-parses it (other keys ignored, re-entry on a built object refused). Today only `DataIdentifierString` needs it.
- Controller rulings (`docs/sources/IDENTIFIERS.md`, "Controller rulings") override the plan; the README "Format only" and "Not built" lists
  repeat them with the reason. Add a ruling there before changing a rule.
