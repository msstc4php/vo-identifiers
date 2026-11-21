<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Internal;

use LogicException;
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
        yield 'tail kept at six masked' => ['1234567890', 2, 2, '12******90'];
        yield 'tail dropped at five masked' => ['123456789', 2, 2, '12*******'];
        yield 'nothing kept' => ['1234', 0, 0, '****'];
        yield 'head only' => ['12', 1, 0, '1*'];
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

    /**
     * @return iterable<string, array{string, int<0, max>}>
     */
    public static function headsThatLeaveNothingMasked(): iterable
    {
        yield 'head equals length' => ['1234', 4];
        yield 'head beyond length' => ['1234', 5];
        yield 'empty value' => ['', 0];
    }

    /**
     * @param int<0, max> $head
     */
    #[DataProvider('headsThatLeaveNothingMasked')]
    public function testAHeadThatLeavesNothingMaskedIsALogicError(string $value, int $head): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('A masked identifier keeps at least one character masked.');

        TelecomRules::mask($value, $head, 0);
    }
}
