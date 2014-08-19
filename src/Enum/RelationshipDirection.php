<?php
namespace Dkd\PhpCmis\Enum;

use Eloquent\Enumeration\AbstractEnumeration;

/**
 * Relationship Direction Enum.
 */
final class RelationshipDirection extends AbstractEnumeration
{
    const EITHER = 'either';
    const SOURCE = 'source';
    const TARGET = 'target';
}
