<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Energy;

use MSSTC4PHP\ValueObject\Identifiers\Internal\CommodityRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * Meter Point Administration Number (Great Britain): 13 digits (the core) or 21 digits (the core with a top line in
 * front). Format only: no reached text gives the layout or the check weights of either form, so neither form is decoded
 * and the check digit is not verified.
 *
 * @see https://ssen.co.uk/globalassets/what-is-a-mpan/mpan-process-for-demand-customers.pdf SSEN, MPAN process for demand customers
 * @see docs/sources/IDENTIFIERS.md "Mpan"
 */
final class Mpan extends AbstractStringValueObject
{
    /** Characters of the longest stored form. */
    public const int MAX_LENGTH = 21;

    private const string TYPE = 'MPAN';

    #[Override]
    protected function parse(string $value): string
    {
        if (\preg_match('/^(?:[0-9]{13}|[0-9]{21})$/D', $value) !== 1) {
            throw CommodityRules::energy(self::TYPE);
        }

        return $value;
    }
}
