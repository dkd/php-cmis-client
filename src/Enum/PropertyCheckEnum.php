<?php
namespace Dkd\PhpCmis\Enum;

use Eloquent\Enumeration\AbstractEnumeration;

/**
 * Property Check Enum.
 */
final class PropertyCheckEnum extends AbstractEnumeration
{
    const MUST_BE_SET = 'MUST_BE_SET';
    const MUST_NOT_BE_SET = 'MUST_NOT_BE_SET';
    const NO_VALUE_CHECK = 'NO_VALUE_CHECK';
    const STRING_MUST_NOT_BE_EMPTY = 'STRING_MUST_NOT_BE_EMPTY';
    const STRING_SHOULD_NOT_BE_EMPTY = 'STRING_SHOULD_NOT_BE_EMPTY';
}
