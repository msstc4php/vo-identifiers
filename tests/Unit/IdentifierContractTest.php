<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Tests\Unit;

use FilesystemIterator;
use LogicException;
use MSSTC4PHP\ValueObject\Contract\StringConvertible;
use MSSTC4PHP\ValueObject\Contract\ValueObject;
use MSSTC4PHP\ValueObject\Exception\InvalidValueObjectException;
use MSSTC4PHP\ValueObject\Identifiers\Energy\EnergyIdentificationCode;
use MSSTC4PHP\ValueObject\Identifiers\Energy\Mpan;
use MSSTC4PHP\ValueObject\Identifiers\Internal\AirWaybillCheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\CasCheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\CommodityRules;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Ean13CheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\EcNumberCheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\EicCheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Iso7064Mod11_2;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Iso7064Mod37_36;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Iso7064Mod97;
use MSSTC4PHP\ValueObject\Identifiers\Internal\IssuingAgencyRegister;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Luhn;
use MSSTC4PHP\ValueObject\Identifiers\Internal\Mod11CheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\PublicationRules;
use MSSTC4PHP\ValueObject\Identifiers\Internal\RorCheckDigits;
use MSSTC4PHP\ValueObject\Identifiers\Internal\S10CheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TelecomRules;
use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Identifiers\Internal\VinCheckDigit;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Ark;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Doi;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Isan;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Isbn;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Ismn;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Isni;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Isrc;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Issn;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Iswc;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Orcid;
use MSSTC4PHP\ValueObject\Identifiers\Publication\Ror;
use MSSTC4PHP\ValueObject\Identifiers\Substance\CasRegistryNumber;
use MSSTC4PHP\ValueObject\Identifiers\Substance\EcNumber;
use MSSTC4PHP\ValueObject\Identifiers\Substance\UnNumber;
use MSSTC4PHP\ValueObject\Identifiers\Telecom\Eid;
use MSSTC4PHP\ValueObject\Identifiers\Telecom\Iccid;
use MSSTC4PHP\ValueObject\Identifiers\Telecom\Imei;
use MSSTC4PHP\ValueObject\Identifiers\Telecom\Imeisv;
use MSSTC4PHP\ValueObject\Identifiers\Telecom\Imsi;
use MSSTC4PHP\ValueObject\Identifiers\Trade\HsCode;
use MSSTC4PHP\ValueObject\Identifiers\Trade\TariffCode;
use MSSTC4PHP\ValueObject\Identifiers\Transport\AirWaybillNumber;
use MSSTC4PHP\ValueObject\Identifiers\Transport\ContainerNumber;
use MSSTC4PHP\ValueObject\Identifiers\Transport\DataIdentifierField;
use MSSTC4PHP\ValueObject\Identifiers\Transport\DataIdentifierString;
use MSSTC4PHP\ValueObject\Identifiers\Transport\IataAirportCode;
use MSSTC4PHP\ValueObject\Identifiers\Transport\IcaoAirportCode;
use MSSTC4PHP\ValueObject\Identifiers\Transport\ImoNumber;
use MSSTC4PHP\ValueObject\Identifiers\Transport\Iso15459Identifier;
use MSSTC4PHP\ValueObject\Identifiers\Transport\Mmsi;
use MSSTC4PHP\ValueObject\Identifiers\Transport\MmsiType;
use MSSTC4PHP\ValueObject\Identifiers\Transport\S10ItemIdentifier;
use MSSTC4PHP\ValueObject\Identifiers\Transport\UicVehicleNumber;
use MSSTC4PHP\ValueObject\Identifiers\Transport\UnLocode;
use MSSTC4PHP\ValueObject\Identifiers\Transport\Vin;
use MSSTC4PHP\ValueObject\Identifiers\Transport\Wmi;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use SplFileInfo;

/**
 * Every identifier value object: string and JSON round trips, equality, serialization that validates again, and the
 * `MAX_LENGTH` the Doctrine columns are sized from.
 */
