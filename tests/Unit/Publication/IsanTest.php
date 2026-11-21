<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Publication;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidPublicationIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Iso7064Mod37_36;
use MSSTC4PHP\ValueObject\Identifiers\Internal\PublicationRules;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Isan;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Isan::class)]
#[UsesClass(PublicationRules::class)]
#[UsesClass(Iso7064Mod37_36::class)]
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
        yield 'no check' => ['B159D8FA01240000'];
        yield 'version without second check' => ['B159D8FA01240000K9F3A0245'];
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
        self::assertSame('ISAN B159-D8FA-0124-0000-K', $isan->formatted());
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
}
