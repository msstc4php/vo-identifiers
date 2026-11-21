<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Transport;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTransportIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Identifiers\Transport\Mmsi;
use MSSTC4PHP\ValueObject\Identifiers\Transport\MmsiType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Mmsi::class)]
#[UsesClass(TransportRules::class)]
final class MmsiTest extends TestCase
{
    /**
     * @return iterable<string, array{string, MmsiType, string|null}>
     */
    public static function classified(): iterable
    {
        yield 'ship 2' => ['201123456', MmsiType::Ship, '201'];
        yield 'ship 7' => ['775123456', MmsiType::Ship, '775'];
        yield 'ship 3' => ['366123456', MmsiType::Ship, '366'];
        yield 'group' => ['021112345', MmsiType::GroupShip, '211'];
        yield 'coast' => ['002111234', MmsiType::CoastStation, '211'];
        yield 'sar aircraft' => ['111211123', MmsiType::SarAircraft, '211'];
        yield 'aid to navigation' => ['992111234', MmsiType::AidToNavigation, '211'];
        yield 'craft associated' => ['982111234', MmsiType::CraftAssociated, '211'];
        yield 'AIS-SART' => ['970123456', MmsiType::DistressDevice, null];
        yield 'MOB' => ['972123456', MmsiType::DistressDevice, null];
        yield 'EPIRB-AIS' => ['974123456', MmsiType::DistressDevice, null];
        yield 'AMRD group B' => ['979123456', MmsiType::Unclassified, null];
        yield 'leading 8' => ['800000000', MmsiType::Unclassified, null];
        yield 'leading 1 without 111' => ['110000000', MmsiType::Unclassified, null];
        yield 'leading 1 and 112' => ['112000000', MmsiType::Unclassified, null];
        yield 'leading 9 other' => ['960000000', MmsiType::Unclassified, null];
    }

    #[DataProvider('classified')]
    public function testTypeAndMid(string $value, MmsiType $type, ?string $mid): void
    {
        $mmsi = new Mmsi($value);

        self::assertSame($value, $mmsi->value);
        self::assertSame($type, $mmsi->type());
        self::assertSame($mid, $mmsi->mid());
    }

    public function testSpacesAreDropped(): void
    {
        self::assertSame('366123456', (new Mmsi('366 123 456'))->value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalid(): iterable
    {
        yield 'eight' => ['36612345'];
        yield 'ten' => ['3661234567'];
        yield 'letter' => ['36612345A'];
        yield 'newline' => ["366123456\n"];
        yield 'empty' => [''];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value): void
    {
        try {
            new Mmsi($value);
            self::fail('Expected an exception.');
        } catch (InvalidTransportIdentifierException $e) {
            self::assertSame('MMSI has an invalid format.', $e->getMessage());
        }
    }
}
