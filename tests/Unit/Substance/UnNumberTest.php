<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Substance;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidSubstanceIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\CommodityRules;
use MSSTC4PHP\ValueObject\Identifiers\Substance\UnNumber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(UnNumber::class)]
#[UsesClass(CommodityRules::class)]
final class UnNumberTest extends TestCase
{
    /**
     * @return iterable<string, array{string}>
     */
    public static function valid(): iterable
    {
        yield 'prefixed' => ['UN1203'];
        yield 'bare' => ['1203'];
        yield 'spaced' => ['UN 1203'];
        yield 'lowercase' => ['un1203'];
    }

    #[DataProvider('valid')]
    public function testPrefixedAndBareFormsGiveTheSameValue(string $input): void
    {
        $un = new UnNumber($input);

        self::assertSame('UN1203', $un->value);
        self::assertSame('1203', $un->number());
        self::assertTrue($un->equals(new UnNumber('UN1203')));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalid(): iterable
    {
        yield 'three digits' => ['UN120'];
        yield 'five digits' => ['12034'];
        yield 'twice' => ['UNUN1203'];
        yield 'letters' => ['UN12O3'];
        yield 'newline' => ["UN1203\n"];
        yield 'empty' => [''];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value): void
    {
        try {
            new UnNumber($value);
            self::fail('Expected an exception.');
        } catch (InvalidSubstanceIdentifierException $e) {
            self::assertSame('UN number has an invalid format.', $e->getMessage());
        }
    }
}
