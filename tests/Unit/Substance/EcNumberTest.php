<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Substance;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidSubstanceIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\CommodityRules;
use MSSTC4PHP\ValueObject\Identifiers\Internal\EcNumberCheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Substance\EcNumber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(EcNumber::class)]
#[UsesClass(EcNumberCheckDigit::class)]
#[UsesClass(CommodityRules::class)]
final class EcNumberTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function valid(): iterable
    {
        // ECHA, "About EC and list numbers".
        yield 'ECHA example' => ['100-000-1', '100-000-1'];
        yield 'list number with a letter' => ['A00-001-5', 'A00-001-5'];
        yield 'compact' => ['1000001', '100-000-1'];
        yield 'lowercase' => ['a00-001-5', 'A00-001-5'];
    }

    #[DataProvider('valid')]
    public function testStoresTheHyphenatedForm(string $input, string $expected): void
    {
        $ec = new EcNumber($input);

        self::assertSame($expected, $ec->value);
        self::assertSame(\substr($expected, -1), $ec->checkDigit());
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function invalid(): iterable
    {
        yield 'wrong check' => ['100-000-2', 'EC number has a wrong check digit.'];
        yield 'letter I' => ['I00-000-1', 'EC number has an invalid format.'];
        yield 'letter check' => ['100-000-A', 'EC number has an invalid format.'];
        yield 'short' => ['100-00-1', 'EC number has an invalid format.'];
        yield 'leading garbage' => ['X100-000-1', 'EC number has an invalid format.'];
        yield 'mixed hyphens' => ['100000-1', 'EC number has an invalid format.'];
        yield 'spaces' => ['100 000 1', 'EC number has an invalid format.'];
        yield 'newline' => ["100-000-1\n", 'EC number has an invalid format.'];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value, string $message): void
    {
        try {
            new EcNumber($value);
            self::fail('Expected an exception.');
        } catch (InvalidSubstanceIdentifierException $e) {
            self::assertSame($message, $e->getMessage());
        }
    }
}
