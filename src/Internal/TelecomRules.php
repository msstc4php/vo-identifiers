<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Internal;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTelecomIdentifierException;

/**
 * Messages name the identifier and the reason, never the input or any part of it.
 *
 * @internal
 */
final class TelecomRules
{
    /**
     * @param non-empty-string $type
     */
    public static function invalidFormat(string $type): InvalidTelecomIdentifierException
    {
        return new InvalidTelecomIdentifierException(\sprintf('%s has an invalid format.', $type));
    }

    /**
     * @param non-empty-string $type
     */
    public static function invalidCheckDigit(string $type): InvalidTelecomIdentifierException
    {
        return new InvalidTelecomIdentifierException(\sprintf('%s has an invalid check digit.', $type));
    }

    /**
     * Keeps the first `$head` and the last `$tail` characters and masks the rest with asterisks; at least one character
     * is always masked.
     *
     * @param int<0, max> $head
     * @param int<0, max> $tail
     *
     * @return non-empty-string
     */
    public static function mask(string $value, int $head, int $tail): string
    {
        $length = \strlen($value);
        $head = \max(0, \min($head, $length - 1));
        $tail = \max(0, \min($tail, $length - $head - 1));

        return \substr($value, 0, $head) . '*' . \str_repeat('*', \max(0, $length - $head - $tail - 1)) . \substr($value, $length - $tail);
    }
}
