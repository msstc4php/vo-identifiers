<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Energy;

use MSSTC4PHP\ValueObject\Identifiers\Internal\CommodityRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * Meter Point Administration Number (Great Britain): the 13-digit core (two-digit distributor ID, eight-digit unique
 * identifier, two digits and a check digit), optionally with the 8 digits of the top line in front (21 in all). Format
 * only: no reached text gives the check weights or the top-line layout, so neither is verified or decoded. Spaces are
 * dropped.
 *
 * @see https://ssen.co.uk/globalassets/what-is-a-mpan/mpan-process-for-demand-customers.pdf SSEN, MPAN process for demand customers
 * @see docs/sources/IDENTIFIERS.md "Mpan"
 */
final class Mpan extends AbstractStringValueObject
{
    private const string TYPE = 'MPAN';

    public function core(): string
    {
        return \substr($this->value, -13);
    }

    public function distributorId(): string
    {
        return \substr($this->core(), 0, 2);
    }

    public function hasTopLine(): bool
    {
        return \strlen($this->value) === 21;
    }

    #[Override]
    protected function parse(string $value): string
    {
        $value = CommodityRules::compact($value);

        if (\preg_match('/^(?:[0-9]{13}|[0-9]{21})$/D', $value) !== 1) {
            throw CommodityRules::energy(self::TYPE);
        }

        return $value;
    }
}
