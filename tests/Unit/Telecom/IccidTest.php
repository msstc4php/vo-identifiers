<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Telecom;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTelecomIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Luhn;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TelecomRules;
use MSSTC4PHP\ValueObject\Identifiers\Telecom\Iccid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Iccid::class)]
#[UsesClass(Luhn::class)]
#[UsesClass(TelecomRules::class)]
final class IccidTest extends TestCase
{
    /**
     * @return iterable<string, array{string}>
     */
    public static function valid(): iterable
    {
        yield '18 digits' => ['894444444444444442'];
        yield '19 digits' => ['8944444444444444447'];
        yield '20 digits' => ['89444444444444444440'];
    }

    #[DataProvider('valid')]
    public function testAccepts(string $value): void
    {
        $iccid = new Iccid($value);

        self::assertSame($value, $iccid->value);
        self::assertSame('8944444', $iccid->issuerIdentifier());
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function invalid(): iterable
    {
        yield 'wrong check digit' => ['8944444444444444448', 'ICCID has an invalid check digit.'];
        yield 'no 89 prefix' => ['9944444444444444441', 'ICCID has an invalid format.'];
        yield '89 not at the start' => ['11894444444444444449', 'ICCID has an invalid format.'];
        yield 'prefix 88' => ['8844444444444444440', 'ICCID has an invalid format.'];
        yield '17 digits' => ['89444444444444442', 'ICCID has an invalid format.'];
        yield '21 digits' => ['894444444444444444443', 'ICCID has an invalid format.'];
        yield 'padding F' => ['8944444444444444444F', 'ICCID has an invalid format.'];
        yield 'spaces' => ['8944 4444 4444 4444 447', 'ICCID has an invalid format.'];
        yield 'newline' => ["8944444444444444447\n", 'ICCID has an invalid format.'];
        yield 'empty' => ['', 'ICCID has an invalid format.'];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value, string $message): void
    {
        try {
            new Iccid($value);
            self::fail('Expected an exception.');
        } catch (InvalidTelecomIdentifierException $e) {
            self::assertSame($message, $e->getMessage());
        }
    }

    public function testMasks(): void
    {
        self::assertSame('8944444**********47', new Iccid('8944444444444444447')->masked());
        self::assertSame('8944444***********40', new Iccid('89444444444444444440')->masked());
    }
}
