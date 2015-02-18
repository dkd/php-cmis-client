<?php
namespace Dkd\PhpCmis\Data;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Accessors to CMIS folder properties.
 */
interface FolderPropertiesInterface
{
    /**
     * Returns the list of the allowed object types in this folder (CMIS property cmis:allowedChildObjectTypeIds).
     * If the list is empty all object types are allowed.
     *
     * @return ObjectTypeInterface[] the property value or empty array if the property hasn't been requested,
     * hasn't been provided by the repository, or the property value isn't set
     */
    public function getAllowedChildObjectTypes();

    /**
     * Returns the parent id or <code>null</code> if the folder is the root folder (CMIS property cmis:parentId).
     *
     * @return string|null the property value or <code>null</code> if the property hasn't been requested, hasn't
     *      been provided by the repository, or the folder is the root folder
     */
    public function getParentId();
}
