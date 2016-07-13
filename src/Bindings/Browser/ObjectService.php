<?php
namespace Dkd\PhpCmis\Bindings\Browser;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Constants;
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
use Dkd\PhpCmis\Exception\CmisInvalidArgumentException;
use Dkd\PhpCmis\ObjectServiceInterface;
use Dkd\PhpCmis\PropertyIds;
use Dkd\PhpCmis\SessionParameter;
use GuzzleHttp\Post\PostFile;
use GuzzleHttp\Stream\LimitStream;
use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\StreamInterface;

/**
 * Object Service Browser Binding client.
 */
class ObjectService extends AbstractBrowserBindingService implements ObjectServiceInterface
{
    /**
     * L1 cache for objects. Fills with two levels:
     *
     * - First level key is the object ID, path or other singular identifier of object(s)
     * - Second level key is a hash of context arguments used to retrieve the object(s)
     *
     * @var array
     */
    protected $objectCache = array();

    /**
     * Appends the content stream to the content of the document.
     *
     * The stream in contentStream is consumed but not closed by this method.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId The identifier for the object. The repository might return a different/new object id
     * @param StreamInterface $contentStream The content stream to append
     * @param boolean $isLastChunk Indicates if this content stream is the last chunk
     * @param string|null $changeToken The last change token of this object that the client received.
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
    ) {
        // TODO: Implement appendContentStream() method.
    }

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
    ) {
        // TODO: Implement bulkUpdateProperties() method.
    }

    /**
     * @param string $action
     * @param PropertiesInterface $properties
     * @param string[] $policies
     * @param AclInterface $addAces
     * @param AclInterface $removeAces
     * @param ExtensionDataInterface $extension
     * @return array
     */
    protected function createQueryArray(
        $action,
        PropertiesInterface $properties,
        array $policies = array(),
        AclInterface $addAces = null,
        AclInterface $removeAces = null,
        ExtensionDataInterface $extension = null
    ) {
        $queryArray = array_replace(
            array(
                Constants::CONTROL_CMISACTION => $action,
                Constants::PARAM_SUCCINCT => $this->getSuccinct() ? 'true' : 'false',
            ),
            $this->convertPropertiesToQueryArray($properties),
            $this->convertPolicyIdArrayToQueryArray($policies)
        );
        if (!empty($removeAces)) {
            $queryArray = array_replace($queryArray, $this->convertAclToQueryArray(
                $removeAces,
                Constants::CONTROL_REMOVE_ACE_PRINCIPAL,
                Constants::CONTROL_REMOVE_ACE_PERMISSION
            ));
        }
        if (!empty($addAces)) {
            $queryArray = array_replace($queryArray, $this->convertAclToQueryArray(
                $addAces,
                Constants::CONTROL_ADD_ACE_PRINCIPAL,
                Constants::CONTROL_ADD_ACE_PERMISSION
            ));
        }
        return $queryArray;
    }

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
     * @param VersioningState|null $versioningState specifies what the versioning state of the newly created object
     *      must be (default is <code>VersioningState::MAJOR</code>)
     * @param string[] $policies a list of policy IDs that must be applied to the newly created document object
     * @param AclInterface|null $addAces a list of ACEs that must be added to the newly created document object,
     *      either using the ACL from folderId if specified, or being applied if no folderId is specified
     * @param AclInterface|null $removeAces a list of ACEs that must be removed from the newly created document object,
     *      either using the ACL from folderId if specified, or being ignored if no folderId is specified
     * @param ExtensionDataInterface|null $extension
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
    ) {
        if ($folderId === null) {
            $url = $this->getRepositoryUrl($repositoryId);
        } else {
            $url = $this->getObjectUrl($repositoryId, $folderId);
        }

        // Guzzle gets the mime type for a file by the filename extension. Sometimes the filename does not contain
        // the correct filename extension for example when a file is uploaded in php it gets a temporary name without
        // a file extension. If the filename does not contain a file extension we use the given 'cmis:name' property
        // as filename. See also https://github.com/guzzle/guzzle/issues/571
        if ($contentStream !== null && pathinfo($contentStream->getMetadata('uri'), PATHINFO_EXTENSION) === '') {
            $contentStream = new PostFile(
                'content',
                $contentStream,
                $properties->getProperties()['cmis:name']->getFirstValue()
            );
        }

        $queryArray = $this->createQueryArray(
            Constants::CMISACTION_CREATE_DOCUMENT,
            $properties,
            $policies,
            $addAces,
            $removeAces,
            $extension
        );
        if ($versioningState !== null) {
            $queryArray[Constants::PARAM_VERSIONING_STATE] = (string) $versioningState;
        }

        $responseData = $this->post(
            $url,
            $queryArray
        )->json();

        $newObject = $this->getJsonConverter()->convertObject($responseData);

        if ($newObject) {
            $newObjectId = $newObject->getId();
            if ($contentStream) {
                if ($contentStream instanceof PostFile) {
                    $contentStream = $contentStream->getContent();
                }
                $this->setContentStream($repositoryId, $newObjectId, $contentStream);
            }
            return $newObjectId;
        }
        return null;
    }

    /**
     * Creates a document object as a copy of the given source document in the (optionally) specified location.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $sourceId the identifier for the source document
     * @param PropertiesInterface $properties the property values that must be applied to the newly
     *      created document object
     * @param string|null $folderId if specified, the identifier for the folder that must be the parent folder for the
     *      newly created document object
     * @param VersioningState|null $versioningState specifies what the versioning state of the newly created object
     *      must be (default is <code>VersioningState::MAJOR</code>)
     * @param string[] $policies a list of policy IDs that must be applied to the newly created document object
     * @param AclInterface|null $addAces a list of ACEs that must be added to the newly created document object,
     *      either using the ACL from folderId if specified, or being applied if no folderId is specified
     * @param AclInterface|null $removeAces a list of ACEs that must be removed from the newly created document object,
     *      either using the ACL from folderId if specified, or being ignored if no folderId is specified
     * @param ExtensionDataInterface|null $extension
     * @return string|null Returns the new object id or <code>null</code> if the repository sent an empty
     *      result (which should not happen)
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
    ) {
        if ($folderId === null) {
            $url = $this->getRepositoryUrl($repositoryId);
        } else {
            $url = $this->getObjectUrl($repositoryId, $folderId);
        }

        $queryArray = $this->createQueryArray(
            Constants::CMISACTION_CREATE_DOCUMENT_FROM_SOURCE,
            $properties,
            $policies,
            $addAces,
            $removeAces,
            $extension
        );
        $queryArray[Constants::PARAM_SOURCE_ID] = (string) $sourceId;
        if ($versioningState !== null) {
            $queryArray[Constants::PARAM_VERSIONING_STATE] = (string) $versioningState;
        }
        $responseData = $this->post($url, $queryArray)->json();

        $newObject = $this->getJsonConverter()->convertObject($responseData);

        return ($newObject === null) ? null : $newObject->getId();
    }

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
    ) {
        $url = $this->getObjectUrl($repositoryId, $folderId);
        $queryArray = $this->createQueryArray(
            Constants::CMISACTION_CREATE_FOLDER,
            $properties,
            $policies,
            $addAces,
            $removeAces,
            $extension
        );

        $responseData = $this->post($url, $queryArray)->json();

        $newObject = $this->getJsonConverter()->convertObject($responseData);

        return ($newObject === null) ? null : $newObject->getId();
    }

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
    ) {
        if ($folderId === null) {
            $url = $this->getRepositoryUrl($repositoryId);
        } else {
            $url = $this->getObjectUrl($repositoryId, $folderId);
        }

        $queryArray = $this->createQueryArray(
            Constants::CMISACTION_CREATE_ITEM,
            $properties,
            $policies,
            $addAces,
            $removeAces,
            $extension
        );

        $responseData = $this->post($url, $queryArray)->json();

        $newObject = $this->getJsonConverter()->convertObject($responseData);

        return ($newObject === null) ? null : $newObject->getId();
    }

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
    ) {
        // TODO: Implement createPolicy() method.
    }

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
     * @return string|null Returns the new item id of the relationship object or <code>null</code> if the repository
     *      sent an empty result (which should not happen)
     */
    public function createRelationship(
        $repositoryId,
        PropertiesInterface $properties,
        array $policies = array(),
        AclInterface $addAces = null,
        AclInterface $removeAces = null,
        ExtensionDataInterface $extension = null
    ) {
        $url = $this->getRepositoryUrl($repositoryId);

        $queryArray = $this->createQueryArray(
            Constants::CMISACTION_CREATE_RELATIONSHIP,
            $properties,
            $policies,
            $addAces,
            $removeAces,
            $extension
        );

        $responseData = $this->post($url, $queryArray)->json();

        $newObject = $this->getJsonConverter()->convertObject($responseData);

        return ($newObject === null) ? null : $newObject->getId();
    }

    /**
     * Deletes the content stream for the specified document object.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId the identifier for the object. The repository might return a different/new object id
     * @param string|null $changeToken the last change token of this object that the client received.
     *      The repository might return a new change token (default is <code>null</code>)
     * @param ExtensionDataInterface|null $extension
     * @throws CmisInvalidArgumentException If $objectId is empty
     */
    public function deleteContentStream(
        $repositoryId,
        & $objectId,
        & $changeToken = null,
        ExtensionDataInterface $extension = null
    ) {
        if (empty($objectId)) {
            throw new CmisInvalidArgumentException('Object id must not be empty!');
        }

        $this->flushCached($objectId);

        $url = $this->getObjectUrl($repositoryId, $objectId);

        $url->getQuery()->modify(
            array(
                Constants::CONTROL_CMISACTION => Constants::CMISACTION_DELETE_CONTENT,
                Constants::PARAM_SUCCINCT => $this->getSuccinct() ? 'true' : 'false'
            )
        );

        if ($changeToken !== null && !$this->getSession()->get(SessionParameter::OMIT_CHANGE_TOKENS, false)) {
            $url->getQuery()->modify(array(Constants::PARAM_CHANGE_TOKEN => $changeToken));
        }

        $responseData = $this->post($url)->json();
        $newObject = $this->getJsonConverter()->convertObject($responseData);

        // $objectId was passed by reference. The value is changed here to new object id
        $objectId = null;
        if ($newObject !== null) {
            $objectId = $newObject->getId();
            $newObjectProperties = $newObject->getProperties()->getProperties();
            if ($changeToken !== null && count($newObjectProperties) > 0) {
                $newChangeToken = $newObjectProperties[PropertyIds::CHANGE_TOKEN];
                // $changeToken was passed by reference. The value is changed here
                $changeToken = $newChangeToken === null ? null : $newChangeToken->getFirstValue();
            }
        }
    }

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
    ) {
        $url = $this->getObjectUrl($repositoryId, $objectId);
        $url->getQuery()->modify(
            array(
                Constants::CONTROL_CMISACTION => Constants::CMISACTION_DELETE,
                Constants::PARAM_ALL_VERSIONS => $allVersions ? 'true' : 'false',
            )
        );

        $this->post($url);
        $this->flushCached($objectId);
    }

    /**
     * Deletes the specified folder object and all of its child- and descendant-objects.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $folderId the identifier for the folder
     * @param boolean $allVersions If <code>true</code> then delete all versions of the document, otherwise delete only
     *      the document object specified (default is <code>true</code>)
     * @param UnfileObject|null $unfileObjects defines how the repository must process file-able child- or
     *      descendant-objects (default is <code>UnfileObject::DELETE</code>)
     * @param boolean $continueOnFailure If <code>true</code>, then the repository should continue attempting to
     *      perform this operation even if deletion of a child- or descendant-object in the specified folder cannot
     *      be deleted
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
    ) {
        $url = $this->getObjectUrl($repositoryId, $folderId);
        $url->getQuery()->modify(
            array(
                Constants::CONTROL_CMISACTION => Constants::CMISACTION_DELETE_TREE,
                Constants::PARAM_FOLDER_ID => $folderId,
                Constants::PARAM_ALL_VERSIONS => $allVersions ? 'true' : 'false',
                Constants::PARAM_CONTINUE_ON_FAILURE => $continueOnFailure ? 'true' : 'false'
            )
        );

        if ($unfileObjects !== null) {
            $url->getQuery()->modify(array(Constants::PARAM_UNFILE_OBJECTS => (string) $unfileObjects));
        }

        $response = $this->post($url);

        return $this->getJsonConverter()->convertFailedToDelete((array) $response->json());
    }

    /**
     * Gets the list of allowable actions for an object.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId the identifier for the object
     * @param ExtensionDataInterface|null $extension
     * @return AllowableActionsInterface
     */
    public function getAllowableActions($repositoryId, $objectId, ExtensionDataInterface $extension = null)
    {
        // TODO: Implement getAllowableActions() method.
    }

    /**
     * Gets the content stream for the specified document object, or gets a rendition stream for
     * a specified rendition of a document or folder object.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId the identifier for the object
     * @param string|null $streamId The identifier for the rendition stream, when used to get a rendition stream.
     *      For documents, if not provided then this method returns the content stream. For folders,
     *      it MUST be provided.
     * @param integer|null $offset
     * @param integer|null $length
     * @param ExtensionDataInterface|null $extension
     * @return StreamInterface|null
     * @throws CmisInvalidArgumentException If object id is empty
     */
    public function getContentStream(
        $repositoryId,
        $objectId,
        $streamId = null,
        $offset = null,
        $length = null,
        ExtensionDataInterface $extension = null
    ) {
        if (empty($objectId)) {
            throw new CmisInvalidArgumentException('Object id must not be empty!');
        }

        $url = $this->getObjectUrl($repositoryId, $objectId, Constants::SELECTOR_CONTENT);

        if ($streamId !== null) {
            $url->getQuery()->modify(array(Constants::PARAM_STREAM_ID => $streamId));
        }

        /** @var Response $response */
        $response = $this->getHttpInvoker()->get($url);

        $contentStream = $response->getBody();
        if (!$contentStream instanceof StreamInterface) {
            return null;
        }

        if ($offset !== null) {
            $contentStream = new LimitStream($contentStream, $length !== null ? $length : - 1, $offset);
        }

        return $contentStream;
    }

    /**
     * Gets the specified information for the object specified by id.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId the identifier for the object
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
     * @return ObjectDataInterface|null Returns object of type ObjectDataInterface or <code>null</code>
     *     if the repository response was empty
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
    ) {
        $cacheKey = $this->createCacheKey(
            $objectId,
            array(
                $repositoryId,
                $filter,
                $includeAllowableActions,
                $includeRelationships,
                $renditionFilter,
                $includePolicyIds,
                $includeAcl,
                $extension,
                $this->getSuccinct()
            )
        );
        if ($this->isCached($cacheKey)) {
            return $this->getCached($cacheKey);
        }
        $url = $this->getObjectUrl($repositoryId, $objectId, Constants::SELECTOR_OBJECT);
        $url->getQuery()->modify(
            array(
                Constants::PARAM_ALLOWABLE_ACTIONS => $includeAllowableActions ? 'true' : 'false',
                Constants::PARAM_RENDITION_FILTER => $renditionFilter,
                Constants::PARAM_POLICY_IDS => $includePolicyIds ? 'true' : 'false',
                Constants::PARAM_ACL => $includeAcl ? 'true' : 'false',
                Constants::PARAM_SUCCINCT => $this->getSuccinct() ? 'true' : 'false',
                Constants::PARAM_DATETIME_FORMAT => (string) $this->getDateTimeFormat()
            )
        );

        if (!empty($filter)) {
            $url->getQuery()->modify(array(Constants::PARAM_FILTER => (string) $filter));
        }

        if ($includeRelationships !== null) {
            $url->getQuery()->modify(array(Constants::PARAM_RELATIONSHIPS => (string) $includeRelationships));
        }

        $responseData = $this->read($url)->json();

        return $this->cache(
            $cacheKey,
            $this->getJsonConverter()->convertObject($responseData)
        );
    }

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
    ) {
        $cacheKey = $this->createCacheKey(
            $path,
            array(
                $repositoryId,
                $filter,
                $includeAllowableActions,
                $includeRelationships,
                $renditionFilter,
                $includePolicyIds,
                $includeAcl,
                $extension,
                $this->getSuccinct()
            )
        );
        if ($this->isCached($cacheKey)) {
            return $this->getCached($cacheKey);
        }

        $url = $this->getPathUrl($repositoryId, $path, Constants::SELECTOR_OBJECT);
        $url->getQuery()->modify(
            array(
                Constants::PARAM_ALLOWABLE_ACTIONS => $includeAllowableActions ? 'true' : 'false',
                Constants::PARAM_RENDITION_FILTER => $renditionFilter,
                Constants::PARAM_POLICY_IDS => $includePolicyIds ? 'true' : 'false',
                Constants::PARAM_ACL => $includeAcl ? 'true' : 'false',
                Constants::PARAM_SUCCINCT => $this->getSuccinct() ? 'true' : 'false',
                Constants::PARAM_DATETIME_FORMAT => (string) $this->getDateTimeFormat()
            )
        );

        if (!empty($filter)) {
            $url->getQuery()->modify(array(Constants::PARAM_FILTER => (string) $filter));
        }

        if ($includeRelationships !== null) {
            $url->getQuery()->modify(array(Constants::PARAM_RELATIONSHIPS => (string) $includeRelationships));
        }

        $responseData = $this->read($url)->json();

        return $this->cache(
            $cacheKey,
            $this->getJsonConverter()->convertObject($responseData)
        );
    }

    /**
     * Gets the list of properties for an object.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId the identifier for the object
     * @param string|null $filter a comma-separated list of query names that defines which properties must be
     *      returned by the repository (default is repository specific)
     * @param ExtensionDataInterface|null $extension
     * @return PropertiesInterface
     */
    public function getProperties(
        $repositoryId,
        $objectId,
        $filter = null,
        ExtensionDataInterface $extension = null
    ) {
        $cacheKey = $this->createCacheKey(
            $objectId,
            array(
                $repositoryId,
                $filter,
                $extension,
                $this->getSuccinct()
            )
        );

        if ($this->isCached($cacheKey)) {
            return $this->getCached($cacheKey);
        }

        $url = $this->getObjectUrl($repositoryId, $objectId, Constants::SELECTOR_PROPERTIES);
        $url->getQuery()->modify(
            array(
                Constants::PARAM_SUCCINCT => $this->getSuccinct() ? 'true' : 'false',
                Constants::PARAM_DATETIME_FORMAT => (string) $this->getDateTimeFormat()
            )
        );

        if (!empty($filter)) {
            $url->getQuery()->modify(array(Constants::PARAM_FILTER => (string) $filter));
        }

        $responseData = $this->read($url)->json();

        if ($this->getSuccinct()) {
            $objectData = $this->getJsonConverter()->convertSuccinctProperties($responseData);
        } else {
            $objectData = $this->getJsonConverter()->convertProperties($responseData);
        }

        return $this->cache(
            $cacheKey,
            $objectData
        );
    }

    /**
     * Gets the list of associated renditions for the specified object.
     * Only rendition attributes are returned, not rendition stream.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId the identifier for the object
     * @param string $renditionFilter indicates what set of renditions the repository must return whose kind
     *      matches this filter (default is "cmis:none")
     * @param integer|null $maxItems the maximum number of items to return in a response
     *       (default is repository specific)
     * @param integer $skipCount number of potential results that the repository MUST skip/page over before
     *      returning any results (default is 0)
     * @param ExtensionDataInterface|null $extension
     * @return RenditionDataInterface[]
     * @throws CmisInvalidArgumentException If object id is empty or skip count not of type integer
     */
    public function getRenditions(
        $repositoryId,
        $objectId,
        $renditionFilter = Constants::RENDITION_NONE,
        $maxItems = null,
        $skipCount = 0,
        ExtensionDataInterface $extension = null
    ) {
        if (empty($objectId)) {
            throw new CmisInvalidArgumentException('Object id must not be empty!');
        }

        if (!is_int($skipCount)) {
            throw new CmisInvalidArgumentException('Skip count must be of type integer!');
        }

        $url = $this->getObjectUrl($repositoryId, $objectId, Constants::SELECTOR_RENDITIONS);
        $url->getQuery()->modify(
            array(
                Constants::PARAM_RENDITION_FILTER => $renditionFilter,
                Constants::PARAM_SKIP_COUNT => (string) $skipCount,
            )
        );

        if ($maxItems !== null) {
            $url->getQuery()->modify(array(Constants::PARAM_MAX_ITEMS => (string) $maxItems));
        }

        $responseData = $this->read($url)->json();

        return $this->getJsonConverter()->convertRenditions($responseData);
    }

    /**
     * Moves the specified file-able object from one folder to another.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId the identifier for the object. The repository might return a different/new object id
     * @param string $targetFolderId the identifier for the target folder
     * @param string $sourceFolderId the identifier for the source folder
     * @param ExtensionDataInterface|null $extension
     * @return ObjectDataInterface|null Returns object of type ObjectDataInterface or <code>null</code>
     *     if the repository response was empty
     */
    public function moveObject(
        $repositoryId,
        & $objectId,
        $targetFolderId,
        $sourceFolderId,
        ExtensionDataInterface $extension = null
    ) {
        $this->flushCached($objectId);

        $url = $this->getObjectUrl($repositoryId, $objectId);
        $url->getQuery()->modify(
            array(
                Constants::CONTROL_CMISACTION => Constants::CMISACTION_MOVE,
                Constants::PARAM_TARGET_FOLDER_ID => $targetFolderId,
                Constants::PARAM_SOURCE_FOLDER_ID => $sourceFolderId,
                Constants::PARAM_SUCCINCT => $this->getSuccinct() ? 'true' : 'false'
            )
        );

        $responseData = $this->post($url)->json();
        $newObject = $this->getJsonConverter()->convertObject($responseData);

        // $objectId was passed by reference. The value is changed here to new object id
        $objectId = ($newObject === null) ? null : $newObject->getId();

        return $newObject;
    }

    /**
     * Sets the content stream for the specified document object.
     *
     * @param string $repositoryId The identifier for the repository
     * @param string $objectId The identifier for the object. The repository might return a different/new object id
     * @param StreamInterface $contentStream The content stream
     * @param boolean $overwriteFlag If <code>true</code>, then the repository must replace the existing content stream
     *      for the object (if any) with the input content stream. If <code>false</code>, then the repository must
     *      only set the input content stream for the object if the object currently does not have a content stream
     *      (default is <code>true</code>)
     * @param string|null $changeToken The last change token of this object that the client received.
     *      The repository might return a new change token (default is <code>null</code>)
     * @param ExtensionDataInterface|null $extension
     * @throws CmisInvalidArgumentException If object id is empty
     */
    public function setContentStream(
        $repositoryId,
        & $objectId,
        StreamInterface $contentStream,
        $overwriteFlag = true,
        & $changeToken = null,
        ExtensionDataInterface $extension = null
    ) {
        if (empty($objectId)) {
            throw new CmisInvalidArgumentException('Object id must not be empty!');
        }

        $this->flushCached($objectId);

        $url = $this->getObjectUrl($repositoryId, $objectId);

        $url->getQuery()->modify(
            array(
                Constants::CONTROL_CMISACTION => Constants::CMISACTION_SET_CONTENT,
                Constants::PARAM_OVERWRITE_FLAG => $overwriteFlag ? 'true' : 'false',
                Constants::PARAM_SUCCINCT => $this->getSuccinct() ? 'true' : 'false'
            )
        );

        if ($changeToken !== null && !$this->getSession()->get(SessionParameter::OMIT_CHANGE_TOKENS, false)) {
            $url->getQuery()->modify(array(Constants::PARAM_CHANGE_TOKEN => $changeToken));
        }

        $responseData = $this->post(
            $url,
            array('content' => $contentStream)
        )->json();

        $newObject = $this->getJsonConverter()->convertObject($responseData);

        // $objectId was passed by reference. The value is changed here to new object id
        $objectId = null;
        if ($newObject !== null) {
            $objectId = $newObject->getId();
            $newObjectProperties = $newObject->getProperties()->getProperties();
            if ($changeToken !== null && count($newObjectProperties) > 0) {
                $newChangeToken = $newObjectProperties[PropertyIds::CHANGE_TOKEN];
                // $changeToken was passed by reference. The value is changed here
                $changeToken = $newChangeToken === null ? null : $newChangeToken->getFirstValue();
            }
        }
    }

    /**
     * Updates properties of the specified object.
     *
     * @param string $repositoryId The identifier for the repository
     * @param string $objectId The identifier for the object. The repository might return a different/new object id
     * @param PropertiesInterface $properties The updated property values that must be applied to the object
     * @param string|null $changeToken The last change token of this object that the client received.
     *      The repository might return a new change token (default is <code>null</code>)
     * @param ExtensionDataInterface|null $extension
     * @throws CmisInvalidArgumentException If $objectId is empty
     */
    public function updateProperties(
        $repositoryId,
        & $objectId,
        PropertiesInterface $properties,
        & $changeToken = null,
        ExtensionDataInterface $extension = null
    ) {
        if (empty($objectId)) {
            throw new CmisInvalidArgumentException('Object id must not be empty!');
        }

        $this->flushCached($objectId);

        $url = $this->getObjectUrl($repositoryId, $objectId);

        if ($changeToken !== null && !$this->getSession()->get(SessionParameter::OMIT_CHANGE_TOKENS, false)) {
            $url->getQuery()->modify(array(Constants::PARAM_CHANGE_TOKEN => $changeToken));
        }

        $queryArray = $this->convertPropertiesToQueryArray($properties);
        $queryArray[Constants::CONTROL_CMISACTION] = Constants::CMISACTION_UPDATE_PROPERTIES;
        $queryArray[Constants::PARAM_SUCCINCT] = $this->getSuccinct() ? 'true' : 'false';
        $responseData = $this->post($url, $queryArray)->json();
        $newObject = $this->getJsonConverter()->convertObject($responseData);

        // $objectId was passed by reference. The value is changed here to new object id
        $objectId = null;
        if ($newObject !== null) {
            $objectId = $newObject->getId();
            $newObjectProperties = $newObject->getProperties()->getProperties();
            if ($changeToken !== null && count($newObjectProperties) > 0) {
                $newChangeToken = $newObjectProperties[PropertyIds::CHANGE_TOKEN];
                // $changeToken was passed by reference. The value is changed here
                $changeToken = $newChangeToken === null ? null : $newChangeToken->getFirstValue();
            }
        }
    }

    /**
     * @param string $identifier
     * @param mixed $additionalHashValues
     * @return array
     */
    protected function createCacheKey($identifier, $additionalHashValues)
    {
        return array(
            $identifier,
            sha1(is_array($additionalHashValues) ? serialize($additionalHashValues) : $additionalHashValues)
        );
    }

    /**
     * Returns TRUE if an object with cache key $identifier is currently cached.
     *
     * @param array $identifier
     * @return boolean
     */
    protected function isCached(array $identifier)
    {
        return isset($this->objectCache[$identifier[0]][$identifier[1]]);
    }

    /**
     * Gets the cached object with cache key $identifier.
     *
     * @param string $identifier
     * @return mixed
     */
    protected function getCached(array $identifier)
    {
        if ($this->isCached($identifier)) {
            return $this->objectCache[$identifier[0]][$identifier[1]];
        }
        return null;
    }

	/**
     * Gets the cached object with cache key $identifier.
     *
     * @param string $identifier
     * @param mixed $object
     * @return mixed
     */
    protected function cache(array $identifier, $object)
    {
        $this->objectCache[$identifier[0]][$identifier[1]] = $object;
        return $object;
    }

	/**
	 * Flushes all cached entries. This is implemented as a flush-all with
	 * no way to flush individual entries due to the way CMIS object data
	 * gets returned from CMIS. Two widely different object data sets may
	 * contain a reference to the same item and even with extensive cross
	 * referencing it would be technically unfeasible to selectively clear
	 * or reload an object by identifier. Such flushing would be inevitably
	 * flawed with edge cases of incomplete flushing or become so complex
	 * that it defeats the purpose of caching in the first place.
	 *
	 * Note that cache flushing only happens when modifying the repository
	 * contents - which should limit the negative impact. The cache is also
	 * not persistent and will only affect the current request. As such, it
	 * is implemented to optimise requests where the same object, type,
	 * policy etc. gets accessed multiple times.
	 *
	 * @return void
	 */
	protected function flushCached()
	{
		$this->objectCache = array();
	}
}
