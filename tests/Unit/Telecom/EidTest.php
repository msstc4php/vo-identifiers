<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Telecom;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTelecomIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Iso7064Mod97;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TelecomRules;
use MSSTC4PHP\ValueObject\Identifiers\Telecom\Eid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Eid::class)]
#[UsesClass(Iso7064Mod97::class)]
#[UsesClass(TelecomRules::class)]
final class EidTest extends TestCase
{
    /**
     * SGP.29 prints no example EID; both values carry the check pair of clause 10.
     *
     * @return iterable<string, array{string}>
     */
    public static function valid(): iterable
    {
        yield 'without the 89 prefix' => ['35209900176148100000000000000078'];
        yield 'with the 89 prefix' => ['89000000000000000000000000000002'];
    }

    #[DataProvider('valid')]
    public function testAccepts(string $value): void
    {
        self::assertSame($value, new Eid($value)->value);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function invalid(): iterable
    {
        yield 'wrong check pair' => ['35209900176148100000000000000079', 'EID has a wrong check digit.'];
        yield '31 digits' => ['3520990017614810000000000000078', 'EID has an invalid format.'];
        yield '33 digits' => ['352099001761481000000000000000780', 'EID has an invalid format.'];
        yield 'letter' => ['3520990017614810000000000000007A', 'EID has an invalid format.'];
        yield 'spaces' => ['35209900 17614810 00000000 00000078', 'EID has an invalid format.'];
        yield 'newline' => ["35209900176148100000000000000078\n", 'EID has an invalid format.'];
        yield 'empty' => ['', 'EID has an invalid format.'];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value, string $message): void
    {
        try {
            new Eid($value);
            self::fail('Expected an exception.');
        } catch (InvalidTelecomIdentifierException $e) {
            self::assertSame($message, $e->getMessage());
        }
    }

    public function testMasks(): void
    {
        self::assertSame('35209900**********************78', new Eid('35209900176148100000000000000078')->masked());
    }
}
