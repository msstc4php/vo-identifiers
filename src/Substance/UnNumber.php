<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Substance;

use MSSTC4PHP\ValueObject\Identifiers\Internal\CommodityRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * UN number of a dangerous good: four digits, written with the letters `UN` in front. The prefix is optional on input;
 * the stored form is `UN` plus the digits. Format only: no check exists and no substance list is embedded.
 *
 * @see https://unece.org/sites/default/files/2025-09/ST_SG_AC10_1_Rev24e_Vol_II_1.pdf UN Model Regulations (Orange Book) Rev. 24
 * @see docs/sources/IDENTIFIERS.md "UnNumber, HsCode, TariffCode"
 */
final class UnNumber extends AbstractStringValueObject
{
    /** Characters of the longest stored form. */
    public const int MAX_LENGTH = 6;

    private const string TYPE = 'UN number';

    public function number(): string
    {
        return \substr($this->value, 2);
    }

    #[Override]
    protected function parse(string $value): string
    {
        $value = \strtoupper($value);

        if (\preg_match('/^(?:UN)?([0-9]{4})$/D', $value, $match) !== 1) {
            throw CommodityRules::substance(self::TYPE);
        }

        return 'UN' . $match[1];
    }
}
