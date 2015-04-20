<?php
namespace Dkd\PhpCmis\DataObjects;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\CmisObject\CmisObjectInterface;
use Dkd\PhpCmis\Constants;
use Dkd\PhpCmis\Data\AceInterface;
use Dkd\PhpCmis\Data\DocumentInterface;
use Dkd\PhpCmis\Data\FailedToDeleteDataInterface;
use Dkd\PhpCmis\Data\FolderInterface;
use Dkd\PhpCmis\Data\ItemInterface;
use Dkd\PhpCmis\Data\ObjectIdInterface;
use Dkd\PhpCmis\Data\ObjectInFolderContainerInterface;
use Dkd\PhpCmis\Data\ObjectTypeInterface;
use Dkd\PhpCmis\Data\PolicyInterface;
use Dkd\PhpCmis\Enum\IncludeRelationships;
use Dkd\PhpCmis\Enum\UnfileObject;
use Dkd\PhpCmis\Enum\VersioningState;
use Dkd\PhpCmis\Exception\CmisRuntimeException;
use Dkd\PhpCmis\OperationContextInterface;
use Dkd\PhpCmis\PropertyIds;
use Dkd\PhpCmis\TreeInterface;
use GuzzleHttp\Stream\StreamInterface;

/**
 * Cmis folder implementation
 */