#[CoversClass(Ark::class)]
#[CoversClass(Doi::class)]
#[CoversClass(Isan::class)]
#[CoversClass(Isbn::class)]
#[CoversClass(Ismn::class)]
#[CoversClass(Isni::class)]
#[CoversClass(Isrc::class)]
#[CoversClass(Issn::class)]
#[CoversClass(Iswc::class)]
#[CoversClass(Orcid::class)]
#[CoversClass(Ror::class)]
#[CoversClass(AirWaybillNumber::class)]
#[CoversClass(ContainerNumber::class)]
#[CoversClass(DataIdentifierField::class)]
#[CoversClass(DataIdentifierString::class)]
#[CoversClass(IataAirportCode::class)]
#[CoversClass(IcaoAirportCode::class)]
#[CoversClass(ImoNumber::class)]
#[CoversClass(Iso15459Identifier::class)]
#[CoversClass(Mmsi::class)]
#[CoversClass(S10ItemIdentifier::class)]
#[CoversClass(UicVehicleNumber::class)]
#[CoversClass(UnLocode::class)]
#[CoversClass(Vin::class)]
#[CoversClass(Wmi::class)]
#[CoversClass(Eid::class)]
#[CoversClass(Iccid::class)]
#[CoversClass(Imei::class)]
#[CoversClass(Imeisv::class)]
#[CoversClass(Imsi::class)]
#[CoversClass(CasRegistryNumber::class)]
#[CoversClass(EcNumber::class)]
#[CoversClass(UnNumber::class)]
#[CoversClass(HsCode::class)]
#[CoversClass(TariffCode::class)]
#[CoversClass(EnergyIdentificationCode::class)]
#[CoversClass(Mpan::class)]
#[UsesClass(AirWaybillCheckDigit::class)]
#[UsesClass(CasCheckDigit::class)]
#[UsesClass(CommodityRules::class)]
#[UsesClass(Ean13CheckDigit::class)]
#[UsesClass(EcNumberCheckDigit::class)]
#[UsesClass(EicCheckDigit::class)]
#[UsesClass(Iso7064Mod11_2::class)]
#[UsesClass(Iso7064Mod37_36::class)]
#[UsesClass(Iso7064Mod97::class)]
#[UsesClass(IssuingAgencyRegister::class)]
#[UsesClass(Luhn::class)]
#[UsesClass(Mod11CheckDigit::class)]
#[UsesClass(PublicationRules::class)]
#[UsesClass(RorCheckDigits::class)]
#[UsesClass(S10CheckDigit::class)]
#[UsesClass(TelecomRules::class)]
#[UsesClass(TransportRules::class)]
#[UsesClass(VinCheckDigit::class)]
final class IdentifierContractTest extends TestCase
{
    private const string NOT_AN_IDENTIFIER = '!';

