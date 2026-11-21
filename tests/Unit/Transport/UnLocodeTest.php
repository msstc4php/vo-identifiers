<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Transport;

use MSSTC4PHP\ValueObject\I18n\CountryCode;
use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTransportIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Identifiers\Transport\UnLocode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(UnLocode::class)]
#[UsesClass(TransportRules::class)]
final class UnLocodeTest extends TestCase
{
    public function testParts(): void
    {
        $code = new UnLocode('de ham');

        self::assertSame('DEHAM', $code->value);
        self::assertSame('DE', $code->countryCode());
        self::assertSame('HAM', $code->location());
        self::assertSame(CountryCode::DE, $code->country());
    }

    public function testNumeralsTwoToNineAreLocationCharacters(): void
    {
        self::assertSame('USNY2', (new UnLocode('USNY2'))->value);
        self::assertSame('USNY9', (new UnLocode('USNY9'))->value);
    }

    public function testCountryIsNullOutsideIso3166(): void
    {
        self::assertNull(new UnLocode('XZABC')->country());
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalid(): iterable
    {
        yield 'numeral 1' => ['DEHA1'];
        yield 'numeral 0' => ['DEHA0'];
        yield 'digit in country' => ['D1HAM'];
        yield 'four' => ['DEHA'];
        yield 'six' => ['DEHAMB'];
        yield 'newline' => ["DEHAM\n"];
        yield 'empty' => [''];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value): void
    {
        try {
            new UnLocode($value);
            self::fail('Expected an exception.');
        } catch (InvalidTransportIdentifierException $e) {
            self::assertSame('UN/LOCODE has an invalid format.', $e->getMessage());
        }
    }
}
