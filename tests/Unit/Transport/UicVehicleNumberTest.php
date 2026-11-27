<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Transport;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTransportIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Luhn;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Identifiers\Transport\UicVehicleNumber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(UicVehicleNumber::class)]
#[UsesClass(TransportRules::class)]
#[UsesClass(Luhn::class)]
final class UicVehicleNumberTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function valid(): iterable
    {
        yield 'printed, Decision 2007/756/EC example 1' => ['33 84 4796 100-8', '338447961008'];
        yield 'compact, example 1' => ['338447961008', '338447961008'];
        yield 'printed, example 2' => ['31 51 3320 198-0', '315133201980'];
        yield 'compact, example 2' => ['315133201980', '315133201980'];
    }

    #[DataProvider('valid')]
    public function testAcceptsPrintedAndCompactForms(string $input, string $expected): void
    {
        self::assertSame($expected, new UicVehicleNumber($input)->value);
    }

    public function testAccessors(): void
    {
        $number = new UicVehicleNumber('31 51 3320 198-0');

        self::assertSame('31', $number->vehicleType());
        self::assertSame('51', $number->uicCountryCode());
        self::assertSame('0', $number->checkDigit());
        self::assertSame('31 51 3320 198-0', $number->formatted());
    }

    public function testAWrongCheckDigitIsRefused(): void
    {
        try {
            new UicVehicleNumber('31 51 3320 198-1');
            self::fail('Expected an exception.');
        } catch (InvalidTransportIdentifierException $e) {
            self::assertSame('UIC vehicle number has a wrong check digit.', $e->getMessage());
        }
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalid(): iterable
    {
        yield 'eleven digits' => ['31513320198'];
        yield 'thirteen digits' => ['3151332019800'];
        yield 'letter' => ['31513320198A'];
        yield 'newline' => ["315133201980\n"];
        yield 'empty' => [''];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value): void
    {
        try {
            new UicVehicleNumber($value);
            self::fail('Expected an exception.');
        } catch (InvalidTransportIdentifierException $e) {
            self::assertSame('UIC vehicle number has an invalid format.', $e->getMessage());
        }
    }
}
