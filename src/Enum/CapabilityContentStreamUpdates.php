<?php
namespace Dkd\PhpCmis\Enum;

use Eloquent\Enumeration\AbstractEnumeration;

/**
 * Capability Enum: Content Stream Updates.
 */
final class CapabilityContentStreamUpdates extends AbstractEnumeration
{
    const ANYTIME = 'anytime';
    const NONE = 'none';
    const PWCONLY = 'pwconly';
}
