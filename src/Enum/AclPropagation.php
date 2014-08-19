<?php
namespace Dkd\PhpCmis\Enum;

use Eloquent\Enumeration\AbstractEnumeration;

/**
 * ACL Propagation Enum.
 */
final class AclPropagation extends AbstractEnumeration
{
    const OBJECTONLY = 'objectonly';
    const REPOSITORYDETERMINED = 'repositorydetermined';
    const PROPAGATE = 'propagate';
}
