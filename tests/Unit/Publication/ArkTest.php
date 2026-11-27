<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Publication;

use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidPublicationIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Mod11CheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\PublicationRules;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Ark;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Issn;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Ark::class)]
#[UsesClass(PublicationRules::class)]
#[UsesClass(Issn::class)]
#[UsesClass(Mod11CheckDigit::class)]
final class ArkTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function valid(): iterable
    {
        yield 'new form' => ['ark:12025/654xz321', 'ark:12025/654xz321'];
        yield 'old form' => ['ark:/12025/654xz321', 'ark:12025/654xz321'];
        yield 'uppercase label and NAAN' => ['ARK:12025/654xz321', 'ark:12025/654xz321'];
        yield 'betanumeric NAAN letters lowercased' => ['ark:B3C4D/x', 'ark:b3c4d/x'];
        yield 'name keeps case' => ['ark:12025/654XZ321', 'ark:12025/654XZ321'];
        yield 'name keeps hyphens' => ['ark:12025/654-xz-321', 'ark:12025/654-xz-321'];
        yield 'qualifiers' => ['ark:12148/btv1b8449691v/f29.item', 'ark:12148/btv1b8449691v/f29.item'];
        yield 'NMA dropped' => ['https://n2t.net/ark:12025/654xz321', 'ark:12025/654xz321'];
        yield 'NMA with old form' => ['http://example.org/ark:/12025/654xz321', 'ark:12025/654xz321'];
        yield 'unicode name' => ['ark:12025/é日本', 'ark:12025/é日本'];
    }

    #[DataProvider('valid')]
    public function testAcceptsAndStoresTheNormalForm(string $input, string $expected): void
    {
        self::assertSame($expected, new Ark($input)->value);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalid(): iterable
    {
        yield 'no label' => ['12025/654xz321'];
        yield 'no name' => ['ark:12025/'];
        yield 'no slash' => ['ark:12025'];
        yield 'empty NAAN' => ['ark:/654xz321'];
        yield 'vowel in NAAN' => ['ark:12a25/654xz321'];
        yield 'space in name' => ['ark:12025/65 4'];
        yield 'trailing newline' => ["ark:12025/654xz321\n"];
        yield 'control character' => ["ark:12025/65\x014"];
        yield 'invalid UTF-8' => ["ark:12025/65\xFF4"];
        yield 'NMA without label' => ['https://n2t.net/12025/654xz321'];
        yield 'empty' => [''];
    }

    #[DataProvider('invalid')]
    public function testRefuses(string $value): void
    {
        $this->expectException(InvalidPublicationIdentifierException::class);
        new Ark($value);
    }

    public function testMessageNamesOnlyTheType(): void
    {
        try {
            new Ark('zz-secret-zz');
            self::fail('Expected an exception.');
        } catch (InvalidPublicationIdentifierException $e) {
            self::assertSame('ARK has an invalid format.', $e->getMessage());
        }
    }

    public function testEqualityIgnoresHyphens(): void
    {
        $ark = new Ark('ark:12025/654-xz-321');

        self::assertTrue($ark->equals(new Ark('ark:12025/654xz321')));
        self::assertTrue($ark->equals(new Ark('ark:/12025/654-xz321')));
        self::assertFalse($ark->equals(new Ark('ark:12025/654xz322')));
        self::assertFalse($ark->equals(new Ark('ark:12026/654xz321')));
        self::assertFalse($ark->equals(new Issn('0317-8471')));
    }

    public function testParts(): void
    {
        $ark = new Ark('ark:/12148/btv1b8449691v/f29');

        self::assertSame('12148', $ark->naan());
        self::assertSame('btv1b8449691v/f29', $ark->name());
    }
}
