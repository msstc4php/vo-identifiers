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
    public function testAcceptsTheCoreAndTheFullForm(): void
    {
        self::assertSame('1234567890123', (new Mpan('1234567890123'))->value);
        self::assertSame('012345671234567890123', (new Mpan('012345671234567890123'))->value);
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
        yield 'spaces' => ['12 3456 7890 123'];
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
