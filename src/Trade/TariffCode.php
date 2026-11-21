<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Trade;

use MSSTC4PHP\ValueObject\Identifiers\Internal\CommodityRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * Tariff code of two national extensions of the Harmonized System: the EU (8-digit CN, 10-digit TARIC) and the US (8-digit
 * HTS subheading, 10-digit HTS number). Other national lengths are not quoted and are refused. Accepted unbroken, with
 * spaces as the EU prints them (`1806 10 15`) or with dots as the USITC prints them (`9801.00.10.10`). The first six
 * digits are the HS subheading. Format only.
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
        $pattern = '/^(?:[0-9]{8}(?:[0-9]{2})?|[0-9]{4}(?:\.[0-9]{2}){2}(?:\.[0-9]{2})?|[0-9]{4}(?: [0-9]{2}){2}(?: [0-9]{2})?)$/D';
        if (\preg_match($pattern, $value) !== 1) {
            throw CommodityRules::trade(self::TYPE);
        }

        return \str_replace(['.', ' '], '', $value);
    }
}
