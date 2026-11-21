<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Internal;

use LogicException;
use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTelecomIdentifierException;

/**
 * Messages name the identifier and the reason, never the input or any part of it.
 *
 * @internal
 */
final class TelecomRules
{
    private const int MIN_MASKED = 6;

    /**
     * @param non-empty-string $type
     */
    public static function invalidFormat(string $type): InvalidTelecomIdentifierException
    {
        return new InvalidTelecomIdentifierException(\sprintf('%s %s.', $type, Reason::INVALID_FORMAT));
    }

    /**
     * @param non-empty-string $type
     */
    public static function invalidCheckDigit(string $type): InvalidTelecomIdentifierException
    {
        return new InvalidTelecomIdentifierException(\sprintf('%s %s.', $type, Reason::WRONG_CHECK_DIGIT));
    }

    /**
     * Keeps the first `$head` characters and the last `$tail` ones only when at least six characters stay masked;
     * otherwise only the first `$head`. The rest are asterisks and the length is kept.
     *
     * @param int<0, max> $head
     * @param int<0, max> $tail
     *
     * @return non-empty-string
     *
     * @throws LogicException when `$head` leaves nothing to mask
     */
    public static function mask(string $value, int $head, int $tail): string
    {
        $length = \strlen($value);
        if ($head >= $length) {
            throw new LogicException('A masked identifier keeps at least one character masked.');
        }

        $keptTail = $length - $head - $tail >= self::MIN_MASKED ? $tail : 0;

        return \substr($value, 0, $head) . '*' . \str_repeat('*', $length - $head - $keptTail - 1) . \substr($value, $length - $keptTail);
    }
}
