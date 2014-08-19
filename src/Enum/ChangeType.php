<?php
namespace Dkd\PhpCmis\Enum;

use Eloquent\Enumeration\AbstractEnumeration;

/**
 * Change type Enum.
 */
final class ChangeType extends AbstractEnumeration
{
    const CREATED = 'created';
    const DELETED = 'deleted';
    const SECURITY = 'security';
    const UPDATED = 'updated';
}
