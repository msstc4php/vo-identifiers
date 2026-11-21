<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Internal;

use MSSTC4PHP\ValueObject\Identifiers\Internal\IssuingAgencyRegister;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(IssuingAgencyRegister::class)]
final class IssuingAgencyRegisterTest extends TestCase
{
    public function testNoCodeIsTheBeginningOfAnother(): void
    {
        $codes = IssuingAgencyRegister::codes();

        foreach ($codes as $code) {
            foreach ($codes as $other) {
                if ($code !== $other) {
                    self::assertFalse(\str_starts_with($other, $code), 'Register is not prefix-free.');
                }
            }
        }
    }

    public function testRegisterSizeAndShapeOfTheCodes(): void
    {
        $codes = IssuingAgencyRegister::codes();

        self::assertCount(62, $codes);
        foreach ($codes as $code) {
            self::assertMatchesRegularExpression('/^(?:[0-9]|[A-Z]{1,3})$/D', $code);
            self::assertNotNull(IssuingAgencyRegister::nameOf($code));
        }
    }

    public function testLongestMatchAndMisses(): void
    {
        self::assertSame('KCA', IssuingAgencyRegister::codeOf('KCA1'));
        self::assertSame('GH', IssuingAgencyRegister::codeOf('GH'));
        self::assertSame('LN', IssuingAgencyRegister::codeOf('LN12345'));
        self::assertSame('J', IssuingAgencyRegister::codeOf('J12345'));
        self::assertSame('7', IssuingAgencyRegister::codeOf('7123'));
        self::assertSame('VBS', IssuingAgencyRegister::codeOf('VBS1'));
        self::assertSame('D', IssuingAgencyRegister::codeOf('D'));
        self::assertNull(IssuingAgencyRegister::codeOf('K'));
        self::assertNull(IssuingAgencyRegister::codeOf('KC'));
        self::assertNull(IssuingAgencyRegister::codeOf(''));
        self::assertNull(IssuingAgencyRegister::nameOf('ZZ'));
    }
}
