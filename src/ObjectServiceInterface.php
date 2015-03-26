<?php
namespace Dkd\PhpCmis;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Data\AclInterface;
use Dkd\PhpCmis\Data\AllowableActionsInterface;
use Dkd\PhpCmis\Data\BulkUpdateObjectIdAndChangeTokenInterface;
use Dkd\PhpCmis\Data\ExtensionDataInterface;
use Dkd\PhpCmis\Data\FailedToDeleteDataInterface;
use Dkd\PhpCmis\Data\ObjectDataInterface;
use Dkd\PhpCmis\Data\PropertiesInterface;
use Dkd\PhpCmis\Data\RenditionDataInterface;
use Dkd\PhpCmis\Enum\IncludeRelationships;
use Dkd\PhpCmis\Enum\UnfileObject;
use Dkd\PhpCmis\Enum\VersioningState;
use GuzzleHttp\Stream\StreamInterface;

/**
 * Object Service interface.
 *
 * See the CMIS 1.0 and CMIS 1.1 specifications for details on the operations,
 * parameters, exceptions and the domain model.
 */
interface ObjectServiceInterface
{

    /**
     * Appends the content stream to the content of the document.
     *
     * The stream in contentStream is consumed but not closed by this method.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId the identifier for the object. The repository might return a different/new object id
     * @param StreamInterface $contentStream the content stream to append
     * @param boolean $isLastChunk indicates if this content stream is the last chunk
     * @param string|null $changeToken the last change token of this object that the client received.
     *      The repository might return a new change token (default is <code>null</code>)
     * @param ExtensionDataInterface|null $extension
     */
    public function appendContentStream(
        $repositoryId,
        & $objectId,
        StreamInterface $contentStream,
        $isLastChunk,
        & $changeToken = null,
        ExtensionDataInterface $extension = null
    );

    /**
     * Updates properties and secondary types of one or more objects.
     *
     * @param string $repositoryId the identifier for the repository
     * @param BulkUpdateObjectIdAndChangeTokenInterface[] $objectIdsAndChangeTokens
     * @param PropertiesInterface $properties
     * @param string[] $addSecondaryTypeIds the secondary types to apply
     * @param string[] $removeSecondaryTypeIds the secondary types to remove
     * @param ExtensionDataInterface|null $extension
     * @return BulkUpdateObjectIdAndChangeTokenInterface[]
     */
    public function bulkUpdateProperties(
        $repositoryId,
        array $objectIdsAndChangeTokens,
        PropertiesInterface $properties,
        array $addSecondaryTypeIds,
        array $removeSecondaryTypeIds,
        ExtensionDataInterface $extension = null
    );

    /**
     * Creates a document object of the specified type (given by the cmis:objectTypeId property)
     * in the (optionally) specified location.
     *
     * @param string $repositoryId the identifier for the repository
     * @param PropertiesInterface $properties the property values that must be applied to the newly
     *      created document object
     * @param string|null $folderId if specified, the identifier for the folder that must be the parent
     *      folder for the newly created document object
     * @param StreamInterface|null $contentStream the content stream that must be stored for the newly
     *      created document object
     * @param VersioningState|null  $versioningState specifies what the versioning state of the newly created object
     *      must be (default is <code>VersioningState::MAJOR</code>)
     * @param string[] $policies a list of policy IDs that must be applied to the newly created document object
     * @param AclInterface|null  $addAces a list of ACEs that must be added to the newly created document object,
     *      either using the ACL from folderId if specified, or being applied if no folderId is specified
     * @param AclInterface|null  $removeAces a list of ACEs that must be removed from the newly created document object,
     *      either using the ACL from folderId if specified, or being ignored if no folderId is specified
     * @param ExtensionDataInterface|null  $extension
     * @return string|null Returns the new object id or <code>null</code> if the repository sent an empty
     *      result (which should not happen)
     */
    public function createDocument(
        $repositoryId,
        PropertiesInterface $properties,
        $folderId = null,
        StreamInterface $contentStream = null,
        VersioningState $versioningState = null,
        array $policies = array(),
        AclInterface $addAces = null,
        AclInterface $removeAces = null,
        ExtensionDataInterface $extension = null
    );

