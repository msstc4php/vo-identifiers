<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Publication;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidPublicationIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\PublicationRules;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Isrc;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Isrc::class)]
#[UsesClass(PublicationRules::class)]
final class IsrcTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function valid(): iterable
    {
        yield 'IFPI example printed' => ['ISRC AA-6QZ-20-00047', 'AA6QZ2000047'];
        yield 'hyphens' => ['AA-6QZ-20-00047', 'AA6QZ2000047'];
        yield 'compact' => ['AA6QZ2000047', 'AA6QZ2000047'];
        yield 'lowercase' => ['aa6qz2000047', 'AA6QZ2000047'];
        yield 'spaces' => ['AA 6QZ 20 00047', 'AA6QZ2000047'];
        yield 'country IS with registrant RC1' => ['ISRC12000047', 'ISRC12000047'];
    }

    #[DataProvider('valid')]
    public function testAcceptsAndStoresTheCompactForm(string $input, string $expected): void
    {
        self::assertSame($expected, (new Isrc($input))->value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalid(): iterable
    {
        yield 'digit in the country' => ['A16QZ2000047'];
        yield 'letter in the year' => ['AA6QZ2X00047'];
        yield 'letter in the designation' => ['AA6QZ20A0047'];
        yield 'symbol in the registrant' => ['AA6Q*2000047'];
        yield 'eleven characters' => ['AA6QZ200004'];
        yield 'thirteen characters' => ['AA6QZ20000471'];
        yield 'trailing newline' => ["AA6QZ2000047\n"];
        yield 'empty' => [''];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value): void
    {
        $this->expectException(InvalidPublicationIdentifierException::class);
        new Isrc($value);
    }

    public function testMessageNamesOnlyTheType(): void
    {
        try {
            new Isrc('zz-secret-zz');
            self::fail('Expected an exception.');
        } catch (InvalidPublicationIdentifierException $e) {
            self::assertSame('ISRC has an invalid format.', $e->getMessage());
        }
    }

    public function testParts(): void
    {
        $isrc = new Isrc('AA-6QZ-20-00047');

        self::assertSame('AA', $isrc->countryCode());
        self::assertSame('6QZ', $isrc->registrant());
        self::assertSame('20', $isrc->year());
        self::assertSame('00047', $isrc->designation());
        self::assertSame('AA-6QZ-20-00047', $isrc->formatted());
    }
}
