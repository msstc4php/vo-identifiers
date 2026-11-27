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
    // The second check characters (`9`, `E`) are computed with the package's own MOD 37,36 helper: no source prints
    // a 26-character vector (ruling 10).
    private const string WITH_VERSION = 'B159D8FA01240000K9F3A02459';

    private const string ZERO_VERSION = 'B159D8FA01240000K00000000E';

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
        self::assertSame($expected, new Isan($input)->value);
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

    /**
     * ISO 15706-2:2023, 4.4: a version segment `0000-0000` is treated as no version. Every pair holds one ISAN in two
     * stored forms.
     *
     * @return iterable<string, array{string, string}>
     */
    public static function zeroVersionPairs(): iterable
    {
        yield '24 digits, 16 digits' => ['B159D8FA0124000000000000', 'B159D8FA01240000'];
        yield '24 digits, 17 characters' => ['B159D8FA0124000000000000', 'B159D8FA01240000K'];
        yield '26 characters, 17 characters' => [self::ZERO_VERSION, 'B159D8FA01240000K'];
        yield '26 characters, 16 digits' => [self::ZERO_VERSION, 'B159D8FA01240000'];
        yield '26 characters, 24 digits' => [self::ZERO_VERSION, 'B159D8FA0124000000000000'];
        yield 'hyphenated' => ['ISAN B159-D8FA-0124-0000-K-0000-0000-E', 'ISAN B159-D8FA-0124-0000-K'];
        yield 'hyphenated, no check characters' => ['B159-D8FA-0124-0000-0000-0000', 'B159-D8FA-0124-0000'];
    }

    #[DataProvider('zeroVersionPairs')]
    public function testZeroVersionEqualsNoVersionInBothDirections(string $withZeroVersion, string $withoutVersion): void
    {
        $zero = new Isan($withZeroVersion);
        $none = new Isan($withoutVersion);

        self::assertTrue($zero->equals($none));
        self::assertTrue($none->equals($zero));
    }

    public function testZeroVersionKeepsTheFormGiven(): void
    {
        $isan = new Isan('ISAN B159-D8FA-0124-0000-K-0000-0000-E');

        self::assertSame(self::ZERO_VERSION, $isan->value);
        self::assertSame(self::ZERO_VERSION, (string) $isan);
        self::assertSame('"' . self::ZERO_VERSION . '"', \json_encode($isan, \JSON_THROW_ON_ERROR));
        self::assertSame('00000000', $isan->version());
        self::assertTrue($isan->hasVersion());
        self::assertFalse($isan->isPrivateVersion());
        self::assertSame('ISAN B159-D8FA-0124-0000-K-0000-0000-E', $isan->formatted());
    }

    public function testZeroVersionStillChecksTheSecondCheckCharacter(): void
    {
        $this->expectException(InvalidPublicationIdentifierException::class);
        new Isan('B159D8FA01240000K00000000F');
    }

    public function testZeroVersionKeepsOtherIsansApart(): void
    {
        self::assertFalse(new Isan(self::ZERO_VERSION)->equals(new Isan(self::WITH_VERSION)));
        self::assertFalse(new Isan(self::ZERO_VERSION)->equals(new Isan('B159D8FA01240001')));
        self::assertFalse(new Isan('B159D8FA0124000000000000')->equals(new Isan('B159D8FA0124000000000001')));
        self::assertFalse(new Isan('B159D8FA01240000')->equals(new Isan('B159D8FA0124000000000001')));
    }

    /**
     * @return iterable<string, array{string, bool}>
     */
    public static function privateVersions(): iterable
    {
        // ISO 15706-2:2023, 4.5 and 4.6: F000 0000 to FFFF FFFF are private, up to EFFF FFFF public.
        yield 'no version' => ['B159D8FA01240000K', false];
        yield 'public version' => [self::WITH_VERSION, false];
        yield 'last public version' => ['B159D8FA01240000EFFFFFFF', false];
        yield 'first private version' => ['B159D8FA01240000F0000000', true];
        yield 'last private version' => ['B159D8FA01240000FFFFFFFF', true];
        yield 'lowercase input' => ['b159d8fa01240000f0000000', true];
        yield 'private version, 26 characters' => ['B159D8FA01240000KF0000000M', true];
    }

    #[DataProvider('privateVersions')]
    public function testReportsAPrivateVersion(string $value, bool $expected): void
    {
        self::assertSame($expected, new Isan($value)->isPrivateVersion());
    }
}
