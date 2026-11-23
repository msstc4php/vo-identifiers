<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Publication;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidPublicationIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Iso7064Mod11_2;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Iso7064Mod37_36;
use MSSTC4PHP\ValueObject\Identifiers\Internal\PublicationRules;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Isan;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Isni;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Isan::class)]
#[UsesClass(PublicationRules::class)]
#[UsesClass(Iso7064Mod37_36::class)]
#[UsesClass(Iso7064Mod11_2::class)]
#[UsesClass(Isni::class)]
final class IsanTest extends TestCase
{
    private const string WITH_VERSION = 'B159D8FA01240000K9F3A02459';

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function valid(): iterable
    {
        yield 'annex 1 printed' => ['ISAN B159-D8FA-0124-0000-K', 'B159D8FA01240000K'];
        yield 'compact' => ['B159D8FA01240000K', 'B159D8FA01240000K'];
        yield 'lowercase' => ['b159d8fa01240000k', 'B159D8FA01240000K'];
        yield 'with version' => ['B159-D8FA-0124-0000-K-9F3A-0245-9', self::WITH_VERSION];
        yield 'with version compact' => [self::WITH_VERSION, self::WITH_VERSION];
        yield 'root and episode only' => ['B159-D8FA-0124-0000', 'B159D8FA01240000'];
        yield 'with version, no check characters' => ['B159-D8FA-0124-0000-9F3A-0245', 'B159D8FA012400009F3A0245'];
        yield 'with version and label' => ['ISAN B159-D8FA-0124-0000-K-9F3A-0245-9', self::WITH_VERSION];
    }

    #[DataProvider('valid')]
    public function testAcceptsAndStoresTheCompactForm(string $input, string $expected): void
    {
        self::assertSame($expected, (new Isan($input))->value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalid(): iterable
    {
        yield 'wrong first check' => ['B159-D8FA-0124-0000-L'];
        yield 'wrong first check with version' => ['B159-D8FA-0124-0000-L-9F3A-0245-9'];
        yield 'wrong second check' => ['B159-D8FA-0124-0000-K-9F3A-0245-8'];
        yield 'first check only before a version' => ['B159D8FA01240000K9F3A0245'];
        yield 'wrong first check, no version' => ['B159-D8FA-0124-0000-0'];
        yield 'wrong second check, last digit' => ['B159-D8FA-0124-0000-K-9F3A-0245-0'];
        yield 'fifteen digits' => ['B159D8FA0124000'];
        yield 'eighteen characters' => ['B159D8FA01240000K9'];
        yield 'non-hex in root' => ['G159-D8FA-0124-0000-K'];
        yield 'non-hex in version' => ['B159-D8FA-0124-0000-K-9F3G-0245-9'];
        yield 'trailing newline' => ["B159D8FA01240000K\n"];
        yield 'empty' => [''];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value): void
    {
        $this->expectException(InvalidPublicationIdentifierException::class);
        new Isan($value);
    }

    public function testMessageNamesOnlyTheType(): void
    {
        try {
            new Isan('B159-D8FA-0124-0000-L');
            self::fail('Expected an exception.');
        } catch (InvalidPublicationIdentifierException $e) {
            self::assertSame('ISAN has a wrong check character.', $e->getMessage());
        }

        try {
            new Isan('zz-secret-zz');
            self::fail('Expected an exception.');
        } catch (InvalidPublicationIdentifierException $e) {
            self::assertSame('ISAN has an invalid format.', $e->getMessage());
        }
    }

    public function testSecondCheckCoversTheVersionButNotTheFirstCheck(): void
    {
        self::assertSame('9', Iso7064Mod37_36::compute('B159D8FA012400009F3A0245'));
    }

    public function testParts(): void
    {
        $isan = new Isan('B159-D8FA-0124-0000-K');

        self::assertSame('B159D8FA0124', $isan->root());
        self::assertSame('0000', $isan->episode());
        self::assertNull($isan->version());
        self::assertFalse($isan->hasVersion());
        self::assertTrue($isan->hasCheckCharacters());
        self::assertSame('ISAN B159-D8FA-0124-0000-K', $isan->formatted());
    }

    public function testPartsWithoutCheckCharacters(): void
    {
        $isan = new Isan('B159-D8FA-0124-0000');

        self::assertSame('B159D8FA0124', $isan->root());
        self::assertSame('0000', $isan->episode());
        self::assertNull($isan->version());
        self::assertFalse($isan->hasVersion());
        self::assertFalse($isan->hasCheckCharacters());
        self::assertSame('ISAN B159-D8FA-0124-0000', $isan->formatted());
    }

    public function testPartsOfAVersionWithoutCheckCharacters(): void
    {
        $isan = new Isan('B159D8FA012400009F3A0245');

        self::assertSame('9F3A0245', $isan->version());
        self::assertTrue($isan->hasVersion());
        self::assertFalse($isan->hasCheckCharacters());
        self::assertSame('ISAN B159-D8FA-0124-0000-9F3A-0245', $isan->formatted());
    }

    public function testPartsWithVersion(): void
    {
        $isan = new Isan(self::WITH_VERSION);

        self::assertSame('B159D8FA0124', $isan->root());
        self::assertSame('0000', $isan->episode());
        self::assertSame('9F3A0245', $isan->version());
        self::assertTrue($isan->hasVersion());
        self::assertSame('ISAN B159-D8FA-0124-0000-K-9F3A-0245-9', $isan->formatted());
    }

    public function testEqualityIgnoresTheCheckCharacters(): void
    {
        self::assertTrue(new Isan('B159D8FA01240000')->equals(new Isan('B159D8FA01240000K')));
        self::assertTrue(new Isan('B159D8FA01240000K')->equals(new Isan('B159D8FA01240000')));
        self::assertTrue(new Isan('B159D8FA012400009F3A0245')->equals(new Isan(self::WITH_VERSION)));
        self::assertTrue(new Isan(self::WITH_VERSION)->equals(new Isan('B159D8FA012400009F3A0245')));
    }

    public function testEqualityKeepsRootEpisodeAndVersionApart(): void
    {
        self::assertFalse(new Isan('B159D8FA01240000')->equals(new Isan('B159D8FA01240001')));
        self::assertFalse(new Isan('B159D8FA01240000')->equals(new Isan('A159D8FA01240000')));
        self::assertFalse(new Isan('B159D8FA01240000K')->equals(new Isan(self::WITH_VERSION)));
        self::assertFalse(new Isan('B159D8FA01240000')->equals(new Isan('B159D8FA012400009F3A0245')));
        self::assertFalse(new Isan('B159D8FA012400009F3A0245')->equals(new Isan('B159D8FA012400009F3A0246')));
        self::assertFalse(new Isan('1422458635730476')->equals(Isni::from('1422458635730476')));
    }
}
