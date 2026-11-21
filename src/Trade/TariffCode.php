<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Trade;

use MSSTC4PHP\ValueObject\Identifiers\Internal\CommodityRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * National extension of the Harmonized System: 8 digits (EU Combined Nomenclature, the HTS heading and subheading) or
 * 10 digits (EU TARIC, US HTS number). The first six digits are the HS subheading. Format only.
 *
 * @see https://legislation.gov.uk/eur/1987/2658/pdfs/eur_19872658_2016-06-01_en.pdf Regulation (EEC) No 2658/87, Article 3
 * @see https://www.usitc.gov/documents/hts/hts_external_user_guide.pdf USITC, HTS external user guide
 * @see docs/sources/IDENTIFIERS.md "UnNumber, HsCode, TariffCode"
 */
final class TariffCode extends AbstractStringValueObject
{
    private const string TYPE = 'tariff code';

    public function hsSubheading(): HsCode
    {
        return new HsCode(\substr($this->value, 0, 6));
    }

    #[Override]
    protected function parse(string $value): string
    {
        $value = CommodityRules::compact($value);

        if (\preg_match('/^(?:[0-9]{8}|[0-9]{10})$/D', $value) !== 1) {
            throw CommodityRules::trade(self::TYPE);
        }

        return $value;
    }
}
