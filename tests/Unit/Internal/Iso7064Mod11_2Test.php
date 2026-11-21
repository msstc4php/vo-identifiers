<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Internal;

use LogicException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Iso7064Mod11_2;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(Iso7064Mod11_2::class)]
final class Iso7064Mod11_2Test extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function bodies(): iterable
    {
        // ISO 27729:2012 Annex A, Table A.1: 1422 4586 3573 0476.
        yield 'ISO 27729 A.1' => ['142245863573047', '6'];
        // ORCID, "Structure of the ORCID Identifier": 0000-0002-1825-0097, 0000-0001-5109-3700, 0000-0002-1694-233X.
        yield 'ORCID 1' => ['000000021825009', '7'];
        yield 'ORCID 2' => ['000000015109370', '0'];
        yield 'ORCID 3 is X' => ['000000021694233', 'X'];
    }

    #[DataProvider('bodies')]
    public function testComputesTheCheckCharacter(string $body, string $expected): void
    {
        self::assertSame($expected, Iso7064Mod11_2::compute($body));
    }

    #[DataProvider('bodies')]
    public function testAcceptsTheBodyWithItsCheckCharacter(string $body, string $expected): void
    {
        self::assertTrue(Iso7064Mod11_2::isValid($body . $expected));
    }

    public function testRejectsAWrongCheckCharacter(): void
    {
        self::assertFalse(Iso7064Mod11_2::isValid('1422458635730475'));
        self::assertFalse(Iso7064Mod11_2::isValid('142245863573047X'));
        self::assertFalse(Iso7064Mod11_2::isValid('0000000216942330'));
    }

    public function testAcceptsTheShortestValue(): void
    {
        self::assertTrue(Iso7064Mod11_2::isValid('01'));
        self::assertFalse(Iso7064Mod11_2::isValid('00'));
    }

    public function testRejectsInvalidShapesWithoutThrowing(): void
    {
        self::assertFalse(Iso7064Mod11_2::isValid(''));
        self::assertFalse(Iso7064Mod11_2::isValid('6'));
        self::assertFalse(Iso7064Mod11_2::isValid('14224586357304A6'));
        self::assertFalse(Iso7064Mod11_2::isValid('000000021694233x'));
    }

    public function testAVeryLongBodyStaysExact(): void
    {
        $check = Iso7064Mod11_2::compute(\str_repeat('9', 80));

        self::assertTrue(Iso7064Mod11_2::isValid(\str_repeat('9', 80) . $check));
    }

    public function testRejectsAnEmptyBody(): void
    {
        $this->expectException(LogicException::class);
        Iso7064Mod11_2::compute('');
    }

    public function testRejectsANonDigitBody(): void
    {
        $this->expectException(LogicException::class);
        Iso7064Mod11_2::compute('14224586357304X');
    }
}
