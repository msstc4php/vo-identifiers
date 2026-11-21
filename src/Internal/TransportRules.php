<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Internal;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTransportIdentifierException;

/**
 * Messages name the identifier and the reason, never the input or any part of it.
 *
 * @internal
 */
final class TransportRules
{
    /**
     * @param non-empty-string $type
     */
    public static function fail(string $type, string $reason): InvalidTransportIdentifierException
    {
        return new InvalidTransportIdentifierException(\sprintf('%s %s.', $type, $reason));
    }

    /**
     * @param non-empty-string $type
     */
    public static function invalidFormat(string $type): InvalidTransportIdentifierException
    {
        return self::fail($type, 'has an invalid format');
    }

    /**
     * Drops the separators the source prints and uppercases.
     *
     * @param non-empty-string $separators each character is a separator
     */
    public static function compact(string $value, string $separators = ' '): string
    {
        return \strtoupper(\str_replace(\str_split($separators), '', $value));
    }
}
