<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Transport;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTransportIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Identifiers\Transport\Wmi;
use MSSTC4PHP\ValueObject\Identifiers\Transport\WmiRegion;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Wmi::class)]
#[UsesClass(TransportRules::class)]
final class WmiTest extends TestCase
{
    public function testAccessors(): void
    {
        $wmi = new Wmi('1g4');

        self::assertSame('1G4', $wmi->value);
        self::assertSame('1G', $wmi->allocation());
        self::assertSame('4', $wmi->manufacturer());
        self::assertFalse($wmi->isSmallManufacturer());
    }

    /**
     * ISO 3780:2009 Annex A, Table A.1: every first character, the zone of its row group.
     *
     * @return iterable<string, array{string, ?WmiRegion}>
     */
    public static function regions(): iterable
    {
        foreach (['A', 'B', 'C'] as $first) {
            yield $first => [$first . 'AA', WmiRegion::Africa];
        }
        foreach (['D', 'E', 'F', 'G', 'H', '7', '0'] as $first) {
            yield $first . ' reserved' => [$first . 'AA', null];
        }
        foreach (['J', 'K', 'L', 'M', 'N', 'P', 'R'] as $first) {
            yield $first => [$first . 'AA', WmiRegion::Asia];
        }
        foreach (['S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'] as $first) {
            yield $first => [$first . 'AA', WmiRegion::Europe];
        }
        foreach (['1', '2', '3', '4', '5'] as $first) {
            yield $first => [$first . 'AA', WmiRegion::NorthAmerica];
        }
        yield '6' => ['6AA', WmiRegion::Oceania];
        yield '8' => ['8AA', WmiRegion::SouthAmerica];
        yield '9' => ['9AA', WmiRegion::SouthAmerica];
    }

    #[DataProvider('regions')]
    public function testRegion(string $value, ?WmiRegion $expected): void
    {
        self::assertSame($expected, new Wmi($value)->region());
    }

    public function testLowercaseInputDecodesTheRegion(): void
    {
        self::assertSame(WmiRegion::Europe, new Wmi('wdb')->region());
    }

    public function testSmallManufacturer(): void
    {
        self::assertTrue(new Wmi('SB9')->isSmallManufacturer());
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalid(): iterable
    {
        yield 'I' => ['1I4'];
        yield 'O' => ['O14'];
        yield 'Q' => ['14Q'];
        yield 'two' => ['1G'];
        yield 'four' => ['1G4A'];
        yield 'newline' => ["1G4\n"];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value): void
    {
        try {
            new Wmi($value);
            self::fail('Expected an exception.');
        } catch (InvalidTransportIdentifierException $e) {
            self::assertSame('WMI has an invalid format.', $e->getMessage());
        }
    }
}
