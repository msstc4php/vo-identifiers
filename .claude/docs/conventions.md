# Conventions

- `src/Internal/*` helpers are `@internal final`, static `compute(string $body): string` and `isValid(string $value): bool`.
  `compute` throws `\LogicException` on a body outside the helper's alphabet or length; `isValid` returns false instead.
  Whatever the helper's input, it never carries a separator: classes strip hyphens and spaces first.
- `VinCheckDigit::compute` takes the sixteen characters without position 9; `isValid` takes the whole 17-character VIN.
- `EicCheckDigit` and its users expose validation only: ENTSO-E allows the algorithm for checking allocated codes only.
- Tests use the source's own examples and name the source in a comment; a synthetic vector (a remainder that no source
  example reaches) is labelled as such. No test vector comes from Wikipedia or a library.
- Infection needs `#[UsesClass]` for helpers that call other helpers, or PHPUnit marks the tests risky (coverage metadata is strict).
