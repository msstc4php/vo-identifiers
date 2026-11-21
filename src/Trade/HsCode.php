<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Trade;

use MSSTC4PHP\ValueObject\Identifiers\Internal\CommodityRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * Harmonized System code: six digits, the first two the chapter, the first four the heading. Format only: the
 * nomenclature is under WCO licence and is not embedded, so a well-formed code is never checked against it.
 *
 * @see https://www.wcoomd.org/en/topics/nomenclature/overview/harmonized_system_faq.aspx WCO, Harmonized System FAQ
 * @see docs/sources/IDENTIFIERS.md "UnNumber, HsCode, TariffCode"
 */
final class HsCode extends AbstractStringValueObject
{
    private const string TYPE = 'HS code';

    public function chapter(): string
    {
        return \substr($this->value, 0, 2);
    }

    public function heading(): string
    {
        return \substr($this->value, 0, 4);
    }

    public function subheading(): string
    {
        return $this->value;
    }

    #[Override]
    protected function parse(string $value): string
    {
        $value = CommodityRules::compact($value);

        if (\preg_match('/^[0-9]{6}$/D', $value) !== 1) {
            throw CommodityRules::trade(self::TYPE);
        }

        return $value;
    }
}
