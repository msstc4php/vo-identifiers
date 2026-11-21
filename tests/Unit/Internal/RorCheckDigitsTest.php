<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Internal;

use LogicException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Iso7064Mod97;
use MSSTC4PHP\ValueObject\Identifiers\Internal\RorCheckDigits;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RorCheckDigits::class)]
#[UsesClass(Iso7064Mod97::class)]
final class RorCheckDigitsTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function bodies(): iterable
    {
        // Real ROR identifiers (https://ror.org/<id>); the body is the six characters after the leading 0.
        yield '02mhbdp94' => ['2mhbdp', '94'];
        yield '01an7q238' => ['1an7q2', '38'];
        yield '03yrm5c26' => ['3yrm5c', '26'];
        yield '05gq02987' => ['5gq029', '87'];
        yield '0168r3w48' => ['168r3w', '48'];
        yield 'smallest' => ['000000', '98'];
        yield 'largest' => ['zzzzzz', '02'];
    }

    #[DataProvider('bodies')]
    public function testComputesTheCheckPair(string $body, string $expected): void
    {
        self::assertSame($expected, RorCheckDigits::compute($body));
    }

    #[DataProvider('bodies')]
    public function testAcceptsTheBodyWithItsCheckPair(string $body, string $expected): void
    {
        self::assertTrue(RorCheckDigits::isValid($body . $expected));
    }

    public function testIsCaseInsensitive(): void
    {
        self::assertTrue(RorCheckDigits::isValid('2MHBDP94'));
        self::assertSame('94', RorCheckDigits::compute('2MHBDP'));
    }

    public function testRejectsAWrongPair(): void
    {
        self::assertFalse(RorCheckDigits::isValid('2mhbdp95'));
        self::assertFalse(RorCheckDigits::isValid('2mhbdp49'));
        self::assertFalse(RorCheckDigits::isValid('2mhbdp00'));
    }

    public function testRejectsInvalidShapesWithoutThrowing(): void
    {
        self::assertFalse(RorCheckDigits::isValid(''));
        self::assertFalse(RorCheckDigits::isValid('2mhbdp9'));
        self::assertFalse(RorCheckDigits::isValid('2mhbdp944'));
        self::assertFalse(RorCheckDigits::isValid('2mhbil94'));
        self::assertFalse(RorCheckDigits::isValid('2mhbdpa4'));
        self::assertFalse(RorCheckDigits::isValid("2mhbdp94\n"));
        self::assertFalse(RorCheckDigits::isValid('x2mhbdp94'));
    }

    public function testRejectsAShortBody(): void
    {
        $this->expectException(LogicException::class);
        RorCheckDigits::compute('2mhbd');
    }

    public function testRejectsAnExcludedLetter(): void
    {
        $this->expectException(LogicException::class);
        RorCheckDigits::compute('2mhbdu');
    }

    public function testRejectsALongBody(): void
    {
        $this->expectException(LogicException::class);
        RorCheckDigits::compute('2mhbdp9');
    }

    public function testRejectsATrailingNewline(): void
    {
        $this->expectException(LogicException::class);
        RorCheckDigits::compute("2mhbdp\n");
    }

    public function testRejectsALeadingCharacter(): void
    {
        $this->expectException(LogicException::class);
        RorCheckDigits::compute('x2mhbdp');
    }
}