    /**
     * Per class: an input in a non-canonical form where the class normalises, its canonical form, and another valid
     * value.
     *
     * @return iterable<string, array{class-string<StringConvertible&ValueObject>, string, string, string}>
     */
    public static function identifiers(): iterable
    {
        yield 'Ark' => [Ark::class, 'ark:/12025/654xz321', 'ark:12025/654xz321', 'ark:b3c4d/x'];
        yield 'Doi' => [Doi::class, 'doi:10.1000/xyz-123', '10.1000/xyz-123', '10.1000/123456'];
        yield 'Isan' => [Isan::class, 'ISAN B159-D8FA-0124-0000-K', 'B159D8FA01240000K', 'B159D8FA01240000K9F3A02459'];
        yield 'Isbn' => [Isbn::class, '0-306-40615-2', '0306406152', '0843610727'];
        yield 'Ismn' => [Ismn::class, 'M-3452-4680-5', '9790345246805', '9790345246812'];
        yield 'Isni' => [Isni::class, 'ISNI 1422 4586 3573 0476', '1422458635730476', '000000021694233X'];
        yield 'Isrc' => [Isrc::class, 'AA-6QZ-20-00047', 'AA6QZ2000047', 'AA6QZ2000048'];
        yield 'Issn' => [Issn::class, '03178471', '0317-8471', '0378-5955'];
        yield 'Iswc' => [Iswc::class, 'T-034.524.680-1', 'T0345246801', 'T0345246802'];
        yield 'Orcid' => [Orcid::class, 'https://orcid.org/0000-0002-1825-0097', '0000-0002-1825-0097', '0000-0001-5109-3700'];
        yield 'Ror' => [Ror::class, 'https://ror.org/02mhbdp94', '02mhbdp94', '01an7q238'];
        yield 'AirWaybillNumber' => [AirWaybillNumber::class, '020-12345675', '02012345675', '02012345686'];
        yield 'ContainerNumber' => [ContainerNumber::class, 'CSQU 305438 3', 'CSQU3054383', 'ABCJ1234560'];
        yield 'DataIdentifierField' => [DataIdentifierField::class, '25SLN12345', '25SLN12345', '1PABC-1'];
        yield 'DataIdentifierString' => [
            DataIdentifierString::class,
            "[)>\x1E06\x1D25SLN12345\x1DS000001\x1E\x04",
            "[)>\x1E06\x1D25SLN12345\x1DS000001\x1E\x04",
            "[)>\x1E06\x1D1PABC-1\x1E\x04",
        ];
        yield 'IataAirportCode' => [IataAirportCode::class, 'syd', 'SYD', 'LHR'];
        yield 'IcaoAirportCode' => [IcaoAirportCode::class, 'eddf', 'EDDF', 'EGLL'];
        yield 'ImoNumber' => [ImoNumber::class, '8712345', 'IMO8712345', 'IMO9074729'];
        yield 'Iso15459Identifier' => [Iso15459Identifier::class, 'LN12345', 'LN12345', 'VBS12345'];
        yield 'Mmsi' => [Mmsi::class, '366123456', '366123456', '211123456'];
        yield 'S10ItemIdentifier' => [S10ItemIdentifier::class, 'ra473124829ch', 'RA473124829CH', 'RB473124829CH'];
        yield 'UicVehicleNumber' => [UicVehicleNumber::class, '33 84 4796 100-8', '338447961008', '315133201980'];
        yield 'UnLocode' => [UnLocode::class, 'deham', 'DEHAM', 'GBLON'];
        yield 'Vin' => [Vin::class, '1g4ah59h45g118341', '1G4AH59H45G118341', '1M8GDM9AXKP042788'];
        yield 'Wmi' => [Wmi::class, '1g4', '1G4', '1M8'];
        yield 'Eid' => [Eid::class, '35209900176148100000000000000078', '35209900176148100000000000000078', '89000000000000000000000000000002'];
        yield 'Iccid' => [Iccid::class, '89444444444444444440', '89444444444444444440', '893339'];
        yield 'Imei' => [Imei::class, '49-015420-323751-8', '490154203237518', '35209900176148'];
        yield 'Imeisv' => [Imeisv::class, '2605317931138301', '2605317931138301', '3520990017614801'];
        yield 'Imsi' => [Imsi::class, '310150123456789', '310150123456789', '310150123456788'];
        yield 'CasRegistryNumber' => [CasRegistryNumber::class, '0050-00-0', '50-00-0', '7732-18-5'];
        yield 'EcNumber' => [EcNumber::class, '1000001', '100-000-1', 'A00-001-5'];
        yield 'UnNumber' => [UnNumber::class, '1203', 'UN1203', 'UN1090'];
        yield 'HsCode' => [HsCode::class, '0101.21', '010121', '010129'];
        yield 'TariffCode' => [TariffCode::class, '0101 21 00 10', '0101210010', '01012100'];
        yield 'EnergyIdentificationCode' => [EnergyIdentificationCode::class, '11xrwenet12345-2', '11XRWENET12345-2', '11Y123456789012T'];
        yield 'Mpan' => [Mpan::class, '123456789012345678901', '123456789012345678901', '1234567890123'];
    }

