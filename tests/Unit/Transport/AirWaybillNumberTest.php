<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Transport;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTransportIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\AirWaybillCheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Identifiers\Transport\AirWaybillNumber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(AirWaybillNumber::class)]
#[UsesClass(TransportRules::class)]
#[UsesClass(AirWaybillCheckDigit::class)]
final class AirWaybillNumberTest extends TestCase
{
    /**
     * @return iterable<string, array{string}>
     */
    public static function valid(): iterable
    {
        yield 'printed' => ['020-12345675'];
        yield 'compact' => ['02012345675'];
        yield 'spaces' => ['020 1234 5675'];
        yield 'hyphen before the check digit' => ['020-1234567-5'];
    }

    #[DataProvider('valid')]
    public function testAcceptsTheAbfExample(string $input): void
    {
        $awb = new AirWaybillNumber($input);

        self::assertSame('02012345675', $awb->value);
        self::assertSame('020', $awb->airlinePrefix());
        self::assertSame('1234567', $awb->serialNumber());
        self::assertSame('5', $awb->checkDigit());
        self::assertSame('020-12345675', $awb->formatted());
    }

    public function testAWrongCheckDigitIsRefused(): void
    {
        try {
            new AirWaybillNumber('020-12345676');
            self::fail('Expected an exception.');
        } catch (InvalidTransportIdentifierException $e) {
            self::assertSame('Air waybill number has a wrong check digit.', $e->getMessage());
        }
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalid(): iterable
    {
        yield 'ten digits' => ['020-1234567'];
        yield 'twelve digits' => ['020-123456755'];
        yield 'letter' => ['020-1234567A'];
        yield 'newline' => ["020-12345675\n"];
        yield 'empty' => [''];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value): void
    {
        try {
            new AirWaybillNumber($value);
            self::fail('Expected an exception.');
        } catch (InvalidTransportIdentifierException $e) {
            self::assertSame('Air waybill number has an invalid format.', $e->getMessage());
        }
    }
}
