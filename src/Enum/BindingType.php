<?php
namespace Dkd\PhpCmis\Enum;

use Eloquent\Enumeration\AbstractEnumeration;

/**
 * Binding Type Enum.
 */
final class BindingType extends AbstractEnumeration
{
    const ATOMPUB = 'atompub';
    const BROWSER = 'browser';
    const CUSTOM = 'custom';
    const LOCAL = 'local';
    const WEBSERVICES = 'webservices';
}