    public function testEveryValueObjectOfThePackageIsListed(): void
    {
        $listed = [];
        foreach (self::identifiers() as [$class]) {
            $listed[] = $class;
        }

        \sort($listed);

        self::assertSame($this->valueObjectClasses(), $listed);
    }

    /**
     * @param class-string<StringConvertible&ValueObject> $class
     */
    #[DataProvider('identifiers')]
    public function testTheCanonicalFormRoundTripsAsStringAndJson(string $class, string $input, string $canonical, string $other): void
    {
        $identifier = $class::from($input);
        $again = $class::from((string) $identifier);

        self::assertSame($canonical, (string) $identifier);
        self::assertSame($canonical, (string) $again);
        self::assertTrue($again->equals($identifier));
        self::assertTrue($identifier->equals($again));
        self::assertSame(\json_encode($canonical, \JSON_THROW_ON_ERROR), \json_encode($identifier, \JSON_THROW_ON_ERROR));
        self::assertNotSame($canonical, (string) $class::from($other));
    }

    /**
     * @param class-string<StringConvertible&ValueObject> $class
     */
    #[DataProvider('identifiers')]
    public function testEqualsIsFalseForAnotherValueAndAnotherType(string $class, string $input, string $canonical, string $other): void
    {
        $identifier = $class::from($input);
        $foreign = $class === Mmsi::class ? Imsi::from('366123456') : Mmsi::from('366123456');

        self::assertFalse($identifier->equals($class::from($other)));
        self::assertFalse($class::from($other)->equals($identifier));
        self::assertFalse($identifier->equals($foreign));
    }

    /**
     * @param class-string<StringConvertible&ValueObject> $class
     */
    #[DataProvider('identifiers')]
    public function testSerializesAsItsValueAlone(string $class, string $input, string $canonical, string $other): void
    {
        $identifier = $class::from($input);

        $serialized = \serialize($identifier);
        $restored = \unserialize($serialized);

        self::assertSame(\sprintf('O:%d:"%s":1:{s:5:"value";%s}', \strlen($class), $class, \serialize($canonical)), $serialized);
        self::assertInstanceOf($class, $restored);
        self::assertTrue($identifier->equals($restored));
        self::assertSame($canonical, (string) $restored);
        $otherRestored = \unserialize(\serialize($class::from($other)));
        self::assertInstanceOf($class, $otherRestored);
        self::assertSame((string) $class::from($other), (string) $otherRestored);
    }

    /**
     * @return iterable<string, array{class-string<StringConvertible&ValueObject>}>
     */
    public static function classes(): iterable
    {
        foreach (self::identifiers() as $label => [$class]) {
            yield $label => [$class];
        }
    }

    /**
     * @param class-string<StringConvertible&ValueObject> $class
     */
    #[DataProvider('classes')]
    public function testAForgedValueIsValidatedAgain(string $class): void
    {
        $this->expectException(InvalidValueObjectException::class);

        \unserialize($this->payload($class, ['value' => self::NOT_AN_IDENTIFIER]));
    }

    /**
     * @param class-string<StringConvertible&ValueObject> $class
     */
    #[DataProvider('identifiers')]
    public function testOtherKeysOfThePayloadAreIgnored(string $class, string $input, string $canonical, string $other): void
    {
        $restored = \unserialize($this->payload($class, ['value' => $canonical, 'data' => $other, 'dataIdentifier' => '9Z', 'parsedFields' => []]));

        self::assertInstanceOf($class, $restored);
        self::assertSame($canonical, (string) $restored);
        self::assertTrue($class::from($canonical)->equals($restored));
    }

    /**
     * @return iterable<string, array{class-string<StringConvertible&ValueObject>, array<array-key, mixed>}>
     */
    public static function malformedPayloads(): iterable
    {
        foreach (self::identifiers() as $label => [$class, , $canonical]) {
            yield $label . ', no value' => [$class, ['data' => $canonical]];
            yield $label . ', a value that is no string' => [$class, ['value' => 1]];
        }
    }

