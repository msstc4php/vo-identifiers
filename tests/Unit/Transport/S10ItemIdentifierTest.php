<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Transport;

use MSSTC4PHP\ValueObject\I18n\CountryCode;
use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTransportIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\S10CheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Identifiers\Transport\S10ItemIdentifier;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(S10ItemIdentifier::class)]
#[UsesClass(TransportRules::class)]
#[UsesClass(S10CheckDigit::class)]
final class S10ItemIdentifierTest extends TestCase
{
    public function testAcceptsTheItemOfTheUpuExample(): void
    {
        $item = new S10ItemIdentifier('RA 47312482 9 CH');

        self::assertSame('RA473124829CH', $item->value);
        self::assertSame('RA', $item->serviceIndicator());
        self::assertSame('47312482', $item->serialNumber());
        self::assertSame('9', $item->checkDigit());
        self::assertSame('CH', $item->countryCode());
        self::assertSame(CountryCode::CH, $item->country());
    }

    public function testLowercase(): void
    {
        self::assertSame('RA473124829CH', new S10ItemIdentifier('ra473124829ch')->value);
    }

    public function testCountryIsNullWhenTheUpuCodeIsNotIso3166(): void
    {
        self::assertNull(new S10ItemIdentifier('RA473124829XZ')->country());
    }

    public function testAWrongCheckDigitIsRefused(): void
    {
        try {
            new S10ItemIdentifier('RA473124828CH');
            self::fail('Expected an exception.');
        } catch (InvalidTransportIdentifierException $e) {
            self::assertSame('S10 item identifier has a wrong check digit.', $e->getMessage());
        }
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function reserved(): iterable
    {
        foreach (['J', 'K', 'S', 'T', 'W'] as $first) {
            yield $first . 'A' => [$first . 'A473124829CH'];
            yield $first . 'Z' => [$first . 'Z473124829CH'];
        }
    }

    #[DataProvider('reserved')]
    public function testReservedServiceIndicatorsAreRefused(string $value): void
    {
        try {
            new S10ItemIdentifier($value);
            self::fail('Expected an exception.');
        } catch (InvalidTransportIdentifierException $e) {
            self::assertSame('S10 item identifier has a reserved service indicator.', $e->getMessage());
        }
    }

    public function testNeighbouringServiceIndicatorsAreAccepted(): void
    {
        foreach (['IA', 'LA', 'RA', 'UA', 'VA', 'XA', 'AA', 'CP'] as $indicator) {
            self::assertSame($indicator, new S10ItemIdentifier($indicator . '473124829CH')->serviceIndicator());
        }
    }

    public function testReservedLettersOutsideTheServiceIndicatorAreAccepted(): void
    {
        self::assertSame('RJ473124829KW', new S10ItemIdentifier('RJ473124829KW')->value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalid(): iterable
    {
        yield 'digit in indicator' => ['R1473124829CH'];
        yield 'short serial' => ['RA47312482CH'];
        yield 'no country' => ['RA473124829'];
        yield 'digit in country' => ['RA473124829C1'];
        yield 'extra leading letter' => ['XRA473124829CH'];
        yield 'newline' => ["RA473124829CH\n"];
        yield 'empty' => [''];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value): void
    {
        try {
            new S10ItemIdentifier($value);
            self::fail('Expected an exception.');
        } catch (InvalidTransportIdentifierException $e) {
            self::assertSame('S10 item identifier has an invalid format.', $e->getMessage());
        }
    }
}
