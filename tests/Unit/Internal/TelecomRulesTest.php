<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Internal;

use MSSTC4PHP\ValueObject\Identifiers\Internal\TelecomRules;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(TelecomRules::class)]
final class TelecomRulesTest extends TestCase
{
    /**
     * @return iterable<string, array{string, int<0, max>, int<0, max>, string}>
     */
    public static function masks(): iterable
    {
        yield 'long' => ['1234567890', 3, 2, '123*****90'];
        yield 'tail shrinks first' => ['123456', 5, 2, '12345*'];
        yield 'both fit' => ['1234567', 5, 2, '12345*7'];
        yield 'nothing kept' => ['1234', 0, 0, '****'];
        yield 'single' => ['1', 5, 2, '*'];
        yield 'empty' => ['', 5, 2, '*'];
    }

    /**
     * @param int<0, max> $head
     * @param int<0, max> $tail
     */
    #[DataProvider('masks')]
    public function testMask(string $value, int $head, int $tail, string $expected): void
    {
        self::assertSame($expected, TelecomRules::mask($value, $head, $tail));
    }
}
