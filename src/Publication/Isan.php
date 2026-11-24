<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Publication;

use MSSTC4PHP\ValueObject\Contract\ValueObject;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Iso7064Mod37_36;
use MSSTC4PHP\ValueObject\Identifiers\Internal\PublicationRules;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Reason;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * International Standard Audiovisual Number (ISO 15706): 12 hexadecimal digits of root and 4 of episode, optionally an
 * 8-digit version segment. The check characters (ISO 7064 MOD 37,36) are added to the printed form: the first after the
 * 16 digits, the second after the version, computed over the 16 digits and the version (not over the first check
 * character). A value without them (16 or 24 digits) is accepted; a check character that is present must be right, and a
 * version needs both or neither. Hyphens, spaces and a leading `ISAN ` are dropped; the stored form is compact and keeps
 * the form given, and {@see equals()} ignores the check characters. A bare 16-digit value is also a valid ISNI:
 * {@see PublicationIdentifierFactory} reports it ambiguous.
 *
 * @see https://isan.org/docs/isan_check_digit_calculation_v2.0.pdf ISAN International Agency, "ISAN Check Characters Calculation" v2.0
 * @see https://github.com/msstc4php/vo-identifiers/blob/main/docs/sources/IDENTIFIERS.md "ISAN (ISO 15706)"
 */
final class Isan extends AbstractStringValueObject
{
    /** Characters of the longest stored form. */
    public const int MAX_LENGTH = 26;

    private const string TYPE = 'ISAN';

    /**
     * Twelve hexadecimal digits.
     */
    public function root(): string
    {
        return \substr($this->value, 0, 12);
    }

    /**
     * Four hexadecimal digits.
     */
    public function episode(): string
    {
        return \substr($this->value, 12, 4);
    }

    /**
     * Eight hexadecimal digits.
     */
    public function version(): ?string
    {
        return match (\strlen($this->value)) {
            24 => \substr($this->value, 16, 8),
            26 => \substr($this->value, 17, 8),
            default => null,
        };
    }

    /**
     * Compares root, episode and version: the check characters are derived from them, so the forms with and without
     * check characters of one ISAN are equal (the stored string keeps the form given).
     */
    #[Override]
    public function equals(ValueObject $other): bool
    {
        return $other instanceof self
            && \substr($other->value, 0, 16) === \substr($this->value, 0, 16)
            && $other->version() === $this->version();
    }

    public function hasVersion(): bool
    {
        return \strlen($this->value) >= 24;
    }

    /**
     * A version segment from `F0000000` to `FFFFFFFF`, reserved for private numbers that are not registered (ISO
     * 15706-2:2023, 4.6). False without a version.
     */
    public function isPrivateVersion(): bool
    {
        return \str_starts_with($this->version() ?? '', 'F');
    }

    public function hasCheckCharacters(): bool
    {
        return \strlen($this->value) === 17 || \strlen($this->value) === 26;
    }

    /**
     * @return non-empty-string `ISAN XXXX-XXXX-XXXX-XXXX[-C]` and, with a version, `-XXXX-XXXX[-C]`; the check
     *                          characters appear when the value carries them
     */
    public function formatted(): string
    {
        $groups = \str_split(\substr($this->value, 0, 16), 4);
        $version = $this->version();
        $hasChecks = $this->hasCheckCharacters();
        if ($hasChecks) {
            $groups[] = $this->value[16];
        }

        if ($version !== null) {
            \array_push($groups, ...\str_split($version, 4));
            if ($hasChecks) {
                $groups[] = $this->value[25];
            }
        }

        return 'ISAN ' . \implode('-', $groups);
    }

    #[Override]
    protected function parse(string $value): string
    {
        $value = PublicationRules::compact($value, 'ISAN');

        $pattern = '/^([0-9A-F]{16})(?:([0-9A-Z])(?:([0-9A-F]{8})([0-9A-Z]))?|([0-9A-F]{8}))?$/D';
        if (\preg_match($pattern, $value, $parts) !== 1) {
            throw PublicationRules::invalidFormat(self::TYPE);
        }

        $first = $parts[2] ?? '';
        if ($first !== '' && Iso7064Mod37_36::compute($parts[1]) !== $first) {
            throw PublicationRules::fail(self::TYPE, Reason::WRONG_CHECK_CHARACTER);
        }

        $second = $parts[4] ?? '';
        if ($second !== '' && Iso7064Mod37_36::compute($parts[1] . ($parts[3] ?? '')) !== $second) {
            throw PublicationRules::fail(self::TYPE, Reason::WRONG_CHECK_CHARACTER);
        }

        return $value;
    }
}
