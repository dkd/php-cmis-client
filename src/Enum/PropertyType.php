<?php
namespace Dkd\PhpCmis\Enum;

use Eloquent\Enumeration\AbstractEnumeration;

/**
 * Property Type Enum.
 */
final class PropertyType extends AbstractEnumeration
{
    const BOOLEAN = 'boolean';
    const DATETIME = 'datetime';
    const DECIMAL = 'decimal';
    const HTML = 'html';
    const ID = 'id';
    const INTEGER = 'integer';
    const STRING = 'string';
    const URI = 'uri';
}
