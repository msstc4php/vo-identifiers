<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Transport;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTransportIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Identifiers\Internal\VinCheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Transport\Vin;
use MSSTC4PHP\ValueObject\Identifiers\Transport\Wmi;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Vin::class)]
#[UsesClass(Wmi::class)]
#[UsesClass(TransportRules::class)]
#[UsesClass(VinCheckDigit::class)]
final class VinTest extends TestCase
{
    public function testAcceptsTheSampleOf49CfrTableVi(): void
    {
        $vin = new Vin('1G4AH59H45G118341');

        self::assertSame('1G4AH59H45G118341', $vin->value);
        self::assertTrue($vin->hasValidCheckDigit());
        self::assertSame('1G4', $vin->wmi()->value);
        self::assertSame('AH59H4', $vin->vds());
        self::assertSame('5G118341', $vin->vis());
    }

    public function testUppercases(): void
    {
        self::assertSame('1G4AH59H45G118341', (new Vin('1g4ah59h45g118341'))->value);
    }

    public function testAWrongPositionNineIsNeverRefused(): void
    {
        self::assertFalse(new Vin('1G4AH59H55G118341')->hasValidCheckDigit());
        self::assertFalse(new Vin('1G4AH59HZ5G118341')->hasValidCheckDigit());
    }

    /**
     * @return iterable<string, array{string, list<int>}>
     */
    public static function years(): iterable
    {
        yield 'digit 5' => ['1G4AH59H45G118341', [2005, 2035]];
        yield 'digit 1' => ['1G4AH59H41G118341', [2001, 2031]];
        yield 'digit 9' => ['1G4AH59H49G118341', [2009, 2039]];
        yield 'letter A' => ['1G4AH59H4AG118341', [1980, 2010]];
        yield 'letter Y' => ['1G4AH59H4YG118341', [2000, 2030]];
        yield 'letter M' => ['1G4AH59H4MG118341', [1991, 2021]];
        yield 'letter V' => ['1G4AH59H4VG118341', [1997, 2027]];
        yield 'zero' => ['1G4AH59H40G118341', []];
        yield 'U' => ['1G4AH59H4UG118341', []];
        yield 'Z' => ['1G4AH59H4ZG118341', []];
    }

    /**
     * @param list<int> $expected
     */
    #[DataProvider('years')]
    public function testModelYearCandidates(string $value, array $expected): void
    {
        self::assertSame($expected, new Vin($value)->modelYear());
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalid(): iterable
    {
        yield 'I' => ['1G4AH59I45G118341'];
        yield 'O' => ['1G4AH59O45G118341'];
        yield 'Q' => ['1G4AH59Q45G118341'];
        yield 'sixteen' => ['1G4AH59H45G11834'];
        yield 'eighteen' => ['1G4AH59H45G1183411'];
        yield 'spaces' => ['1G4AH 59H45G118341'];
        yield 'trailing newline' => ["1G4AH59H45G118341\n"];
        yield 'empty' => [''];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value): void
    {
        try {
            new Vin($value);
            self::fail('Expected an exception.');
        } catch (InvalidTransportIdentifierException $e) {
            self::assertSame('VIN has an invalid format.', $e->getMessage());
        }
    }
}