    /**
     * @param class-string<StringConvertible&ValueObject> $class
     * @param array<array-key, mixed> $data
     */
    #[DataProvider('malformedPayloads')]
    public function testAPayloadWithoutAStringValueIsRefused(string $class, array $data): void
    {
        $this->expectException(InvalidValueObjectException::class);
        $this->expectExceptionMessage('A serialized ' . $class . ' must hold its value as a string.');

        \unserialize($this->payload($class, $data));
    }

    /**
     * @param class-string<StringConvertible&ValueObject> $class
     */
    #[DataProvider('identifiers')]
    public function testABuiltObjectRefusesUnserializeAndKeepsItsValue(string $class, string $input, string $canonical, string $other): void
    {
        $identifier = $class::from($input);
        self::assertTrue(\method_exists($identifier, '__unserialize'));

        try {
            $identifier->__unserialize(['value' => $other]);
            self::fail('__unserialize() on a built object must throw.');
        } catch (LogicException $e) {
            self::assertSame('A value object is immutable; __unserialize() runs only on a new object.', $e->getMessage());
        }

        self::assertSame($canonical, (string) $identifier);
    }

    /**
     * Per class with a bounded length: a value of the longest stored form.
     *
     * @return iterable<string, array{class-string<StringConvertible&ValueObject>, non-empty-string}>
     */
    public static function longest(): iterable
    {
        yield 'Isan' => [Isan::class, 'B159D8FA01240000K9F3A02459'];
        yield 'Isbn' => [Isbn::class, '9780306406157'];
        yield 'Ismn' => [Ismn::class, '9790345246805'];
        yield 'Isni' => [Isni::class, '1422458635730476'];
        yield 'Isrc' => [Isrc::class, 'AA6QZ2000047'];
        yield 'Issn' => [Issn::class, '0317-8471'];
        yield 'Iswc' => [Iswc::class, 'T0345246801'];
        yield 'Orcid' => [Orcid::class, '0000-0002-1825-0097'];
        yield 'Ror' => [Ror::class, '02mhbdp94'];
        yield 'AirWaybillNumber' => [AirWaybillNumber::class, '02012345675'];
        yield 'ContainerNumber' => [ContainerNumber::class, 'CSQU3054383'];
        yield 'IataAirportCode' => [IataAirportCode::class, 'SYD'];
        yield 'IcaoAirportCode' => [IcaoAirportCode::class, 'EDDF'];
        yield 'ImoNumber' => [ImoNumber::class, 'IMO8712345'];
        yield 'Mmsi' => [Mmsi::class, '366123456'];
        yield 'S10ItemIdentifier' => [S10ItemIdentifier::class, 'RA473124829CH'];
        yield 'UicVehicleNumber' => [UicVehicleNumber::class, '338447961008'];
        yield 'UnLocode' => [UnLocode::class, 'DEHAM'];
        yield 'Vin' => [Vin::class, '1G4AH59H45G118341'];
        yield 'Wmi' => [Wmi::class, '1G4'];
        yield 'Eid' => [Eid::class, '35209900176148100000000000000078'];
        yield 'Iccid' => [Iccid::class, '89444444444444444440'];
        yield 'Imei' => [Imei::class, '490154203237518'];
        yield 'Imeisv' => [Imeisv::class, '2605317931138301'];
        yield 'Imsi' => [Imsi::class, '310150123456789'];
        yield 'CasRegistryNumber' => [CasRegistryNumber::class, '1234567-89-5'];
        yield 'EcNumber' => [EcNumber::class, '100-000-1'];
        yield 'UnNumber' => [UnNumber::class, 'UN1203'];
        yield 'HsCode' => [HsCode::class, '010121'];
        yield 'TariffCode' => [TariffCode::class, '0101210010'];
        yield 'EnergyIdentificationCode' => [EnergyIdentificationCode::class, '11XRWENET12345-2'];
        yield 'Mpan' => [Mpan::class, '123456789012345678901'];
    }

