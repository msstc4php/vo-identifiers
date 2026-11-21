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
        self::assertSame('010121', (new HsCode(' 010121 '))->value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalid(): iterable
    {
        yield 'five digits' => ['01012'];
        yield 'seven digits' => ['0101210'];
        yield 'letters' => ['01012A'];
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
