<?php
namespace Dkd\PhpCmis\Enum;

use Eloquent\Enumeration\AbstractEnumeration;

/**
 * Unfile Object Enum.
 */
final class UnfileObject extends AbstractEnumeration
{
    const DELETE = 'delete';
    const DELETESINGLEFILED = 'deletesinglefiled';
    const UNFILE = 'unfile';
}
