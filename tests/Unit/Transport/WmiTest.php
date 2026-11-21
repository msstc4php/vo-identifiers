<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Transport;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTransportIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Identifiers\Transport\Wmi;
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
