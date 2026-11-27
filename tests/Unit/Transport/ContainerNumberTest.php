<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Transport;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTransportIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Identifiers\Transport\ContainerNumber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ContainerNumber::class)]
#[UsesClass(TransportRules::class)]
final class ContainerNumberTest extends TestCase
{
    public function testAcceptsTheBicExample(): void
    {
        $number = new ContainerNumber('BICU 123456 5');

        self::assertSame('BICU1234565', $number->value);
        self::assertSame('BIC', $number->ownerCode());
        self::assertSame('U', $number->category());
        self::assertSame('123456', $number->serialNumber());
        self::assertSame('5', $number->checkDigit());
        self::assertSame('BICU 123456 5', $number->formatted());
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function valid(): iterable
    {
        yield 'review focus value' => ['CSQU3054383', 'CSQU3054383'];
        yield 'spaces' => ['CSQU 305438 3', 'CSQU3054383'];
        yield 'lowercase' => ['csqu3054383', 'CSQU3054383'];
        yield 'J' => ['ABCJ1234560', 'ABCJ1234560'];
        yield 'Z' => ['ABCZ1234560', 'ABCZ1234560'];
        yield 'check digit is not verified' => ['BICU1234560', 'BICU1234560'];
    }

    #[DataProvider('valid')]
    public function testAccepts(string $input, string $expected): void
    {
        self::assertSame($expected, new ContainerNumber($input)->value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalid(): iterable
    {
        yield 'category X' => ['BICX1234565'];
        yield 'digit in owner' => ['B1CU1234565'];
        yield 'six digits' => ['BICU123456'];
        yield 'eight digits' => ['BICU12345655'];
        yield 'letter in serial' => ['BICU12345A5'];
        yield 'extra leading letter' => ['XBICU1234565'];
        yield 'newline' => ["BICU1234565\n"];
        yield 'empty' => [''];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value): void
    {
        try {
            new ContainerNumber($value);
            self::fail('Expected an exception.');
        } catch (InvalidTransportIdentifierException $e) {
            self::assertSame('Container number has an invalid format.', $e->getMessage());
        }
    }
}
