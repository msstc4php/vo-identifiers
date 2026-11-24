<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Energy;

use MSSTC4PHP\ValueObject\Identifiers\Internal\CommodityRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * Meter Point Administration Number (Great Britain): 13 digits (the core) or 21 digits (the core with a top line in
 * front). The core is a two-digit distributor identifier, a ten-digit unique reference number and a check digit (Elexon
 * BSC guidance); the check weights were not reached, so the check digit is not verified, and the widths of the top-line
 * fields are not quoted, so the top line is not decoded.
 *
 * @see https://bscdocs.elexon.co.uk/guidance-notes/msid-mpan-guidance Elexon, BSC Guidance Note "MSID and MPAN Guidance" V4.0
 * @see https://ssen.co.uk/globalassets/what-is-a-mpan/mpan-process-for-demand-customers.pdf SSEN, MPAN process for demand customers
 * @see https://github.com/msstc4php/vo-identifiers/blob/main/docs/sources/IDENTIFIERS.md "Mpan"
 */
final class Mpan extends AbstractStringValueObject
{
    /** Characters of the longest stored form. */
    public const int MAX_LENGTH = 21;

    private const string TYPE = 'MPAN';

    /**
     * The 13-digit MPAN core: the whole value, or the last 13 digits of the 21-digit form.
     */
    public function core(): string
    {
        return \substr($this->value, -13);
    }

    public function distributorId(): string
    {
        return \substr($this->core(), 0, 2);
    }

    public function uniqueReference(): string
    {
        return \substr($this->core(), 2, 10);
    }

    #[Override]
    protected function parse(string $value): string
    {
        if (\preg_match('/^(?:[0-9]{13}|[0-9]{21})$/D', $value) !== 1) {
            throw CommodityRules::energy(self::TYPE);
        }

        return $value;
    }
}
