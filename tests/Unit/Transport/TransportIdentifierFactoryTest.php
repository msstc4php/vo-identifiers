<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Transport;

use MSSTC4PHP\ValueObject\Exception\AmbiguousValueException;
use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTransportIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\AirWaybillCheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Luhn;
use MSSTC4PHP\ValueObject\Identifiers\Internal\S10CheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Identifiers\Internal\VinCheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Transport\AirWaybillNumber;
use MSSTC4PHP\ValueObject\Identifiers\Transport\ContainerNumber;
use MSSTC4PHP\ValueObject\Identifiers\Transport\DataIdentifierField;
use MSSTC4PHP\ValueObject\Identifiers\Transport\DataIdentifierString;
use MSSTC4PHP\ValueObject\Identifiers\Transport\IataAirportCode;
use MSSTC4PHP\ValueObject\Identifiers\Transport\IcaoAirportCode;
use MSSTC4PHP\ValueObject\Identifiers\Transport\ImoNumber;
use MSSTC4PHP\ValueObject\Identifiers\Transport\Mmsi;
use MSSTC4PHP\ValueObject\Identifiers\Transport\S10ItemIdentifier;
use MSSTC4PHP\ValueObject\Identifiers\Transport\TransportIdentifierFactory;
use MSSTC4PHP\ValueObject\Identifiers\Transport\UicVehicleNumber;
use MSSTC4PHP\ValueObject\Identifiers\Transport\UnLocode;
use MSSTC4PHP\ValueObject\Identifiers\Transport\Vin;
use MSSTC4PHP\ValueObject\Identifiers\Transport\Wmi;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TransportIdentifierFactory::class)]
#[UsesClass(TransportRules::class)]
#[UsesClass(VinCheckDigit::class)]
#[UsesClass(S10CheckDigit::class)]
#[UsesClass(AirWaybillCheckDigit::class)]
#[UsesClass(Luhn::class)]
#[UsesClass(DataIdentifierField::class)]
#[UsesClass(Vin::class)]
#[UsesClass(Wmi::class)]
#[UsesClass(ContainerNumber::class)]
#[UsesClass(ImoNumber::class)]
#[UsesClass(Mmsi::class)]
#[UsesClass(S10ItemIdentifier::class)]
#[UsesClass(AirWaybillNumber::class)]
#[UsesClass(UicVehicleNumber::class)]
#[UsesClass(UnLocode::class)]
#[UsesClass(IataAirportCode::class)]
#[UsesClass(IcaoAirportCode::class)]
#[UsesClass(DataIdentifierString::class)]
final class TransportIdentifierFactoryTest extends TestCase
{
    /**
     * @return iterable<string, array{string, class-string}>
     */
    public static function unambiguous(): iterable
    {
        yield 'VIN' => ['1G4AH59H45G118341', Vin::class];
        yield 'WMI' => ['1G4', Wmi::class];
        yield 'container' => ['CSQU 305438 3', ContainerNumber::class];
        yield 'IMO' => ['IMO 8712345', ImoNumber::class];
        yield 'seven digits: IMO' => ['8712345', ImoNumber::class];
        yield 'MMSI' => ['366123456', Mmsi::class];
        yield 'S10' => ['RA473124829CH', S10ItemIdentifier::class];
        yield 'air waybill' => ['020-12345675', AirWaybillNumber::class];
        yield 'UIC vehicle number' => ['31 51 3320 198-0', UicVehicleNumber::class];
        yield 'UN/LOCODE' => ['DEHAM', UnLocode::class];
        yield 'ICAO' => ['EDDF', IcaoAirportCode::class];
        yield 'data identifiers' => ["[)>\x1E06\x1D1PABC\x1E\x04", DataIdentifierString::class];
    }

    /**
     * @param class-string $class
     */
    #[DataProvider('unambiguous')]
    public function testPicksTheOnlyMatch(string $value, string $class): void
    {
        self::assertInstanceOf($class, TransportIdentifierFactory::fromString($value));
    }

    public function testThreeLettersAreAWmiAndAnAirportCode(): void
    {
        try {
            TransportIdentifierFactory::fromString('SYD');
            self::fail('Expected an exception.');
        } catch (AmbiguousValueException $e) {
            self::assertSame([Wmi::class, IataAirportCode::class], $e->candidates());
            self::assertStringNotContainsString('SYD', $e->getMessage());
        }
    }

    public function testThreeLettersWithAnIllegalVinCharacterAreOnlyIata(): void
    {
        self::assertInstanceOf(IataAirportCode::class, TransportIdentifierFactory::fromString('LIS'));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function unmatched(): iterable
    {
        yield 'not an identifier' => ['not an identifier!'];
        yield 'ISO/IEC 15459 shaped, not a candidate' => ['LN12345'];
        yield 'ISO/IEC 15459 licence plate shaped' => ['J12345678'];
    }

    #[DataProvider('unmatched')]
    public function testNothingMatches(string $value): void
    {
        try {
            TransportIdentifierFactory::fromString($value);
            self::fail('Expected an exception.');
        } catch (InvalidTransportIdentifierException $e) {
            self::assertSame('Value is not a recognised transport identifier.', $e->getMessage());
        }
    }
}
