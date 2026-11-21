<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Internal;

use LogicException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\EicCheckDigit;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(EicCheckDigit::class)]
final class EicCheckDigitTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function bodies(): iterable
    {
        // ENTSO-E EIC Data Exchange IG v1.2: the worked example 11XRWENET12345- gives 2 (sum 2107), and the area 11Y123456789012T.
        yield 'IG worked example' => ['11XRWENET12345-', '2'];
        yield 'IG area' => ['11Y123456789012', 'T'];
        yield 'zero result' => ['10X000000000010', '0'];
        yield 'all zeros' => ['000000000000000', '0'];
        yield 'small sum' => ['000000000000001', 'Z'];
        yield 'digit result' => ['10X000000000000', '3'];
        yield 'letter result' => ['10X000000000003', 'Y'];
        yield 'ten is A' => ['10X000000000049', 'A'];
    }

    #[DataProvider('bodies')]
    public function testComputesTheCheckCharacter(string $body, string $expected): void
    {
        self::assertSame($expected, EicCheckDigit::compute($body));
    }

    #[DataProvider('bodies')]
    public function testAcceptsTheBodyWithItsCheckCharacter(string $body, string $expected): void
    {
        self::assertTrue(EicCheckDigit::isValid($body . $expected));
    }

    public function testTheImplementationGuideCallsThisCodeIncorrect(): void
    {
        self::assertFalse(EicCheckDigit::isValid('10Z317973010277Q'));
    }

    public function testRejectsAWrongCheckCharacter(): void
    {
        self::assertFalse(EicCheckDigit::isValid('11XRWENET12345-3'));
        self::assertFalse(EicCheckDigit::isValid('11Y123456789012U'));
    }

    public function testAHyphenIsNeverAValidCheckCharacter(): void
    {
        self::assertFalse(EicCheckDigit::isValid('10X000000000002-'));
        self::assertFalse(EicCheckDigit::isValid('10X000000000001-'));
    }

    public function testRejectsInvalidShapesWithoutThrowing(): void
    {
        self::assertFalse(EicCheckDigit::isValid(''));
        self::assertFalse(EicCheckDigit::isValid('11XRWENET12345-'));
        self::assertFalse(EicCheckDigit::isValid('11XRWENET12345-22'));
        self::assertFalse(EicCheckDigit::isValid('11xRWENET12345-2'));
        self::assertFalse(EicCheckDigit::isValid('11XRWENET12345_2'));
        self::assertFalse(EicCheckDigit::isValid("11XRWENET12345-2\n"));
    }

    public function testRejectsAShortBody(): void
    {
        $this->expectException(LogicException::class);
        EicCheckDigit::compute('11XRWENET1234-');
    }

    public function testRejectsALongBody(): void
    {
        $this->expectException(LogicException::class);
        EicCheckDigit::compute('11XRWENET12345-2');
    }

    public function testRejectsATrailingNewline(): void
    {
        $this->expectException(LogicException::class);
        EicCheckDigit::compute("11XRWENET12345-\n");
    }

    public function testRejectsABodyOutsideTheAlphabet(): void
    {
        $this->expectException(LogicException::class);
        EicCheckDigit::compute('11xRWENET12345-');
    }

    public function testRejectsABodyWhoseCheckCharacterWouldBeAHyphen(): void
    {
        $this->expectException(LogicException::class);
        EicCheckDigit::compute('10X000000000002');
    }
}
