<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit;

use MSSTC4PHP\ValueObject\Exception\InvalidValueObjectException;
use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidEnergyIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidPublicationIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidSubstanceIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTelecomIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTradeIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Exception\InvalidTransportIdentifierException;
use MSSTC4PHP\ValueObject\Identifiers\Publication\PublicationIdentifierFactory;
use MSSTC4PHP\ValueObject\Identifiers\Telecom\TelecomIdentifierFactory;
use MSSTC4PHP\ValueObject\Identifiers\Transport\TransportIdentifierFactory;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Shapes frozen by the 2.x backward-compatibility promise.
 */
#[CoversNothing]
final class PackageShapeTest extends TestCase
{
    /**
     * @return iterable<string, array{class-string<InvalidValueObjectException>}>
     */
    public static function exceptions(): iterable
    {
        yield 'energy' => [InvalidEnergyIdentifierException::class];
        yield 'publication' => [InvalidPublicationIdentifierException::class];
        yield 'substance' => [InvalidSubstanceIdentifierException::class];
        yield 'telecom' => [InvalidTelecomIdentifierException::class];
        yield 'trade' => [InvalidTradeIdentifierException::class];
        yield 'transport' => [InvalidTransportIdentifierException::class];
    }

    /**
     * @param class-string<InvalidValueObjectException> $class
     */
    #[DataProvider('exceptions')]
    public function testEveryExceptionIsFinalUnderTheBaseException(string $class): void
    {
        $reflection = new ReflectionClass($class);

        self::assertTrue($reflection->isFinal());
        $parent = $reflection->getParentClass();
        self::assertNotFalse($parent);
        self::assertSame(InvalidValueObjectException::class, $parent->getName());
    }

    /**
     * @return iterable<string, array{class-string}>
     */
    public static function factories(): iterable
    {
        yield 'publication' => [PublicationIdentifierFactory::class];
        yield 'telecom' => [TelecomIdentifierFactory::class];
        yield 'transport' => [TransportIdentifierFactory::class];
    }

    /**
     * @param class-string $class
     */
    #[DataProvider('factories')]
    public function testAFactoryCannotBeInstantiated(string $class): void
    {
        $constructor = new ReflectionClass($class)->getConstructor();

        self::assertNotNull($constructor);
        self::assertTrue($constructor->isPrivate());
    }

    public function testThe0xMmsiTypeAliasIsGone(): void
    {
        self::assertFalse(\enum_exists('MSSTC4PHP\\ValueObject\\Identifiers\\Transport\\MmsiType'));
    }
}
