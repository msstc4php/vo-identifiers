<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Transport;

use MSSTC4PHP\ValueObject\Identifiers\Internal\TransportRules;
use MSSTC4PHP\ValueObject\Support\AbstractStringValueObject;
use Override;

/**
 * IMO ship identification number: the letters `IMO` and seven digits, the seventh being a check digit that is not
 * verified (no IMO text gives the weights). The prefix is optional on input; the stored form is `IMO` plus the digits.
 *
 * @see https://wwwcdn.imo.org/localresources/en/KnowledgeCentre/IndexofIMOResolutions/AssemblyDocuments/A.1215(34).pdf IMO Assembly Resolution A.1215(34), paragraph 5
 * @see https://github.com/msstc4php/vo-identifiers/blob/main/docs/sources/IDENTIFIERS.md "ImoNumber"
 */
final class ImoNumber extends AbstractStringValueObject
{
    /** Characters of the longest stored form. */
    public const int MAX_LENGTH = 10;

    private const string TYPE = 'IMO number';

    /**
     * @return string the seven digits
     */
    public function number(): string
    {
        return \substr($this->value, 3);
    }

    /**
     * @return non-empty-string `IMO 8712345`
     */
    public function formatted(): string
    {
        return 'IMO ' . $this->number();
    }

    #[Override]
    protected function parse(string $value): string
    {
        $value = TransportRules::compact($value);

        if (\preg_match('/^(?:IMO)?([0-9]{7})$/D', $value, $match) !== 1) {
            throw TransportRules::invalidFormat(self::TYPE);
        }

        return 'IMO' . $match[1];
    }
}
