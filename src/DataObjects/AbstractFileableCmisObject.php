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
use Dkd\PhpCmis\Data\FileableCmisObjectInterface;
use Dkd\PhpCmis\Data\FolderInterface;
use Dkd\PhpCmis\Data\ObjectDataInterface;
use Dkd\PhpCmis\Data\ObjectIdInterface;
use Dkd\PhpCmis\Data\ObjectTypeInterface;
use Dkd\PhpCmis\Data\PropertyIdInterface;
use Dkd\PhpCmis\Data\PropertyStringInterface;
use Dkd\PhpCmis\Enum\BaseTypeId;
use Dkd\PhpCmis\Enum\IncludeRelationships;
use Dkd\PhpCmis\Exception\CmisRuntimeException;
use Dkd\PhpCmis\OperationContextInterface;
use Dkd\PhpCmis\PropertyIds;
use Dkd\PhpCmis\SessionInterface;

/**
 * Base class for all fileable persistent session object classes.
 */
abstract class AbstractFileableCmisObject extends AbstractCmisObject implements FileableCmisObjectInterface
{
    /**
     * @param SessionInterface $session
     * @param ObjectTypeInterface $objectType
     * @param OperationContextInterface $context
     * @param ObjectDataInterface|null $objectData
     */
    public function __construct(
        SessionInterface $session,
        ObjectTypeInterface $objectType,
        OperationContextInterface $context,
        ObjectDataInterface $objectData = null
    ) {
        $this->initialize($session, $objectType, $context, $objectData);
    }

    /**
     * Returns the parents of this object.
     *
     * @param OperationContextInterface|null $context the OperationContext to use to fetch the parent folder objects
     * @return FolderInterface[] the list of parent folders of this object or an empty list if this object is unfiled
     *     or if this object is the root folder
     * @throws CmisRuntimeException Throws exception if invalid data is returned by the repository
     */
    public function getParents(OperationContextInterface $context = null)
    {
        $context = $this->ensureContext($context);

        // get object ids of the parent folders
        $bindingParents = $this->getBinding()->getNavigationService()->getObjectParents(
            $this->getRepositoryId(),
            $this->getId(),
            $this->getPropertyQueryName(PropertyIds::OBJECT_ID),
            false,
            IncludeRelationships::cast(IncludeRelationships::NONE),
            Constants::RENDITION_NONE,
            false,
            null
        );

        $parents = array();

        foreach ($bindingParents as $parent) {
            if ($parent->getObject()->getProperties() === null) {
                // that should never happen
                throw new CmisRuntimeException('Repository sent invalid data!');
            }

            $parentProperties = $parent->getObject()->getProperties()->getProperties();
            // get id property
            $idProperty = null;
            if (isset($parentProperties[PropertyIds::OBJECT_ID])) {
                $idProperty = $parentProperties[PropertyIds::OBJECT_ID];
            }

            if (!$idProperty instanceof PropertyIdInterface && !$idProperty instanceof PropertyStringInterface) {
                // the repository sent an object without a valid object id...
                throw new CmisRuntimeException('Repository sent invalid data! No object id!');
            }

            // fetch the object and make sure it is a folder
            $parentFolder = $this->getSession()->getObject(
                $this->session->createObjectId((String) $idProperty->getFirstValue()),
                $context
            );
            if (!$parentFolder instanceof FolderInterface) {
                // the repository sent an object that is not a folder...
                throw new CmisRuntimeException('Repository sent invalid data! Object is not a folder!');
            }

            $parents[] = $parentFolder;
        }

        return $parents;
    }

    /**
     * Returns the paths of this object.
     *
     * @return string[] the list of paths of this object or an empty list if this object is unfiled or if this object
     *     is the root folder
     * @throws CmisRuntimeException Throws exception if repository sends invalid data
     */
    public function getPaths()
    {
        $folderType = $this->getSession()->getTypeDefinition((string) BaseTypeId::cast(BaseTypeId::CMIS_FOLDER));

        $propertyDefinition = $folderType->getPropertyDefinition(PropertyIds::PATH);
        $pathQueryName = ($propertyDefinition === null) ? null : $propertyDefinition->getQueryName();

        // get object paths of the parent folders
        $bindingParents = $this->getBinding()->getNavigationService()->getObjectParents(
            $this->getRepositoryId(),
            $this->getId(),
            $pathQueryName,
            false,
            IncludeRelationships::cast(IncludeRelationships::NONE),
            Constants::RENDITION_NONE,
            true,
            null
        );
        $paths = array();
        foreach ($bindingParents as $parent) {
            if ($parent->getObject()->getProperties() === null) {
                // that should never happen but could in case of an faulty repository implementation
                throw new CmisRuntimeException('Repository sent invalid data! No properties given.');
            }

            $parentProperties = $parent->getObject()->getProperties()->getProperties();
            $pathProperty = null;

            if (isset($parentProperties[PropertyIds::PATH])) {
                $pathProperty = $parentProperties[PropertyIds::PATH];
            }

            if (!$pathProperty instanceof PropertyStringInterface) {
                // the repository sent an object without a valid path...
                throw new CmisRuntimeException('Repository sent invalid data! Path is not set!');
            }

            if ($parent->getRelativePathSegment() === null) {
                throw new CmisRuntimeException('Repository sent invalid data! No relative path segement!');
            }

            $folderPath = rtrim((string) $pathProperty->getFirstValue(), '/') . '/';
            $paths[] = $folderPath . $parent->getRelativePathSegment();
        }

        return $paths;
    }

