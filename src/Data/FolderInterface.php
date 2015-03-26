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
use Dkd\PhpCmis\TreeInterface;
use GuzzleHttp\Stream\StreamInterface;
use Dkd\PhpCmis\Enum\UnfileObject;
use Dkd\PhpCmis\Enum\VersioningState;

/**
 * CMIS folder interface.
 */
interface FolderInterface extends FileableCmisObjectInterface, FolderPropertiesInterface
{
    /**
     * Creates a new document in this folder.
     *
     * @param array $properties The property values that MUST be applied to the object. The array key is the property
     *     name the value is the property value.
     * @param StreamInterface $contentStream
     * @param VersioningState $versioningState An enumeration specifying what the versioning state of the newly-created
     *     object MUST be. Valid values are:
     *      <code>none</code>
     *          (default, if the object-type is not versionable) The document MUST be created as a non-versionable
     *          document.
     *     <code>checkedout</code>
     *          The document MUST be created in the checked-out state. The checked-out document MAY be
     *          visible to other users.
     *     <code>major</code>
     *          (default, if the object-type is versionable) The document MUST be created as a major version.
     *     <code>minor</code>
     *          The document MUST be created as a minor version.
     * @param PolicyInterface[] $policies A list of policy ids that MUST be applied to the newly-created document
     *     object.
     * @param AceInterface[] $addAces A list of ACEs that MUST be added to the newly-created document object, either
     *     using the ACL from folderId if specified, or being applied if no folderId is specified.
     * @param AceInterface[] $removeAces A list of ACEs that MUST be removed from the newly-created document object,
     *     either using the ACL from folderId if specified, or being ignored if no folderId is specified.
     * @param OperationContextInterface|null $context
     * @return DocumentInterface|null the new folder object or <code>null</code> if the parameter <code>context</code>
     *     was set to <code>null</code>
     */
    public function createDocument(
        array $properties,
        StreamInterface $contentStream,
        VersioningState $versioningState,
        array $policies = array(),
        array $addAces = array(),
        array $removeAces = array(),
        OperationContextInterface $context = null
    );

    /**
     * Creates a new document from a source document in this folder.
     *
     * @param ObjectIdInterface $source The ID of the source document.
     * @param array $properties The property values that MUST be applied to the object. The array key is the property
     *     name the value is the property value.
     * @param VersioningState $versioningState An enumeration specifying what the versioning state of the newly-created
     *     object MUST be. Valid values are:
     *      <code>none</code>
     *          (default, if the object-type is not versionable) The document MUST be created as a non-versionable
     *          document.
     *     <code>checkedout</code>
     *          The document MUST be created in the checked-out state. The checked-out document MAY be
     *          visible to other users.
     *     <code>major</code>
     *          (default, if the object-type is versionable) The document MUST be created as a major version.
     *     <code>minor</code>
     *          The document MUST be created as a minor version.
     * @param PolicyInterface[] $policies A list of policy ids that MUST be applied to the newly-created document
     *     object.
     * @param AceInterface[] $addAces A list of ACEs that MUST be added to the newly-created document object, either
     *     using the ACL from folderId if specified, or being applied if no folderId is specified.
     * @param AceInterface[] $removeAces A list of ACEs that MUST be removed from the newly-created document object,
     *     either using the ACL from folderId if specified, or being ignored if no folderId is specified.
     * @param OperationContextInterface|null $context
     * @return DocumentInterface|null the new folder object or <code>null</code> if the parameter <code>context</code>
     *     was set to <code>null</code>
     */
    public function createDocumentFromSource(
        ObjectIdInterface $source,
        array $properties,
        VersioningState $versioningState,
        array $policies = array(),
        array $addAces = array(),
        array $removeAces = array(),
        OperationContextInterface $context = null
    );

    /**
     * Creates a new subfolder in this folder.
     *
     * @param array $properties The property values that MUST be applied to the newly-created item object.
     * @param PolicyInterface[] $policies A list of policy ids that MUST be applied to the newly-created folder object.
     * @param AceInterface[] $addAces A list of ACEs that MUST be added to the newly-created folder object, either
     *     using the ACL from folderId if specified, or being applied if no folderId is specified.
     * @param AceInterface[] $removeAces A list of ACEs that MUST be removed from the newly-created folder object,
     *     either using the ACL from folderId if specified, or being ignored if no folderId is specified.
     * @param OperationContextInterface|null $context
     * @return FolderInterface|null the new folder object or <code>null</code> if the parameter <code>context</code>
     *     was set to <code>null</code>
     */
    public function createFolder(
        array $properties,
        array $policies = array(),
        array $addAces = array(),
        array $removeAces = array(),
        OperationContextInterface $context = null
    );

