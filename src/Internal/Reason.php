<?php

declare(strict_types=1);

namespace MSSTC4PHP\ValueObject\Identifiers\Internal;

/**
 * The reasons every identifier message ends with, one wording per failure: `<type> <reason>.`.
 *
 * @internal
 */
final class Reason
{
    public const string INVALID_FORMAT = 'has an invalid format';

    public const string WRONG_CHECK_DIGIT = 'has a wrong check digit';

    public const string WRONG_CHECK_CHARACTER = 'has a wrong check character';
}
