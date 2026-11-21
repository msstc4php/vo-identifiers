<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Internal;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidEnergyIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidSubstanceIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTradeIdentifierException;

/**
 * Messages name the identifier and the reason, never the input or any part of it.
 *
 * @internal
 */
final class CommodityRules
{
    /**
     * @param non-empty-string $type
     */
    public static function substance(string $type, string $reason = Reason::INVALID_FORMAT): InvalidSubstanceIdentifierException
    {
        return new InvalidSubstanceIdentifierException(\sprintf('%s %s.', $type, $reason));
    }

    /**
     * @param non-empty-string $type
     */
    public static function trade(string $type, string $reason = Reason::INVALID_FORMAT): InvalidTradeIdentifierException
    {
        return new InvalidTradeIdentifierException(\sprintf('%s %s.', $type, $reason));
    }

    /**
     * @param non-empty-string $type
     */
    public static function energy(string $type, string $reason = Reason::INVALID_FORMAT): InvalidEnergyIdentifierException
    {
        return new InvalidEnergyIdentifierException(\sprintf('%s %s.', $type, $reason));
    }
}
