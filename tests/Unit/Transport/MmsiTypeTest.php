<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit\Transport;

use BackedEnum;
use MSSTC4PHP\ValueObject\Identifiers\Transport\MmsiStationType;
use MSSTC4PHP\ValueObject\Identifiers\Transport\MmsiType;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionEnum;

/**
 * No coverage attributes: the alias is file-level code in `src/Transport/MmsiType.php`, which no `Covers*` attribute can name.
 */
final class MmsiTypeTest extends TestCase
{
    /**
     * Payloads as `serialize()` wrote them under 0.2.0, where the enum was `Transport\MmsiType`.
     *
     * @return iterable<string, array{string, MmsiStationType}>
     */
    public static function payloadsOf02(): iterable
    {
        yield 'Ship' => ['E:57:"MSSTC4PHP\ValueObject\Identifiers\Transport\MmsiType:Ship";', MmsiStationType::Ship];
        yield 'CoastStation' => ['E:65:"MSSTC4PHP\ValueObject\Identifiers\Transport\MmsiType:CoastStation";', MmsiStationType::CoastStation];
        yield 'Unclassified' => ['E:65:"MSSTC4PHP\ValueObject\Identifiers\Transport\MmsiType:Unclassified";', MmsiStationType::Unclassified];
    }

    #[DataProvider('payloadsOf02')]
    public function testPayloadOf02UnserializesToTheStationType(string $payload, MmsiStationType $expected): void
    {
        self::assertSame($expected, \unserialize($payload));
    }

    /**
     * The old name as a mapping holds it (`enumType: MmsiType::class`); static analysis sees the stub declaration.
     *
     * @return iterable<string, array{class-string<BackedEnum>}>
     */
    public static function oldName(): iterable
    {
        yield 'MmsiType' => [MmsiType::class];
    }

    /**
     * @param class-string<BackedEnum> $oldName
     */
    #[DataProvider('oldName')]
    public function testOldNameIsAnAliasOfTheStationType(string $oldName): void
    {
        self::assertSame(MmsiStationType::GroupShip, $oldName::from('group_ship'));
        self::assertSame(MmsiStationType::Ship, \constant($oldName . '::Ship'));
        self::assertSame(MmsiStationType::class, new ReflectionEnum($oldName)->getName());
        self::assertTrue(\is_a($oldName, MmsiStationType::class, true));
    }

    /**
     * @param class-string<BackedEnum> $oldName
     */
    #[DataProvider('oldName')]
    public function testSerializingWritesTheNewName(string $oldName): void
    {
        self::assertSame(
            'E:64:"MSSTC4PHP\ValueObject\Identifiers\Transport\MmsiStationType:Ship";',
            \serialize($oldName::from('ship')),
        );
    }
}
