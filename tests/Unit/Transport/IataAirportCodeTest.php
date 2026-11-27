<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Transport;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTransportIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Identifiers\Transport\IataAirportCode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IataAirportCode::class)]
#[UsesClass(TransportRules::class)]
final class IataAirportCodeTest extends TestCase
{
    public function testUppercases(): void
    {
        self::assertSame('SYD', new IataAirportCode('syd')->value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalid(): iterable
    {
        yield 'short' => ['YD'];
        yield 'long' => ['SYDN'];
        yield 'digit' => ['1YD'];
        yield 'newline' => ["SYD\n"];
        yield 'empty' => [''];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value): void
    {
        try {
            new IataAirportCode($value);
            self::fail('Expected an exception.');
        } catch (InvalidTransportIdentifierException $e) {
            self::assertSame('IATA airport code has an invalid format.', $e->getMessage());
        }
    }
}