    /**
     * Creates a document object as a copy of the given source document in the (optionally) specified location.
     *
     * @param string $repositoryId The identifier for the repository
     * @param string $sourceId The identifier for the source document
     * @param PropertiesInterface $properties The property values that must be applied to the newly
     *      created document object
     * @param string|null $folderId If specified, the identifier for the folder that must be the parent folder for the
     *      newly created document object
     * @param VersioningState|null $versioningState Specifies what the versioning state of the newly created object
     *      must be (default is <code>VersioningState::MAJOR</code>)
     * @param string[] $policies a list of policy IDs that must be applied to the newly created document object
     * @param AclInterface|null $addAces A list of ACEs that must be added to the newly created document object,
     *      either using the ACL from folderId if specified, or being applied if no folderId is specified
     * @param AclInterface|null $removeAces A list of ACEs that must be removed from the newly created document object,
     *      either using the ACL from folderId if specified, or being ignored if no folderId is specified
     * @param ExtensionDataInterface|null $extension
     * @return string The id of the newly-created document.
     */
    public function createDocumentFromSource(
        $repositoryId,
        $sourceId,
        PropertiesInterface $properties,
        $folderId = null,
        VersioningState $versioningState = null,
        array $policies = array(),
        AclInterface $addAces = null,
        AclInterface $removeAces = null,
        ExtensionDataInterface $extension = null
    );

    /**
     * Creates a folder object of the specified type (given by the cmis:objectTypeId property) in
     * the specified location.
     *
     * @param string $repositoryId the identifier for the repository
     * @param PropertiesInterface $properties the property values that must be applied to the newly
     *      created document object
     * @param string $folderId if specified, the identifier for the folder that must be the parent folder for the
     *      newly created document object
     * @param string[] $policies a list of policy IDs that must be applied to the newly created document object
     * @param AclInterface|null $addAces a list of ACEs that must be added to the newly created document object,
     *      either using the ACL from folderId if specified, or being applied if no folderId is specified
     * @param AclInterface|null $removeAces a list of ACEs that must be removed from the newly created document object,
     *      either using the ACL from folderId if specified, or being ignored if no folderId is specified
     * @param ExtensionDataInterface|null $extension
     * @return string|null Returns the new object id or <code>null</code> if the repository sent an empty
     *      result (which should not happen)
     */
    public function createFolder(
        $repositoryId,
        PropertiesInterface $properties,
        $folderId,
        array $policies = array(),
        AclInterface $addAces = null,
        AclInterface $removeAces = null,
        ExtensionDataInterface $extension = null
    );

    /**
     * Creates an item object of the specified type (given by the cmis:objectTypeId property).
     *
     * @param string $repositoryId The identifier for the repository
     * @param PropertiesInterface $properties The property values that must be applied to the newly
     *      created document object
     * @param string|null $folderId If specified, the identifier for the folder that must be the parent folder for the
     *      newly created document object
     * @param string[] $policies A list of policy IDs that must be applied to the newly created document object
     * @param AclInterface|null $addAces A list of ACEs that must be added to the newly created document object,
     *      either using the ACL from folderId if specified, or being applied if no folderId is specified
     * @param AclInterface|null $removeAces A list of ACEs that must be removed from the newly created document object,
     *      either using the ACL from folderId if specified, or being ignored if no folderId is specified
     * @param ExtensionDataInterface|null $extension
     * @return string|null Returns the new item id or <code>null</code> if the repository sent an empty
     *      result (which should not happen)
     */
    public function createItem(
        $repositoryId,
        PropertiesInterface $properties,
        $folderId = null,
        array $policies = array(),
        AclInterface $addAces = null,
        AclInterface $removeAces = null,
        ExtensionDataInterface $extension = null
    );

    /**
     * Creates a policy object of the specified type (given by the cmis:objectTypeId property).
     *
     * @param string $repositoryId The identifier for the repository
     * @param PropertiesInterface $properties The property values that must be applied to the newly
     *      created document object
     * @param string|null $folderId If specified, the identifier for the folder that must be the parent folder for the
     *      newly created document object
     * @param string[] $policies A list of policy IDs that must be applied to the newly created document object
     * @param AclInterface|null $addAces A list of ACEs that must be added to the newly created document object,
     *      either using the ACL from folderId if specified, or being applied if no folderId is specified
     * @param AclInterface|null $removeAces A list of ACEs that must be removed from the newly created document object,
     *      either using the ACL from folderId if specified, or being ignored if no folderId is specified
     * @param ExtensionDataInterface|null $extension
     * @return string The id of the newly-created policy.
     */
    public function createPolicy(
        $repositoryId,
        PropertiesInterface $properties,
        $folderId = null,
        array $policies = array(),
        AclInterface $addAces = null,
        AclInterface $removeAces = null,
        ExtensionDataInterface $extension = null
    );

