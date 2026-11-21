<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Energy;

use MSSTC4PHP\ValueObject\Identifiers\Energy\Mpan;
use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidEnergyIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\CommodityRules;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Mpan::class)]
#[UsesClass(CommodityRules::class)]
final class MpanTest extends TestCase
{
    public function testCoreOnly(): void
    {
        $mpan = new Mpan('12 3456 7890 123');

        self::assertSame('1234567890123', $mpan->value);
        self::assertSame('1234567890123', $mpan->core());
        self::assertSame('12', $mpan->distributorId());
        self::assertFalse($mpan->hasTopLine());
    }

    public function testFullFormKeepsTheTopLineUndecoded(): void
    {
        $mpan = new Mpan('01 801 100 12 3456 7890 123');

        self::assertSame('018011001234567890123', $mpan->value);
        self::assertSame('1234567890123', $mpan->core());
        self::assertSame('12', $mpan->distributorId());
        self::assertTrue($mpan->hasTopLine());
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalid(): iterable
    {
        yield 'twelve digits' => ['123456789012'];
        yield 'fourteen digits' => ['12345678901234'];
        yield 'twenty digits' => ['12345678901234567890'];
        yield 'letters' => ['12345678901A3'];
        yield 'newline' => ["1234567890123\n"];
        yield 'empty' => [''];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value): void
    {
        try {
            new Mpan($value);
            self::fail('Expected an exception.');
        } catch (InvalidEnergyIdentifierException $e) {
            self::assertSame('MPAN has an invalid format.', $e->getMessage());
        }
    }
}
