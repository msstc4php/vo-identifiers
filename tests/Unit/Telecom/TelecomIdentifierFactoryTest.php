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
     * @return iterable<string, array{string, list<string>}>
     */
    public static function ambiguous(): iterable
    {
        yield '15 digits valid as IMEI and IMSI' => ['490154203237518', ['Imei', 'Imsi']];
        yield '14 digits' => ['26053179311383', ['Imei', 'Imsi']];
        yield '14 digits starting 89' => ['89333333333333', ['Imei', 'Iccid', 'Imsi']];
        yield '15 digits starting 89' => ['893333333333339', ['Imei', 'Iccid', 'Imsi']];
        yield '16 digits starting 89' => ['8933333333333334', ['Imeisv', 'Iccid']];
    }

    /**
     * @param list<string> $names
     */
    #[DataProvider('ambiguous')]
    public function testAmbiguousValuesNameEveryCandidate(string $value, array $names): void
    {
        try {
            TelecomIdentifierFactory::fromString($value);
            self::fail('Expected an exception.');
        } catch (AmbiguousValueException $e) {
            foreach ($names as $name) {
                self::assertStringContainsString($name, $e->getMessage());
            }
            self::assertStringNotContainsString($value, $e->getMessage());
        }
    }

    public function testThrowsForNoMatch(): void
    {
        $this->expectException(InvalidTelecomIdentifierException::class);

        TelecomIdentifierFactory::fromString('not an identifier');
    }
}
