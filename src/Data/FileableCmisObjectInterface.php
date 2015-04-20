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

use Dkd\PhpCmis\CmisObject\CmisObjectInterface;
use Dkd\PhpCmis\OperationContextInterface;

/**
 * Fileable CMIS object. A fileable object is an object that can reside in a folder.
 */
interface FileableCmisObjectInterface extends CmisObjectInterface
{
    /**
     * Adds this object to a folder.
     *
     * @param ObjectIdInterface $folderId
     * @param boolean $allVersions
     */
    public function addToFolder(ObjectIdInterface $folderId, $allVersions);

    /**
     * Returns the parents of this object.
     *
     * @param OperationContextInterface|null $context the OperationContext to use to fetch the parent folder objects
     * @return FolderInterface[] the list of parent folders of this object or an
     * empty list if this object is unfiled or if this object is the root folder
     */
    public function getParents(OperationContextInterface $context = null);

    /**
     * Returns the paths of this object.
     *
     * @return string[] the list of paths of this object or an empty list if this object is
     * unfiled or if this object is the root folder
     */
    public function getPaths();

    /**
     * Moves this object.
     *
     * @param ObjectIdInterface $sourceFolderId the object ID of the source folder
     * @param ObjectIdInterface $targetFolderId the object ID of the target folder
     * @param OperationContextInterface|null $context the OperationContext to use to fetch the moved object
     *
     * @return FileableCmisObjectInterface the moved object
     */
    public function move(
        ObjectIdInterface $sourceFolderId,
        ObjectIdInterface $targetFolderId,
        OperationContextInterface $context = null
    );

    /**
     * Removes this object from a folder.
     *
     * @param ObjectIdInterface $folderId the object ID of the folder from which this object should be removed
     */
    public function removeFromFolder(ObjectIdInterface $folderId);
}
