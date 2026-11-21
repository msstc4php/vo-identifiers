<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Telecom;

use InvalidArgumentException;
use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTelecomIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TelecomRules;
use MSSTC4PHP\ValueObject\Identifiers\Telecom\Imsi;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Imsi::class)]
#[UsesClass(TelecomRules::class)]
final class ImsiTest extends TestCase
{
    public function testParts(): void
    {
        $imsi = new Imsi('310150123456789');

        self::assertSame('310', $imsi->mcc());
        self::assertSame('15', $imsi->mnc(2));
        self::assertSame('0123456789', $imsi->msin(2));
        self::assertSame('150', $imsi->mnc(3));
        self::assertSame('123456789', $imsi->msin(3));
    }

    public function testRefusesAnMncLengthOtherThanTwoOrThree(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Imsi('310150123456789')->mnc(4);
    }

    public function testRefusesAnMsinLengthOtherThanTwoOrThree(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Imsi('310150123456789')->msin(1);
    }

    public function testRefusesAnMncThatLeavesNoSubscriberNumber(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Imsi('310150')->msin(3);
    }

    public function testShortestImsi(): void
    {
        self::assertSame('0', new Imsi('310150')->msin(2));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalid(): iterable
    {
        yield 'five' => ['31015'];
        yield 'sixteen' => ['3101501234567890'];
        yield 'letter' => ['31015012345678A'];
        yield 'spaces' => ['310 150 123456789'];
        yield 'newline' => ["310150123456789\n"];
        yield 'empty' => [''];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value): void
    {
        try {
            new Imsi($value);
            self::fail('Expected an exception.');
        } catch (InvalidTelecomIdentifierException $e) {
            self::assertSame('IMSI has an invalid format.', $e->getMessage());
        }
    }

    public function testMasks(): void
    {
        self::assertSame('31015********89', new Imsi('310150123456789')->masked());
        self::assertSame('31015*', new Imsi('310150')->masked());
        self::assertSame('31015*1', new Imsi('3101501')->masked());
        self::assertSame('31015**89', new Imsi('310150189')->masked());
    }
}
