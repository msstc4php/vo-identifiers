<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Trade;

use MSSTC4PHP\ValueObject\Identifiers\Internal\CommodityRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * Harmonized System code: six digits, the first two the chapter, the first four the heading. Written as printed by the WCO (`0103.10`), by the EU (`1806 10`) or unbroken. Format only: the
 * nomenclature is under WCO licence and is not embedded, so a well-formed code is never checked against it.
 *
 * @see https://www.wcoomd.org/en/topics/nomenclature/overview/harmonized_system_faq.aspx WCO, Harmonized System FAQ
 * @see docs/sources/IDENTIFIERS.md "UnNumber, HsCode, TariffCode"
 */
final class HsCode extends AbstractStringValueObject
{
    /** Characters of the longest stored form. */
    public const int MAX_LENGTH = 6;

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
        if (\preg_match('/^(?:[0-9]{6}|[0-9]{4}[. ][0-9]{2})$/D', $value) !== 1) {
            throw CommodityRules::trade(self::TYPE);
        }

        return \str_replace(['.', ' '], '', $value);
    }
}
