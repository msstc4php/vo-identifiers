<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Internal;

use MSSTC4PHP\ValueObject\Identifiers\Internal\EicCheckDigit;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(EicCheckDigit::class)]
final class EicCheckDigitTest extends TestCase
{
    /**
     * @return iterable<string, array{string}>
     */
    public static function validCodes(): iterable
    {
        // ENTSO-E EIC Data Exchange IG v1.2: the worked example 11XRWENET12345- gives 2 (sum 2107), and the area 11Y123456789012T.
        yield 'IG worked example' => ['11XRWENET12345-2'];
        yield 'IG area' => ['11Y123456789012T'];
        // Synthetic bodies that reach each branch of the check value.
        yield 'zero result' => ['10X0000000000100'];
        yield 'all zeros' => ['0000000000000000'];
        yield 'small sum' => ['000000000000001Z'];
        yield 'digit result' => ['10X0000000000003'];
        yield 'letter result' => ['10X000000000003Y'];
        yield 'ten is A' => ['10X000000000049A'];
    }

    #[DataProvider('validCodes')]
    public function testAcceptsAValidCode(string $code): void
    {
        self::assertTrue(EicCheckDigit::isValid($code));
    }

    #[DataProvider('validCodes')]
    public function testRejectsEveryOtherCheckCharacter(string $code): void
    {
        $body = \substr($code, 0, 15);
        foreach (\str_split('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ') as $other) {
            if ($other !== $code[15]) {
                self::assertFalse(EicCheckDigit::isValid($body . $other));
            }
        }
    }

    public function testTheImplementationGuideCallsThisCodeIncorrect(): void
    {
        self::assertFalse(EicCheckDigit::isValid('10Z317973010277Q'));
    }

    public function testAHyphenIsNeverAValidCheckCharacter(): void
    {
        self::assertFalse(EicCheckDigit::isValid('10X000000000002-'));
        self::assertFalse(EicCheckDigit::isValid('10X000000000001-'));
        self::assertFalse(EicCheckDigit::isValid('10X0000000000020'));
    }

    public function testRejectsInvalidShapesWithoutThrowing(): void
    {
        self::assertFalse(EicCheckDigit::isValid(''));
        self::assertFalse(EicCheckDigit::isValid('11XRWENET12345-'));
        self::assertFalse(EicCheckDigit::isValid('11XRWENET12345-22'));
        self::assertFalse(EicCheckDigit::isValid('11xRWENET12345-2'));
        self::assertFalse(EicCheckDigit::isValid('11XRWENET12345_2'));
        self::assertFalse(EicCheckDigit::isValid("11XRWENET12345-2\n"));
        self::assertFalse(EicCheckDigit::isValid('x11XRWENET12345-2'));
    }
}
