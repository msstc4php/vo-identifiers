<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Substance;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidSubstanceIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\CasCheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\CommodityRules;
use MSSTC4PHP\ValueObject\Identifiers\Substance\CasRegistryNumber;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CasRegistryNumber::class)]
#[UsesClass(CasCheckDigit::class)]
#[UsesClass(CommodityRules::class)]
final class CasRegistryNumberTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function valid(): iterable
    {
        yield 'CAS example' => ['107-07-3', '107-07-3'];
        yield 'water' => ['7732-18-5', '7732-18-5'];
        yield 'compact' => ['7732185', '7732-18-5'];
        yield 'spaces' => [' 7732-18-5 ', '7732-18-5'];
        yield 'two-digit first part' => ['50-00-0', '50-00-0'];
    }

    #[DataProvider('valid')]
    public function testStoresTheHyphenatedForm(string $input, string $expected): void
    {
        $cas = new CasRegistryNumber($input);

        self::assertSame($expected, $cas->value);
        self::assertSame($expected, (string) $cas);
        self::assertSame(\substr($expected, -1), $cas->checkDigit());
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function invalid(): iterable
    {
        yield 'wrong check' => ['7732-18-4', 'CAS Registry Number has an invalid check digit.'];
        yield 'one digit first part' => ['7-07-3', 'CAS Registry Number has an invalid format.'];
        yield 'eight digit first part' => ['10000000-07-3', 'CAS Registry Number has an invalid format.'];
        yield 'letters' => ['7732-1A-5', 'CAS Registry Number has an invalid format.'];
        yield 'leading garbage' => ['X7732-18-5', 'CAS Registry Number has an invalid format.'];
        yield 'newline' => ["7732-18-5\n", 'CAS Registry Number has an invalid format.'];
        yield 'empty' => ['', 'CAS Registry Number has an invalid format.'];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value, string $message): void
    {
        try {
            new CasRegistryNumber($value);
            self::fail('Expected an exception.');
        } catch (InvalidSubstanceIdentifierException $e) {
            self::assertSame($message, $e->getMessage());
        }
    }
}