class Folder extends AbstractFileableCmisObject implements FolderInterface
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
     * @throws CmisRuntimeException Exception is thrown if the created object is not a document
     */
    public function createDocument(
        array $properties,
        StreamInterface $contentStream,
        VersioningState $versioningState,
        array $policies = array(),
        array $addAces = array(),
        array $removeAces = array(),
        OperationContextInterface $context = null
    ) {
        $newObjectId = $this->getSession()->createDocument(
            $properties,
            $this,
            $contentStream,
            $versioningState,
            $policies,
            $addAces,
            $removeAces
        );

        $document = $this->getNewlyCreatedObject($newObjectId, $context);

        if ($document === null) {
            return null;
        } elseif (!$document instanceof DocumentInterface) {
            throw new CmisRuntimeException('Newly created object is not a document! New id: ' . $document->getId());
        }

        return $document;
    }

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
     * @throws CmisRuntimeException Exception is thrown if the created object is not a document
     */
    public function createDocumentFromSource(
        ObjectIdInterface $source,
        array $properties,
        VersioningState $versioningState,
        array $policies = array(),
        array $addAces = array(),
        array $removeAces = array(),
        OperationContextInterface $context = null
    ) {
        $newObjectId = $this->getSession()->createDocumentFromSource(
            $source,
            $properties,
            $this,
            $versioningState,
            $policies,
            $addAces,
            $removeAces
        );

        $document = $this->getNewlyCreatedObject($newObjectId, $context);

        if ($document === null) {
            return null;
        } elseif (!$document instanceof DocumentInterface) {
            throw new CmisRuntimeException('Newly created object is not a document! New id: ' . $document->getId());
        }

        return $document;
    }

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
     * @throws CmisRuntimeException Exception is thrown if the created object is not a folder
     */
    public function createFolder(
        array $properties,
        array $policies = array(),
        array $addAces = array(),
        array $removeAces = array(),
        OperationContextInterface $context = null
    ) {
        $newObjectId = $this->getSession()->createFolder($properties, $this, $policies, $addAces, $removeAces);

        $folder = $this->getNewlyCreatedObject($newObjectId, $context);

        if ($folder === null) {
            return null;
        } elseif (!$folder instanceof FolderInterface) {
            throw new CmisRuntimeException('Newly created object is not a folder! New id: ' . $folder->getId());
        }

        return $folder;
    }

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
     * @throws CmisRuntimeException Exception is thrown if the created object is not a item
     */
    public function createItem(
        array $properties,
        array $policies = array(),
        array $addAces = array(),
        array $removeAces = array(),
        OperationContextInterface $context = null
    ) {
        $newObjectId = $this->getSession()->createItem($properties, $this, $policies, $addAces, $removeAces);

        $item = $this->getNewlyCreatedObject($newObjectId, $context);

        if ($item === null) {
            return null;
        } elseif (!$item instanceof ItemInterface) {
            throw new CmisRuntimeException('Newly created object is not a item! New id: ' . $item->getId());
        }

        return $item;
    }

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
     * @throws CmisRuntimeException Exception is thrown if the created object is not a policy
     */
    public function createPolicy(
        array $properties,
        array $policies = array(),
        array $addAces = array(),
        array $removeAces = array(),
        OperationContextInterface $context = null
    ) {
        $newObjectId = $this->getSession()->createPolicy($properties, $this, $policies, $addAces, $removeAces);

        $policy = $this->getNewlyCreatedObject($newObjectId, $context);

        if ($policy === null) {
            return null;
        } elseif (!$policy instanceof PolicyInterface) {
            throw new CmisRuntimeException('Newly created object is not a policy! New id: ' . $policy->getId());
        }

        return $policy;
    }

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
    public function deleteTree($allVersions, UnfileObject $unfile, $continueOnFailure = true)
    {
        $failed = $this->getBinding()->getObjectService()->deleteTree(
            $this->getRepositoryId(),
            $this->getId(),
            $allVersions,
            $unfile,
            $continueOnFailure
        );

        if (count($failed->getIds()) === 0) {
            $this->getSession()->removeObjectFromCache($this);
        }

        return $failed;
    }

    /**
     * Returns all checked out documents in this folder using the given OperationContext.
     *
     * @param OperationContextInterface|null $context
     * @return DocumentInterface[] A list of checked out documents.
     */
    public function getCheckedOutDocs(OperationContextInterface $context = null)
    {
        $context = $this->ensureContext($context);
        $checkedOutDocs = $this->getBinding()->getNavigationService()->getCheckedOutDocs(
            $this->getRepositoryId(),
            $this->getId(),
            $context->getQueryFilterString(),
            $context->getOrderBy(),
            $context->isIncludeAllowableActions(),
            $context->getIncludeRelationships(),
            $context->getRenditionFilterString()
        );

        $result = array();
        $objectFactory = $this->getObjectFactory();
        foreach ($checkedOutDocs->getObjects() as $objectData) {
            $document = $objectFactory->convertObject($objectData, $context);
            if (!($document instanceof DocumentInterface)) {
                // should not happen but could happen if the repository is not implemented correctly ...
                continue;
            }

            $result[] = $document;
        }

        return $result;
    }

    /**
     * Returns the children of this folder using the given OperationContext.
     *
     * @param OperationContextInterface|null $context
     * @return CmisObjectInterface[] A list of the child objects for the specified folder.
     */
    public function getChildren(OperationContextInterface $context = null)
    {
        $context = $this->ensureContext($context);
        $children = $this->getBinding()->getNavigationService()->getChildren(
            $this->getRepositoryId(),
            $this->getId(),
            $context->getQueryFilterString(),
            $context->getOrderBy(),
            $context->isIncludeAllowableActions(),
            $context->getIncludeRelationships(),
            $context->getRenditionFilterString(),
            $context->isIncludePathSegments()
        );

        $result = array();
        $objectFactory = $this->getObjectFactory();
        foreach ($children->getObjects() as $objectData) {
            if ($objectData->getObject() !== null) {
                $result[] = $objectFactory->convertObject($objectData->getObject(), $context);
            }
        }

        return $result;
    }

    /**
     * Gets the folder descendants starting with this folder.
     *
     * @param integer $depth
     * @param OperationContextInterface|null $context
     * @return TreeInterface A tree that contains FileableCmisObject objects
     * @see FileableCmisObject FileableCmisObject contained in returned TreeInterface
     */
    public function getDescendants($depth, OperationContextInterface $context = null)
    {
        $context = $this->ensureContext($context);
        $containerList = $this->getBinding()->getNavigationService()->getDescendants(
            $this->getRepositoryId(),
            $this->getId(),
            (int) $depth,
            $context->getQueryFilterString(),
            $context->isIncludeAllowableActions(),
            $context->getIncludeRelationships(),
            $context->getRenditionFilterString(),
            $context->isIncludePathSegments()
        );

        return $this->convertBindingContainer($containerList, $context);
    }

    /**
     * Gets the parent folder object.
     *
     * @return FolderInterface|null the parent folder object or <code>null</code> if the folder is the root folder.
     */
    public function getFolderParent()
    {
        if ($this->isRootFolder()) {
            return null;
        }

        $parents = $this->getParents($this->getSession()->getDefaultContext());

        // return the first element of the array
        $parent = reset($parents);

        if (!$parent instanceof FolderInterface) {
            return null;
        }

        return $parent;
    }

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
    public function getFolderTree($depth, OperationContextInterface $context = null)
    {
        $context = $this->ensureContext($context);
        $containerList = $this->getBinding()->getNavigationService()->getFolderTree(
            $this->getRepositoryId(),
            $this->getId(),
            (int) $depth,
            $context->getQueryFilterString(),
            $context->isIncludeAllowableActions(),
            $context->getIncludeRelationships(),
            $context->getRenditionFilterString(),
            $context->isIncludePathSegments()
        );

        return $this->convertBindingContainer($containerList, $context);
    }

    /**
     * Returns the path of the folder.
     *
     * @return string the absolute folder path
     */
    public function getPath()
    {
        $path = $this->getPropertyValue(PropertyIds::PATH);

        // if the path property isn't set, get it
        if ($path === null) {
            $objectData = $this->getBinding()->getObjectService()->getObject(
                $this->getRepositoryId(),
                $this->getId(),
                $this->getPropertyQueryName(PropertyIds::PATH),
                false,
                IncludeRelationships::cast(IncludeRelationships::NONE),
                Constants::RENDITION_NONE,
                false,
                false
            );

            if ($objectData !== null
                && $objectData->getProperties() !== null
                && $objectData->getProperties()->getProperties() !== null
            ) {
                $objectProperties = $objectData->getProperties()->getProperties();
                if (isset($objectProperties[PropertyIds::PATH])
                    && $objectProperties[PropertyIds::PATH] instanceof PropertyString
                ) {
                    $path = $objectProperties[PropertyIds::PATH]->getFirstValue();
                }
            }
        }

        // we still don't know the path ... it's not a CMIS compliant repository
        if ($path === null) {
            throw new CmisRuntimeException('Repository didn\'t return ' . PropertyIds::PATH . '!');
        }

        return $path;
    }

    /**
     * Returns if the folder is the root folder.
     *
     * @return boolean <code>true</code> if the folder is the root folder, <code>false</code> otherwise
     */
    public function isRootFolder()
    {
        return $this->getSession()->getRepositoryInfo()->getRootFolderId() === $this->getId();
    }

    /**
     * Returns the list of the allowed object types in this folder (CMIS property cmis:allowedChildObjectTypeIds).
     * If the list is empty or <code>null</code> all object types are allowed.
     *
     * @return ObjectTypeInterface[] the property value or <code>null</code> if the property hasn't been requested,
     *     hasn't been provided by the repository, or the property value isn't set
     */
    public function getAllowedChildObjectTypes()
    {
        $result = array();

        $objectTypeIds = $this->getPropertyValue(PropertyIds::ALLOWED_CHILD_OBJECT_TYPE_IDS);
        if ($objectTypeIds === null) {
            return $result;
        }

        foreach ($objectTypeIds as $objectTypeId) {
            $result[] = $this->getSession()->getTypeDefinition($objectTypeId);
        }

        return $result;
    }

    /**
     * Returns the parent id or <code>null</code> if the folder is the root folder (CMIS property cmis:parentId).
     *
     * @return string|null the property value or <code>null</code> if the property hasn't been requested, hasn't
     *      been provided by the repository, or the folder is the root folder
     */
    public function getParentId()
    {
        return $this->getPropertyValue(PropertyIds::PARENT_ID);
    }


    /**
     * Converts a binding container into an API container.
     *
     * @param ObjectInFolderContainerInterface[] $bindingContainerList
     * @param OperationContextInterface $context
     * @return TreeInterface[]
     */
    private function convertBindingContainer(array $bindingContainerList, OperationContextInterface $context)
    {
        // TODO implement when Tree and ObjectInFolderContainer is implemented
        throw new \Exception('Not yet implemented!');
    }
}
