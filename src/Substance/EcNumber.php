<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Substance;

use MSSTC4PHP\ValueObject\Identifiers\Internal\CommodityRules;
use MSSTC4PHP\ValueObject\Identifiers\Internal\EcNumberCheckDigit;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * ECHA EC or list number: six digits (list numbers may carry capital letters other than I, L, O) and a check digit,
 * stored as `NNN-NNN-N`. The hyphens are optional on input.
 *
 * @see https://echa.europa.eu/about-ec-and-list-numbers ECHA, "About EC and list numbers"
 * @see docs/sources/IDENTIFIERS.md "EcNumber"
 */
final class EcNumber extends AbstractStringValueObject
{
    private const string TYPE = 'EC number';

    public function checkDigit(): string
    {
        return \substr($this->value, -1);
    }

    #[Override]
    protected function parse(string $value): string
    {
        $value = CommodityRules::compact($value, ' ');

        if (\preg_match('/^([0-9A-HJKMNP-Z]{3})-?([0-9A-HJKMNP-Z]{3})-?([0-9])$/D', $value, $match) !== 1) {
            throw CommodityRules::substance(self::TYPE);
        }

        if (!EcNumberCheckDigit::isValid($match[1] . $match[2] . $match[3])) {
            throw CommodityRules::substance(self::TYPE, 'has an invalid check digit');
        }

        return $match[1] . '-' . $match[2] . '-' . $match[3];
    }
}