    /**
     * Creates a relationship object of the specified type (given by the cmis:objectTypeId property).
     *
     * @param string $repositoryId the identifier for the repository
     * @param PropertiesInterface $properties the property values that must be applied to the newly
     *      created document object
     * @param string[] $policies a list of policy IDs that must be applied to the newly created document object
     * @param AclInterface|null $addAces a list of ACEs that must be added to the newly created document object,
     *      either using the ACL from folderId if specified, or being applied if no folderId is specified
     * @param AclInterface|null $removeAces a list of ACEs that must be removed from the newly created document object,
     *      either using the ACL from folderId if specified, or being ignored if no folderId is specified
     * @param ExtensionDataInterface|null $extension
     * @return string
     */
    public function createRelationship(
        $repositoryId,
        PropertiesInterface $properties,
        array $policies = array(),
        AclInterface $addAces = null,
        AclInterface $removeAces = null,
        ExtensionDataInterface $extension = null
    );

    /**
     * Deletes the content stream for the specified document object.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId the identifier for the object. The repository might return a different/new object id
     * @param string|null $changeToken the last change token of this object that the client received.
     *      The repository might return a new change token (default is <code>null</code>)
     * @param ExtensionDataInterface|null $extension
     */
    public function deleteContentStream(
        $repositoryId,
        & $objectId,
        & $changeToken = null,
        ExtensionDataInterface $extension = null
    );

    /**
     * Deletes the specified object.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId the identifier for the object
     * @param boolean $allVersions If <code>true</code> then delete all versions of the document, otherwise delete only
     *      the document object specified (default is <code>true</code>)
     * @param ExtensionDataInterface|null $extension
     */
    public function deleteObject(
        $repositoryId,
        $objectId,
        $allVersions = true,
        ExtensionDataInterface $extension = null
    );

    /**
     * Deletes the specified folder object and all of its child- and descendant-objects.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $folderId the identifier for the folder
     * @param boolean $allVersions If <code>true</code> then delete all versions of the document, otherwise delete only
     *      the document object specified (default is <code>true</code>)
     * @param UnfileObject|null $unfileObjects defines how the repository must process file-able child- or
     *      descendant-objects (default is UnfileObject::DELETE)
     * @param boolean $continueOnFailure If <code>true</code>, then the repository should continue attempting to perform
     *      this operation even if deletion of a child- or descendant-object in the specified folder cannot be deleted
     * @param ExtensionDataInterface|null $extension
     * @return FailedToDeleteDataInterface Returns a list of object ids that could not be deleted
     */
    public function deleteTree(
        $repositoryId,
        $folderId,
        $allVersions = true,
        UnfileObject $unfileObjects = null,
        $continueOnFailure = false,
        ExtensionDataInterface $extension = null
    );

    /**
     * Gets the list of allowable actions for an object.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId the identifier for the object
     * @param ExtensionDataInterface|null $extension
     * @return AllowableActionsInterface
     */
    public function getAllowableActions($repositoryId, $objectId, ExtensionDataInterface $extension = null);

    /**
     * Gets the content stream for the specified document object, or gets a rendition stream for
     * a specified rendition of a document or folder object.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId the identifier for the object
     * @param string|null $streamId
     * @param integer|null $offset
     * @param integer|null $length
     * @param ExtensionDataInterface|null $extension
     * @return StreamInterface|null
     */
    public function getContentStream(
        $repositoryId,
        $objectId,
        $streamId = null,
        $offset = null,
        $length = null,
        ExtensionDataInterface $extension = null
    );

    /**
     * Gets the specified information for the object specified by id.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId the identifier for the object
     * @param string|null $filter a comma-separated list of query names that defines which properties
     *      must be returned by the repository (default is repository specific)
     * @param boolean $includeAllowableActions if <code>true</code>, then the repository must return the allowable
     *      actions for the object (default is <code>false</code>)
     * @param IncludeRelationships|null $includeRelationships indicates what relationships in which the objects
     *      participate must be returned (default is <code>IncludeRelationships::NONE</code>)
     * @param string $renditionFilter indicates what set of renditions the repository must return whose kind
     *      matches this filter (default is "cmis:none")
     * @param boolean $includePolicyIds if <code>true</code>, then the repository must return the policy ids for
     *      the object (default is <code>false</code>)
     * @param boolean $includeAcl if <code>true</code>, then the repository must return the ACL for the object
     *      (default is <code>false</code>)
     * @param ExtensionDataInterface|null $extension
     * @return ObjectDataInterface|null Returns object of type ObjectDataInterface or <code>null</code> if the
     *      repository response was empty
     */
    public function getObject(
        $repositoryId,
        $objectId,
        $filter = null,
        $includeAllowableActions = false,
        IncludeRelationships $includeRelationships = null,
        $renditionFilter = Constants::RENDITION_NONE,
        $includePolicyIds = false,
        $includeAcl = false,
        ExtensionDataInterface $extension = null
    );

