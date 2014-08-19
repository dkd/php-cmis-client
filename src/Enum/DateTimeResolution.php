<?php
namespace Dkd\PhpCmis\Enum;

use Eloquent\Enumeration\AbstractEnumeration;

/**
 * Date Time Resolution Enum.
 */
final class DateTimeResolution extends AbstractEnumeration
{
    const DATE = 'date';
    const TIME = 'time';
    const YEAR = 'year';
}
