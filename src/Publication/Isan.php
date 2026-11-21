<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Publication;

use MSSTC4PHP\ValueObject\Identifiers\Internal\Iso7064Mod37_36;
use MSSTC4PHP\ValueObject\Identifiers\Internal\PublicationRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * International Standard Audiovisual Number (ISO 15706): 12 hexadecimal digits of root and 4 of episode, an ISO 7064
 * MOD 37,36 check character, and optionally an 8-digit version segment with a second check character computed over the
 * 16 digits and the version (not over the first check character). Both check characters are required; a value without
 * them is refused. Hyphens, spaces and a leading `ISAN ` are dropped; the stored form is compact.
 *
 * @see https://isan.org/docs/isan_check_digit_calculation_v2.0.pdf ISAN International Agency, "ISAN Check Characters Calculation" v2.0
 * @see docs/sources/IDENTIFIERS.md "ISAN (ISO 15706)"
 */
final class Isan extends AbstractStringValueObject
{
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
        return $this->hasVersion() ? \substr($this->value, 17, 8) : null;
    }

    public function hasVersion(): bool
    {
        return \strlen($this->value) === 26;
    }

    /**
     * @return non-empty-string `ISAN XXXX-XXXX-XXXX-XXXX-C` or, with a version, `...-C-XXXX-XXXX-C`
     */
    public function formatted(): string
    {
        $formatted = 'ISAN ' . \implode('-', \str_split(\substr($this->value, 0, 16), 4)) . '-' . $this->value[16];

        if ($this->hasVersion()) {
            $formatted .= '-' . \implode('-', \str_split(\substr($this->value, 17, 8), 4)) . '-' . $this->value[25];
        }

        return $formatted;
    }

    #[Override]
    protected function parse(string $value): string
    {
        $value = PublicationRules::compact($value, 'ISAN');

        if (\preg_match('/^([0-9A-F]{16})([0-9A-Z])(?:([0-9A-F]{8})([0-9A-Z]))?$/D', $value, $parts) !== 1) {
            throw PublicationRules::invalidFormat(self::TYPE);
        }

        if (Iso7064Mod37_36::compute($parts[1]) !== $parts[2]) {
            throw PublicationRules::fail(self::TYPE, 'has a wrong check character');
        }

        if (isset($parts[3], $parts[4]) && Iso7064Mod37_36::compute($parts[1] . $parts[3]) !== $parts[4]) {
            throw PublicationRules::fail(self::TYPE, 'has a wrong check character');
        }

        return $value;
    }
}
