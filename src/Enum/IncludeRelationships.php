<?php
namespace Dkd\PhpCmis\Enum;

use Eloquent\Enumeration\AbstractEnumeration;

/**
 * Include Relationships Enum.
 */
final class IncludeRelationships extends AbstractEnumeration
{
    const NONE = 'none';
    const SOURCE = 'source';
    const TARGET = 'target';
    const BOTH = 'both';
}