    /**
     * Creates a new item in this folder.
     *
     * @param array $properties The property values that MUST be applied to the newly-created item object.
     * @param PolicyInterface[] $policies A list of policy ids that MUST be applied to the newly-created item object.
     * @param AceInterface[] $addAces A list of ACEs that MUST be added to the newly-created item object, either using
     *     the ACL from folderId if specified, or being applied if no folderId is specified.
     * @param AceInterface[] $removeAces A list of ACEs that MUST be removed from the newly-created item object, either
     *     using the ACL from folderId if specified, or being ignored if no folderId is specified.
     * @param OperationContextInterface|null $context
     * @return ItemInterface|null the new item object
     */
    public function createItem(
        array $properties,
        array $policies = array(),
        array $addAces = array(),
        array $removeAces = array(),
        OperationContextInterface $context = null
    );

    /**
     * Creates a new policy in this folder.
     *
     * @param array $properties The property values that MUST be applied to the newly-created policy object.
     * @param PolicyInterface[] $policies A list of policy ids that MUST be applied to the newly-created policy object.
     * @param AceInterface[] $addAces A list of ACEs that MUST be added to the newly-created policy object, either
     *     using the ACL from folderId if specified, or being applied if no folderId is specified.
     * @param AceInterface[] $removeAces A list of ACEs that MUST be removed from the newly-created policy object,
     *     either using the ACL from folderId if specified, or being ignored if no folderId is specified.
     * @param OperationContextInterface|null $context
     * @return PolicyInterface|null the new policy object
     */
    public function createPolicy(
        array $properties,
        array $policies = array(),
        array $addAces = array(),
        array $removeAces = array(),
        OperationContextInterface $context = null
    );

    /**
     * Deletes this folder and all subfolders.
     *
     * @param boolean $allVersions If <code>true</code>, then delete all versions of all documents. If
     *     <code>false</code>, delete only the document versions referenced in the tree. The repository MUST ignore the
     *     value of this parameter when this service is invoked on any non-document objects or non-versionable document
     *     objects.
     * @param UnfileObject $unfile An enumeration specifying how the repository MUST process file-able child- or
     *     descendant-objects.
     * @param boolean $continueOnFailure If <code>true</code>, then the repository SHOULD continue attempting to
     *     perform this operation even if deletion of a child- or descendant-object in the specified folder cannot be
     *     deleted. If <code>false</code>, then the repository SHOULD abort this method when it fails to
     *     delete a single child object or descendant object.
     * @return FailedToDeleteDataInterface A list of identifiers of objects in the folder tree that were not deleted.
     */
    public function deleteTree($allVersions, UnfileObject $unfile, $continueOnFailure = true);

    /**
     * Returns all checked out documents in this folder using the given OperationContext.
     *
     * @param OperationContextInterface|null $context
     * @return DocumentInterface[] A list of checked out documents.
     */
    public function getCheckedOutDocs(OperationContextInterface $context = null);

    /**
     * Returns the children of this folder using the given OperationContext.
     *
     * @param OperationContextInterface|null $context
     * @return CmisObjectInterface[] A list of the child objects for the specified folder.
     */
    public function getChildren(OperationContextInterface $context = null);

    /**
     * Gets the folder descendants starting with this folder.
     *
     * @param integer $depth
     * @param OperationContextInterface|null $context
     * @return TreeInterface A tree that contains FileableCmisObject objects
     * @see FileableCmisObject FileableCmisObject contained in returned TreeInterface
     */
    public function getDescendants($depth, OperationContextInterface $context = null);

    /**
     * Gets the parent folder object.
     *
     * @return FolderInterface|null the parent folder object or <code>null</code> if the folder is the root folder.
     */
    public function getFolderParent();

    /**
     * Gets the folder tree starting with this folder using the given OperationContext.
     *
     * @param integer $depth The number of levels of depth in the folder hierarchy from which to return results.
     *    Valid values are:
     *    1
     *      Return only objects that are children of the folder. See also getChildren.
     *    <Integer value greater than 1>
     *      Return only objects that are children of the folder and descendants up to <value> levels deep.
     *    -1
     *      Return ALL descendant objects at all depth levels in the CMIS hierarchy.
     *      The default value is repository specific and SHOULD be at least 2 or -1.
     * @param OperationContextInterface|null $context
     * @return TreeInterface A tree that contains FileableCmisObject objects
     * @see FileableCmisObject FileableCmisObject contained in returned TreeInterface
     */
    public function getFolderTree($depth, OperationContextInterface $context = null);

    /**
     * Returns the path of the folder.
     *
     * @return string the absolute folder path
     */
    public function getPath();

    /**
     * Returns if the folder is the root folder.
     *
     * @return boolean <code>true</code> if the folder is the root folder, <code>false</code> otherwise
     */
    public function isRootFolder();
}
