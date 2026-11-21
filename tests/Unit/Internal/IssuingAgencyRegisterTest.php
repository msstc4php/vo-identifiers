<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Internal;

use MSSTC4PHP\ValueObject\Identifiers\Internal\IssuingAgencyRegister;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(IssuingAgencyRegister::class)]
final class IssuingAgencyRegisterTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function codePairs(): iterable
    {
        $codes = IssuingAgencyRegister::codes();
        foreach ($codes as $code) {
            foreach (\array_diff($codes, [$code]) as $other) {
                yield $code . ' / ' . $other => [$code, $other];
            }
        }
    }

    #[DataProvider('codePairs')]
    public function testNoCodeIsTheBeginningOfAnother(string $code, string $other): void
    {
        self::assertFalse(\str_starts_with($other, $code), 'Register is not prefix-free.');
    }

    public function testRegisterSize(): void
    {
        $codes = IssuingAgencyRegister::codes();

        self::assertCount(62, $codes);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function codes(): iterable
    {
        foreach (IssuingAgencyRegister::codes() as $code) {
            yield $code => [$code];
        }
    }

    #[DataProvider('codes')]
    public function testEachCodeHasTheRegisterShapeAndAName(string $code): void
    {
        self::assertMatchesRegularExpression('/^(?:[0-9]|[A-Z]{1,3})$/D', $code);
        self::assertNotNull(IssuingAgencyRegister::nameOf($code));
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
