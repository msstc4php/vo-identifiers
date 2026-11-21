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
use ReflectionMethod;

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

    /**
     * @return iterable<string, array{string, int}>
     */
    public static function badMncLengths(): iterable
    {
        yield 'one' => ['310150123456789', 1];
        yield 'four' => ['310150123456789', 4];
        yield 'leaves no subscriber number' => ['310150', 3];
    }

    #[DataProvider('badMncLengths')]
    public function testMncRefusesALengthThatIsNotTwoOrThreeOrLeavesNoSubscriberNumber(string $value, int $length): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ReflectionMethod(Imsi::class, 'mnc')->invoke(new Imsi($value), $length);
    }

    #[DataProvider('badMncLengths')]
    public function testMsinRefusesTheSameLengths(string $value, int $length): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ReflectionMethod(Imsi::class, 'msin')->invoke(new Imsi($value), $length);
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

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function masks(): iterable
    {
        yield '6 digits' => ['310150', '31015*'];
        yield '7 digits' => ['3101501', '31015**'];
        yield '12 digits: tail not kept' => ['310150123456', '31015*******'];
        yield '13 digits: tail kept' => ['3101501234567', '31015******67'];
        yield '15 digits' => ['310150123456789', '31015********89'];
    }

    #[DataProvider('masks')]
    public function testMasks(string $value, string $masked): void
    {
        self::assertSame($masked, new Imsi($value)->masked());
    }
}
