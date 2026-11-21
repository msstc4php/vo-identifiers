<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Internal;

use LogicException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Iso6346CheckDigit;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Iso6346CheckDigit::class)]
final class Iso6346CheckDigitTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function bodies(): iterable
    {
        // BIC, "BIC Codes" page: the example container "BICU 123456 5" (the algorithm text of ISO 6346 is not reachable).
        yield 'BIC example' => ['BICU123456', '5'];
        // A remainder of 10 is written 0.
        yield 'remainder ten' => ['AAAU000006', '0'];
        // Letter values skip multiples of 11: A is 10, K is 21, L is 23, V is 34, Z is 38.
        yield 'letters KLMU123456' => ['KLMU123456', '0'];
        yield 'letters VWXU123456' => ['VWXU123456', '8'];
        yield 'letters XYZU654321' => ['XYZU654321', '7'];
        yield 'letters JKLU000001' => ['JKLU000001', '9'];
    }

    #[DataProvider('bodies')]
    public function testComputesTheCheckDigit(string $body, string $expected): void
    {
        self::assertSame($expected, Iso6346CheckDigit::compute($body));
    }

    #[DataProvider('bodies')]
    public function testAcceptsTheBodyWithItsCheckDigit(string $body, string $expected): void
    {
        self::assertTrue(Iso6346CheckDigit::isValid($body . $expected));
    }

    public function testRejectsAWrongCheckDigit(): void
    {
        self::assertFalse(Iso6346CheckDigit::isValid('BICU1234566'));
        self::assertFalse(Iso6346CheckDigit::isValid('BICU1234560'));
    }

    public function testRejectsInvalidShapesWithoutThrowing(): void
    {
        self::assertFalse(Iso6346CheckDigit::isValid(''));
        self::assertFalse(Iso6346CheckDigit::isValid('BICU123456'));
        self::assertFalse(Iso6346CheckDigit::isValid('BICU12345655'));
        self::assertFalse(Iso6346CheckDigit::isValid('bicu1234565'));
        self::assertFalse(Iso6346CheckDigit::isValid('BICU123456X'));
        self::assertFalse(Iso6346CheckDigit::isValid('BIC01234565'));
        self::assertFalse(Iso6346CheckDigit::isValid("BICU1234565\n"));
        self::assertFalse(Iso6346CheckDigit::isValid('xBICU1234565'));
    }

    public function testRejectsABodyOfTheWrongShape(): void
    {
        $this->expectException(LogicException::class);
        Iso6346CheckDigit::compute('BICU12345');
    }

    public function testRejectsALeadingCharacter(): void
    {
        $this->expectException(LogicException::class);
        Iso6346CheckDigit::compute('xBICU123456');
    }

    public function testRejectsATrailingNewline(): void
    {
        $this->expectException(LogicException::class);
        Iso6346CheckDigit::compute("BICU123456\n");
    }

    public function testRejectsADigitInTheOwnerCode(): void
    {
        $this->expectException(LogicException::class);
        Iso6346CheckDigit::compute('BIC0123456');
    }

    public function testRejectsALetterInTheSerial(): void
    {
        $this->expectException(LogicException::class);
        Iso6346CheckDigit::compute('BICU12345A');
    }

    public function testRejectsAnOverlongBody(): void
    {
        $this->expectException(LogicException::class);
        Iso6346CheckDigit::compute('BICU1234567');
    }
}