    public function testEveryClassWithAMaxLengthHasALongestSample(): void
    {
        $bounded = \array_values(\array_filter(
            $this->valueObjectClasses(),
            static fn (string $class): bool => new ReflectionClass($class)->hasConstant('MAX_LENGTH'),
        ));
        $listed = [];
        foreach (self::longest() as [$class]) {
            $listed[] = $class;
        }

        \sort($listed);

        self::assertSame($bounded, $listed);
    }

    /**
     * @param class-string<StringConvertible&ValueObject> $class
     * @param non-empty-string $value
     */
    #[DataProvider('longest')]
    public function testTheLongestFormHasMaxLengthCharacters(string $class, string $value): void
    {
        $maxLength = new ReflectionClass($class)->getConstant('MAX_LENGTH');

        self::assertSame($maxLength, \strlen((string) $class::from($value)));
    }

    /**
     * The refusal must come from the format, not from a check digit that the extra character happens to break.
     *
     * @param class-string<StringConvertible&ValueObject> $class
     * @param non-empty-string $value
     */
    #[DataProvider('longest')]
    public function testOneCharacterMoreIsAFormatError(string $class, string $value): void
    {
        try {
            $class::from($value . \substr($value, -1));
            self::fail('A value longer than MAX_LENGTH was accepted.');
        } catch (InvalidValueObjectException $e) {
            self::assertStringEndsWith(' has an invalid format.', $e->getMessage());
            self::assertStringNotContainsString((string) (\strlen($value) + 1), $e->getMessage());
        }
    }

    /**
     * No message carries the input or its length: an over-long value gets the same message as a one-character one.
     *
     * @param class-string<StringConvertible&ValueObject> $class
     */
    #[DataProvider('classes')]
    public function testTheMessageForAnOverLongValueHoldsNoPartOfIt(string $class): void
    {
        $long = \str_repeat('9', 4321) . self::NOT_AN_IDENTIFIER;
        $message = $this->messageFor($class, $long);

        self::assertSame($this->messageFor($class, self::NOT_AN_IDENTIFIER), $message);
        self::assertStringNotContainsString('4321', $message);
        self::assertStringNotContainsString('4322', $message);
        self::assertStringNotContainsString('99', $message);
    }

    /**
     * @param class-string<StringConvertible&ValueObject> $class
     */
    private function messageFor(string $class, string $value): string
    {
        try {
            $class::from($value);
        } catch (InvalidValueObjectException $e) {
            return $e->getMessage();
        }

        self::fail('The value was accepted.');
    }

    /**
     * @param class-string $class
     * @param array<array-key, mixed> $data
     */
    private function payload(string $class, array $data): string
    {
        return \sprintf('O:%d:"%s":%d:{%s}', \strlen($class), $class, \count($data), \substr(\serialize($data), \strlen(\sprintf('a:%d:{', \count($data))), -1));
    }

    /**
     * @return list<class-string> the non-abstract value objects outside Internal, sorted
     */
    private function valueObjectClasses(): array
    {
        $file = new ReflectionClass(Isbn::class)->getFileName();
        self::assertIsString($file);
        $source = \dirname($file, 2);
        $classes = [];
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS)) as $entry) {
            self::assertInstanceOf(SplFileInfo::class, $entry);
            $class = 'MSSTC4PHP\\ValueObject\\Identifiers\\' . \str_replace('/', '\\', \substr($entry->getPathname(), \strlen($source) + 1, -4));
            // Loading the alias would run its file-level code under this test's coverage attributes; MmsiTypeTest covers it.
            if (\str_contains($class, '\\Internal\\') || $class === MmsiType::class || !\class_exists($class)) {
                continue;
            }

            $reflection = new ReflectionClass($class);
            if ($reflection->isAbstract() || !$reflection->implementsInterface(ValueObject::class)) {
                continue;
            }

            self::assertTrue($reflection->implementsInterface(StringConvertible::class), $class);
            $classes[] = $class;
        }

        \sort($classes);

        return $classes;
    }
}
