<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Substance;

use MSSTC4PHP\ValueObject\Identifiers\Internal\CasCheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\CommodityRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * CAS Registry Number: 2 to 7 digits, 2 digits and a check digit, stored with its hyphens (`7732-18-5`). The hyphens are
 * optional on input.
 *
 * @see https://www.cas.org/support/documentation/chemical-substances/checkdig CAS, "Check Digit Verification of CAS Registry Numbers"
 * @see docs/sources/IDENTIFIERS.md "CasRegistryNumber"
 */
final class CasRegistryNumber extends AbstractStringValueObject
{
    private const string TYPE = 'CAS Registry Number';

    public function checkDigit(): string
    {
        return \substr($this->value, -1);
    }

    #[Override]
    protected function parse(string $value): string
    {
        $value = CommodityRules::compact($value, ' ');

        if (\preg_match('/^([0-9]{2,7})-?([0-9]{2})-?([0-9])$/D', $value, $match) !== 1) {
            throw CommodityRules::substance(self::TYPE);
        }

        if (!CasCheckDigit::isValid($match[1] . $match[2] . $match[3])) {
            throw CommodityRules::substance(self::TYPE, 'has an invalid check digit');
        }

        return $match[1] . '-' . $match[2] . '-' . $match[3];
    }
}