    /**
     * Moves this object.
     *
     * @param ObjectIdInterface $sourceFolderId the object ID of the source folder
     * @param ObjectIdInterface $targetFolderId the object ID of the target folder
     * @param OperationContextInterface|null $context the OperationContext to use to fetch the moved object
     * @return FileableCmisObjectInterface the moved object
     * @throws CmisRuntimeException Throws exception if the repository returns an invalid object after the object has
     *     been moved
     */
    public function move(
        ObjectIdInterface $sourceFolderId,
        ObjectIdInterface $targetFolderId,
        OperationContextInterface $context = null
    ) {
        $context = $this->ensureContext($context);

        $originalId = $this->getId();
        $newObjectId = $this->getId();

        $this->getBinding()->getObjectService()->moveObject(
            $this->getRepositoryId(),
            $newObjectId,
            $targetFolderId->getId(),
            $sourceFolderId->getId(),
            null
        );

        // invalidate path cache
        $this->getSession()->removeObjectFromCache($this->getSession()->createObjectId($originalId));

        if (empty($newObjectId)) {
            return null;
        }

        $movedObject = $this->getSession()->getObject(
            $this->getSession()->createObjectId((string) $newObjectId),
            $context
        );
        if (!$movedObject instanceof FileableCmisObjectInterface) {
            throw new CmisRuntimeException(
                'Moved object is invalid because it must be of type FileableCmisObjectInterface but is not.'
            );
        }

        return $movedObject;
    }

    /**
     * Adds this object to a folder.
     *
     * @param ObjectIdInterface $folderId The folder into which the object is to be filed.
     * @param boolean $allVersions Add all versions of the object to the folder if the repository supports
     *     version-specific filing. Defaults to <code>true</code>.
     */
    public function addToFolder(ObjectIdInterface $folderId, $allVersions = true)
    {
        $objectId = $this->getId();
        $this->getBinding()->getMultiFilingService()->addObjectToFolder(
            $this->getRepositoryId(),
            $objectId,
            $folderId->getId(),
            $allVersions
        );

        // remove object form cache
        $this->getSession()->removeObjectFromCache($this->getSession()->createObjectId($objectId));
    }

    /**
     * Removes this object from a folder.
     *
     * @param ObjectIdInterface $folderId the object ID of the folder from which this object should be removed
     */
    public function removeFromFolder(ObjectIdInterface $folderId)
    {
        $objectId = $this->getId();

        $this->getBinding()->getMultiFilingService()->removeObjectFromFolder(
            $this->getRepositoryId(),
            $objectId,
            $folderId->getId(),
            null
        );

        // remove object form cache
        $this->getSession()->removeObjectFromCache($this->getSession()->createObjectId($objectId));
    }

    /**
     * @param ObjectIdInterface|null $objectId
     * @param OperationContextInterface|null $context
     * @return CmisObjectInterface|null
     * @throws CmisRuntimeException Throws exception if newly created object is not a document as expected
     */
    protected function getNewlyCreatedObject(
        ObjectIdInterface $objectId = null,
        OperationContextInterface $context = null
    ) {
        // if no context is provided the object will not be fetched
        if ($context === null || $objectId === null) {
            return null;
        }

        // get the new object
        return $this->getSession()->getObject($objectId, $context);
    }

    /**
     * Ensures that the context is set. If the given context is <code>null</code> the session default context will
     * be returned.
     *
     * @param OperationContextInterface|null $context
     *
     * @return OperationContextInterface
     */
    protected function ensureContext(OperationContextInterface $context = null)
    {
        if ($context === null) {
            $context = $this->getSession()->getDefaultContext();
        }

        return $context;
    }
}
