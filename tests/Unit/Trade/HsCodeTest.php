<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Trade;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTradeIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\CommodityRules;
use MSSTC4PHP\ValueObject\Identifiers\Trade\HsCode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(HsCode::class)]
#[UsesClass(CommodityRules::class)]
final class HsCodeTest extends TestCase
{
    public function testDecodesChapterHeadingAndSubheading(): void
    {
        $hs = new HsCode('010121');

        self::assertSame('01', $hs->chapter());
        self::assertSame('0101', $hs->heading());
        self::assertSame('010121', $hs->subheading());
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function printed(): iterable
    {
        // WCO FAQ prints `0103.10`, the Commission's CN page prints `1806 10`.
        yield 'WCO dotted' => ['0103.10', '010310'];
        yield 'EU spaced' => ['1806 10', '180610'];
        yield 'unbroken' => ['180610', '180610'];
    }

    #[DataProvider('printed')]
    public function testStripsTheSeparatorsTheSourcesPrint(string $input, string $expected): void
    {
        self::assertSame($expected, (new HsCode($input))->value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalid(): iterable
    {
        yield 'five digits' => ['01012'];
        yield 'seven digits' => ['0101210'];
        yield 'letters' => ['01012A'];
        yield 'dot in the wrong place' => ['01.0121'];
        yield 'space in the wrong place' => ['01 0121'];
        yield 'mixed' => ['0101.2 1'];
        yield 'newline' => ["010121\n"];
        yield 'empty' => [''];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value): void
    {
        try {
            new HsCode($value);
            self::fail('Expected an exception.');
        } catch (InvalidTradeIdentifierException $e) {
            self::assertSame('HS code has an invalid format.', $e->getMessage());
        }
    }
}
