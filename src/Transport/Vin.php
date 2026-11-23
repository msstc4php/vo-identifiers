<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Transport;

use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Identifiers\Internal\VinCheckDigit;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * Vehicle Identification Number (ISO 3779): 17 digits and capital letters without `I`, `O` and `Q`; lowercase is
 * uppercased. Position 9 is never a reason to refuse: 49 CFR 565.15 binds vehicles by the market they are sold in, which
 * a VIN does not carry, so {@see hasValidCheckDigit()} only reports. VINs before 1981 are not 17 characters and are out of scope.
 *
 * @see https://www.law.cornell.edu/cfr/text/49/565.13 49 CFR 565.13(g), the character set
 * @see https://www.law.cornell.edu/cfr/text/49/565.15 49 CFR 565.15(c), (d), the check digit and the model year
 * @see docs/sources/IDENTIFIERS.md "VIN (ISO 3779) and WMI (ISO 3780)"
 */
final class Vin extends AbstractStringValueObject
{
    /** Characters of the longest stored form. */
    public const int MAX_LENGTH = 17;

    private const string TYPE = 'VIN';

    private const string LETTER_YEARS = 'ABCDEFGHJKLMNPRSTVWXY';

    private const string DIGIT_YEARS = '123456789';

    public function wmi(): Wmi
    {
        return new Wmi(\substr($this->value, 0, 3));
    }

    /**
     * Vehicle descriptor section, positions 4 to 9.
     */
    public function vds(): string
    {
        return \substr($this->value, 3, 6);
    }

    /**
     * Vehicle indicator section, positions 10 to 17.
     */
    public function vis(): string
    {
        return \substr($this->value, 9);
    }

    /**
     * Whether position 9 is the check digit of the other sixteen characters (49 CFR 565.15(c)).
     */
    public function hasValidCheckDigit(): bool
    {
        return VinCheckDigit::isValid($this->value);
    }

    /**
     * The model years the position 10 code stands for (49 CFR 565.15(d), Table VII), 30 years apart; empty when the code
     * is `0`, `U` or `Z`. Position 7 is not used to pick one: the table's note covers only some vehicle classes.
     *
     * @return list<int>
     */
    public function modelYears(): array
    {
        $code = $this->value[9];
        $position = \strpos(self::LETTER_YEARS, $code);

        if ($position !== false) {
            return [1980 + $position, 2010 + $position];
        }

        $position = \strpos(self::DIGIT_YEARS, $code);

        if ($position !== false) {
            return [2001 + $position, 2031 + $position];
        }

        return [];
    }

    #[Override]
    protected function parse(string $value): string
    {
        $value = \strtoupper($value);

        if (\preg_match('/^[0-9A-HJ-NPR-Z]{17}$/D', $value) !== 1) {
            throw TransportRules::invalidFormat(self::TYPE);
        }

        return $value;
    }
}
