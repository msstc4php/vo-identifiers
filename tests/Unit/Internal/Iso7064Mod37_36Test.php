<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Internal;

use LogicException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Iso7064Mod37_36;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Iso7064Mod37_36::class)]
final class Iso7064Mod37_36Test extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function bodies(): iterable
    {
        // ISAN International Agency, "ISAN Check Characters Calculation" v2.0, Annex 1: B159-D8FA-0124-0000-K.
        yield 'ISAN annex 1' => ['B159D8FA01240000', 'K'];
        // The same procedure over letters and digits (ISO 7064 MOD 37,36): the intermediate product 1 gives 0.
        yield 'product one gives zero' => ['0089', '0'];
        yield 'all zeros' => ['0000000000000000', '9'];
        yield 'letters' => ['ZZZZ', '9'];
        yield 'single zero' => ['0', '2'];
        yield 'single letter' => ['A', 'H'];
        yield 'ten is A' => ['0003', 'A'];
        yield 'nine' => ['0068', '9'];
    }

    #[DataProvider('bodies')]
    public function testComputesTheCheckCharacter(string $body, string $expected): void
    {
        self::assertSame($expected, Iso7064Mod37_36::compute($body));
    }

    #[DataProvider('bodies')]
    public function testAcceptsTheBodyWithItsCheckCharacter(string $body, string $expected): void
    {
        self::assertTrue(Iso7064Mod37_36::isValid($body . $expected));
    }

    public function testTheSecondCharacterCoversTheVersionSegmentToo(): void
    {
        $root = 'B159D8FA01240000';
        $version = '00000001';
        $second = Iso7064Mod37_36::compute($root . $version);

        self::assertTrue(Iso7064Mod37_36::isValid($root . $version . $second));
        self::assertNotSame($second, Iso7064Mod37_36::compute($root . '00000002'));
    }

    public function testRejectsAWrongCheckCharacter(): void
    {
        self::assertFalse(Iso7064Mod37_36::isValid('B159D8FA01240000L'));
        self::assertFalse(Iso7064Mod37_36::isValid('B159D8FA01240000J'));
        self::assertFalse(Iso7064Mod37_36::isValid('B159D8FA012400001'));
    }

    public function testRejectsInvalidShapesWithoutThrowing(): void
    {
        self::assertFalse(Iso7064Mod37_36::isValid(''));
        self::assertFalse(Iso7064Mod37_36::isValid('K'));
        self::assertFalse(Iso7064Mod37_36::isValid('b159D8FA01240000K'));
        self::assertFalse(Iso7064Mod37_36::isValid('B159-D8FA-0124-0000-K'));
    }

    public function testRejectsAnEmptyBody(): void
    {
        $this->expectException(LogicException::class);
        Iso7064Mod37_36::compute('');
    }

    public function testRejectsLowercaseInTheBody(): void
    {
        $this->expectException(LogicException::class);
        Iso7064Mod37_36::compute('b159');
    }
}
