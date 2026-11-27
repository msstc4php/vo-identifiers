<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Transport;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTransportIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Identifiers\Transport\IcaoAirportCode;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IcaoAirportCode::class)]
#[UsesClass(TransportRules::class)]
final class IcaoAirportCodeTest extends TestCase
{
    public function testUppercases(): void
    {
        self::assertSame('EDDF', new IcaoAirportCode('eddf')->value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalid(): iterable
    {
        yield 'short' => ['EDD'];
        yield 'long' => ['EDDFX'];
        yield 'digit' => ['1DDF'];
        yield 'newline' => ["EDDF\n"];
        yield 'empty' => [''];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value): void
    {
        try {
            new IcaoAirportCode($value);
            self::fail('Expected an exception.');
        } catch (InvalidTransportIdentifierException $e) {
            self::assertSame('ICAO airport code has an invalid format.', $e->getMessage());
        }
    }
}
