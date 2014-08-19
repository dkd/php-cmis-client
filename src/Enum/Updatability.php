<?php
namespace Dkd\PhpCmis\Enum;

use Eloquent\Enumeration\AbstractEnumeration;

/**
 * Updatability Enum.
 */
final class Updatability extends AbstractEnumeration
{
    const ONCREATE = 'oncreate';
    const READONLY = 'readonly';
    const READWRITE = 'readwrite';
    const WHENCHECKEDOUT = 'whencheckedout';
}
