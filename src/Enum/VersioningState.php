<?php
namespace Dkd\PhpCmis\Enum;

use Eloquent\Enumeration\AbstractEnumeration;

/**
 * ACL Propagation Enum.
 */
final class VersioningState extends AbstractEnumeration
{
    const CHECKEDOUT = 'checkedout';
    const MAJOR = 'major';
    const MINOR = 'minor';
    const NONE = 'none';
}
