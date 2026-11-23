<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Transport;

use MSSTC4PHP\ValueObject\Identifiers\Internal\Luhn;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Reason;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * Railway vehicle number (UIC 438, the European wagon number): twelve digits, the last being the Luhn check digit of the
 * basic number before it. Accepted printed (`31 51 3320 198-0`) or compact, stored compact. The vehicle type and
 * country digits are returned raw: the UIC code tables were not read.
 *
 * @see https://legislation.gov.uk/eudn/2007/756/annex/appendix/6/part/3/2020-12-31 Commission Decision 2007/756/EC, Appendix 6 part 3
 * @see https://github.com/msstc4php/vo-identifiers/blob/main/docs/sources/IDENTIFIERS.md "UicVehicleNumber"
 */
final class UicVehicleNumber extends AbstractStringValueObject
{
    /** Characters of the longest stored form. */
    public const int MAX_LENGTH = 12;

    private const string TYPE = 'UIC vehicle number';

    /**
     * The first two digits.
     */
    public function vehicleType(): string
    {
        return \substr($this->value, 0, 2);
    }

    /**
     * The third and fourth digits, the UIC numeric country code (not an ISO 3166 code).
     */
    public function uicCountryCode(): string
    {
        return \substr($this->value, 2, 2);
    }

    public function checkDigit(): string
    {
        return $this->value[11];
    }

    /**
     * @return non-empty-string `31 51 3320 198-0`
     */
    public function formatted(): string
    {
        return \sprintf(
            '%s %s %s %s-%s',
            $this->vehicleType(),
            $this->uicCountryCode(),
            \substr($this->value, 4, 4),
            \substr($this->value, 8, 3),
            $this->checkDigit(),
        );
    }

    #[Override]
    protected function parse(string $value): string
    {
        $value = TransportRules::compact($value, '- ');

        if (\preg_match('/^[0-9]{12}$/D', $value) !== 1) {
            throw TransportRules::invalidFormat(self::TYPE);
        }

        if (!Luhn::isValid($value)) {
            throw TransportRules::fail(self::TYPE, Reason::WRONG_CHECK_DIGIT);
        }

        return $value;
    }
}
