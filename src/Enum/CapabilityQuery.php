<?php
namespace Dkd\PhpCmis\Enum;

use Eloquent\Enumeration\AbstractEnumeration;

/**
 * Capability Enum: Query.
 */
final class CapabilityQuery extends AbstractEnumeration
{
    const BOTHCOMBINED = 'bothcombined';
    const BOTHSEPARATE = 'bothseparate';
    const FULLTEXTONLY = 'fulltextonly';
    const METADATAONLY = 'metadataonly';
    const NONE = 'none';
}
