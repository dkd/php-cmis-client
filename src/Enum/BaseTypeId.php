<?php
namespace Dkd\PhpCmis\Enum;

use Eloquent\Enumeration\AbstractEnumeration;

/**
 * Base Object Type IDs Enum.
 */
final class BaseTypeId extends AbstractEnumeration
{
    const CMIS_DOCUMENT = 'cmis:document';
    const CMIS_FOLDER = 'cmis:folder';
    const CMIS_ITEM = 'cmis:item';
    const CMIS_POLICY = 'cmis:policy';
    const CMIS_RELATIONSHIP = 'cmis:relationship';
    const CMIS_SECONDARY = 'cmis:secondary';
}