    /**
     * Gets the specified information for the object specified by path.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $path the path to the object
     * @param string|null $filter a comma-separated list of query names that defines which properties must be
     *      returned by the repository (default is repository specific)
     * @param boolean $includeAllowableActions if <code>true</code>, then the repository must return the allowable
     *      actions for the object (default is <code>false</code>)
     * @param IncludeRelationships|null $includeRelationships indicates what relationships in which the objects
     *      participate must be returned (default is <code>IncludeRelationships::NONE</code>)
     * @param string $renditionFilter indicates what set of renditions the repository must return whose kind
     *      matches this filter (default is "cmis:none")
     * @param boolean $includePolicyIds if <code>true</code>, then the repository must return the policy ids for
     *      the object (default is <code>false</code>)
     * @param boolean $includeAcl if <code>true</code>, then the repository must return the ACL for the object
     *      (default is <code>false</code>)
     * @param ExtensionDataInterface|null $extension
     * @return ObjectDataInterface|null Returns object of type <code>ObjectDataInterface</code> or <code>null</code>
     *      if the repository response was empty
     */
    public function getObjectByPath(
        $repositoryId,
        $path,
        $filter = null,
        $includeAllowableActions = false,
        IncludeRelationships $includeRelationships = null,
        $renditionFilter = Constants::RENDITION_NONE,
        $includePolicyIds = false,
        $includeAcl = false,
        ExtensionDataInterface $extension = null
    );

    /**
     * Gets the list of properties for an object.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId the identifier for the object
     * @param string|null $filter a comma-separated list of query names that defines which properties
     *      must be returned by the repository (default is repository specific)
     * @param ExtensionDataInterface|null $extension
     * @return PropertiesInterface
     */
    public function getProperties(
        $repositoryId,
        $objectId,
        $filter = null,
        ExtensionDataInterface $extension = null
    );

    /**
     * Gets the list of associated renditions for the specified object.
     * Only rendition attributes are returned, not rendition stream.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId the identifier for the object
     * @param string $renditionFilter indicates what set of renditions the repository must return whose kind
     *      matches this filter (default is "cmis:none")
     * @param integer|null $maxItems the maximum number of items to return in a response
     *      (default is repository specific)
     * @param integer $skipCount number of potential results that the repository MUST skip/page over before
     *      returning any results (default is 0)
     * @param ExtensionDataInterface|null $extension
     * @return RenditionDataInterface[]
     */
    public function getRenditions(
        $repositoryId,
        $objectId,
        $renditionFilter = Constants::RENDITION_NONE,
        $maxItems = null,
        $skipCount = 0,
        ExtensionDataInterface $extension = null
    );

    /**
     * Moves the specified file-able object from one folder to another.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId the identifier for the object. The repository might return a different/new object id
     * @param string $targetFolderId the identifier for the target folder
     * @param string $sourceFolderId the identifier for the source folder
     * @param ExtensionDataInterface|null $extension
     * @return ObjectDataInterface|null Returns object of type ObjectDataInterface or <code>null</code>
     *      if the repository response was empty
     */
    public function moveObject(
        $repositoryId,
        & $objectId,
        $targetFolderId,
        $sourceFolderId,
        ExtensionDataInterface $extension = null
    );

    /**
     * Sets the content stream for the specified document object.
     *
     * @param string $repositoryId The identifier for the repository
     * @param string $objectId The identifier for the object. The repository might return a different/new object id
     * @param StreamInterface $contentStream The content stream
     * @param boolean $overwriteFlag If <code>true</code>, then the repository must replace the existing content stream
     *      for the object (if any) with the input content stream. If <code>false</code>, then the repository must only
     *      set the input content stream for the object if the object currently does not have a content stream
     *      (default is <code>true</code>)
     * @param string|null $changeToken The last change token of this object that the client received.
     *      The repository might return a new change token (default is <code>null</code>)
     * @param ExtensionDataInterface|null $extension
     */
    public function setContentStream(
        $repositoryId,
        & $objectId,
        StreamInterface $contentStream,
        $overwriteFlag = true,
        & $changeToken = null,
        ExtensionDataInterface $extension = null
    );

    /**
     * Updates properties of the specified object.
     *
     * @param string $repositoryId The identifier for the repository
     * @param string $objectId The identifier for the object. The repository might return a different/new object id
     * @param PropertiesInterface $properties The updated property values that must be applied to the object
     * @param string|null $changeToken The last change token of this object that the client received.
     *      The repository might return a new change token (default is <code>null</code>)
     * @param ExtensionDataInterface|null $extension
     */
    public function updateProperties(
        $repositoryId,
        & $objectId,
        PropertiesInterface $properties,
        & $changeToken = null,
        ExtensionDataInterface $extension = null
    );
}
