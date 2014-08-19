<?php
namespace Dkd\PhpCmis\Enum;

use Eloquent\Enumeration\AbstractEnumeration;

/**
 * Capability Enum: Join.
 */
final class CapabilityJoin extends AbstractEnumeration
{
    const INNERANDOUTER = 'innerandouter';
    const INNERONLY = 'inneronly';
    const NONE = 'none';
}
