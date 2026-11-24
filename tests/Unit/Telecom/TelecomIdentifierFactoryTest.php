<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Telecom;

use MSSTC4PHP\ValueObject\Exception\AmbiguousValueException;
use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTelecomIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Iso7064Mod97;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Luhn;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TelecomRules;
use MSSTC4PHP\ValueObject\Identifiers\Telecom\Eid;
use MSSTC4PHP\ValueObject\Identifiers\Telecom\Iccid;
use MSSTC4PHP\ValueObject\Identifiers\Telecom\Imei;
use MSSTC4PHP\ValueObject\Identifiers\Telecom\Imeisv;
use MSSTC4PHP\ValueObject\Identifiers\Telecom\Imsi;
use MSSTC4PHP\ValueObject\Identifiers\Telecom\TelecomIdentifierFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TelecomIdentifierFactory::class)]
#[UsesClass(Luhn::class)]
#[UsesClass(Iso7064Mod97::class)]
#[UsesClass(TelecomRules::class)]
#[UsesClass(Imei::class)]
#[UsesClass(Imeisv::class)]
#[UsesClass(Iccid::class)]
#[UsesClass(Imsi::class)]
#[UsesClass(Eid::class)]
final class TelecomIdentifierFactoryTest extends TestCase
{
    /**
     * @return iterable<string, array{string, class-string}>
     */
    public static function unambiguous(): iterable
    {
        yield 'IMEISV' => ['2605317931138301', Imeisv::class];
        yield 'ICCID' => ['8944444444444444447', Iccid::class];
        yield 'EID' => ['35209900176148100000000000000078', Eid::class];
        yield '15 digits, bad Luhn: IMSI' => ['310150123456789', Imsi::class];
        yield '12 digits: IMSI' => ['310150123456', Imsi::class];
    }

    /**
     * @param class-string $class
     */
    #[DataProvider('unambiguous')]
    public function testPicksTheOnlyMatch(string $value, string $class): void
    {
        self::assertInstanceOf($class, TelecomIdentifierFactory::fromString($value));
    }

    /**
     * @return iterable<string, array{string, non-empty-list<class-string>}>
     */
    public static function ambiguous(): iterable
    {
        yield '15 digits valid as IMEI and IMSI' => ['490154203237518', [Imei::class, Imsi::class]];
        yield '14 digits' => ['26053179311383', [Imei::class, Imsi::class]];
        yield '14 digits starting 89' => ['89333333333333', [Imei::class, Iccid::class, Imsi::class]];
        yield '15 digits starting 89' => ['893333333333339', [Imei::class, Iccid::class, Imsi::class]];
        yield '16 digits starting 89' => ['8933333333333334', [Imeisv::class, Iccid::class]];
    }

    /**
     * @param non-empty-list<class-string> $candidates
     */
    #[DataProvider('ambiguous')]
    public function testAmbiguousValuesNameEveryCandidate(string $value, array $candidates): void
    {
        try {
            TelecomIdentifierFactory::fromString($value);
            self::fail('Expected an exception.');
        } catch (AmbiguousValueException $e) {
            self::assertSame($candidates, $e->candidates());
            self::assertStringNotContainsString($value, $e->getMessage());
        }
    }

    public function testThrowsForNoMatch(): void
    {
        try {
            TelecomIdentifierFactory::fromString('not an identifier');
            self::fail('Expected an exception.');
        } catch (InvalidTelecomIdentifierException $e) {
            self::assertSame('Value is not a recognised telecom identifier.', $e->getMessage());
        }
    }

    public function testTryFromStringBuildsWhatFromStringBuilds(): void
    {
        $imeisv = TelecomIdentifierFactory::tryFromString('2605317931138301');

        self::assertInstanceOf(Imeisv::class, $imeisv);
        self::assertEquals(TelecomIdentifierFactory::fromString('2605317931138301'), $imeisv);
    }

    public function testTryFromStringIsNullForNoMatch(): void
    {
        self::assertNull(TelecomIdentifierFactory::tryFromString('not an identifier'));
    }

    public function testTryFromStringIsNullForAnAmbiguousValue(): void
    {
        self::assertNull(TelecomIdentifierFactory::tryFromString('490154203237518'));
    }
}
