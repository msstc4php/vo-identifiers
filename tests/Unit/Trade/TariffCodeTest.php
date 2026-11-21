<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Trade;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTradeIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\CommodityRules;
use MSSTC4PHP\ValueObject\Identifiers\Trade\HsCode;
use MSSTC4PHP\ValueObject\Identifiers\Trade\TariffCode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TariffCode::class)]
#[UsesClass(CommodityRules::class)]
#[UsesClass(HsCode::class)]
final class TariffCodeTest extends TestCase
{
    /**
     * @return iterable<string, array{string}>
     */
    public static function valid(): iterable
    {
        yield 'CN, 8 digits' => ['01012100'];
        yield 'TARIC or HTS, 10 digits' => ['0101210010'];
        // Commission CN page: `1806 10 15`; USITC search pages: `9801.00.10.10`.
        yield 'CN spaced' => ['0101 21 00'];
        yield 'TARIC spaced' => ['0101 21 00 10'];
        yield 'HTS dotted' => ['0101.21.00'];
        yield 'HTS number dotted' => ['0101.21.00.10'];
    }

    #[DataProvider('valid')]
    public function testDecodesTheHsSubheading(string $input): void
    {
        $code = new TariffCode($input);

        self::assertSame('010121', $code->hsSubheading()->value);
        self::assertSame('01', $code->hsSubheading()->chapter());
        self::assertMatchesRegularExpression('/^[0-9]{8}(?:[0-9]{2})?$/D', $code->value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalid(): iterable
    {
        yield 'six digits' => ['010121'];
        yield 'nine digits' => ['010121001'];
        yield 'eleven digits' => ['01012100100'];
        yield 'letters' => ['0101210A'];
        yield 'groups of four' => ['0101 2100 10'];
        yield 'dotted CN' => ['0101.2100'];
        yield 'mixed' => ['0101.21 00'];
        yield 'dotted six' => ['0101.21'];
        yield 'newline' => ["01012100\n"];
        yield 'empty' => [''];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value): void
    {
        try {
            new TariffCode($value);
            self::fail('Expected an exception.');
        } catch (InvalidTradeIdentifierException $e) {
            self::assertSame('tariff code has an invalid format.', $e->getMessage());
        }
    }
}
