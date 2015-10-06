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

use Dkd\PhpCmis\Bindings\CmisBindingInterface;
use Dkd\PhpCmis\CmisObject\CmisObjectInterface;
use Dkd\PhpCmis\Data\AceInterface;
use Dkd\PhpCmis\Data\AclInterface;
use Dkd\PhpCmis\Data\BulkUpdateObjectIdAndChangeTokenInterface;
use Dkd\PhpCmis\Data\DocumentInterface;
use Dkd\PhpCmis\Data\FolderInterface;
use Dkd\PhpCmis\Data\ObjectIdInterface;
use Dkd\PhpCmis\Data\ObjectTypeInterface;
use Dkd\PhpCmis\Data\PolicyInterface;
use Dkd\PhpCmis\Data\RelationshipInterface;
use Dkd\PhpCmis\Data\RepositoryInfoInterface;
use Dkd\PhpCmis\Definitions\TypeDefinitionInterface;
use Dkd\PhpCmis\Enum\AclPropagation;
use Dkd\PhpCmis\Enum\IncludeRelationships;
use Dkd\PhpCmis\Enum\RelationshipDirection;
use Dkd\PhpCmis\Enum\VersioningState;
use Dkd\PhpCmis\Exception\CmisObjectNotFoundException;
use GuzzleHttp\Stream\StreamInterface;

/**
 * A session is a connection to a CMIS repository with a specific user.
 *
 * CMIS itself is stateless. OpenCMIS uses the concept of a session to cache data across calls and to deal with
 * user authentication. The session object is also used as entry point to all CMIS operations and objects.
 * Because a session is only a client side concept, the session object needs not to be closed or released
 * when it's not needed anymore.
 *
 * Not all operations provided by this API might be supported by the connected repository.
 * Either OpenCMIS or the repository will throw an exception if an unsupported operation is called.
 * The capabilities of the repository can be discovered by evaluating the repository info (see getRepositoryInfo()).
 * Almost all methods might throw exceptions derived from CmisBaseException which is a runtime exception.
 * See the CMIS specification for a list of all operations and their exceptions.
 * Note that some incompliant repositories might throw other exception than you expect.
 *
 * Refer to the CMIS 1.0 specification or the CMIS 1.1 specification for details about the domain model,
 * terms, concepts, base types, properties, IDs and query names, query language, etc.
 */
interface SessionInterface
{
    /**
     * Applies ACL changes to an object and dependent objects. Only direct ACEs can be added and removed.
     *
     * @param ObjectIdInterface $objectId the ID the object
     * @param AceInterface[] $addAces of ACEs to be added or <code>null</code> if no ACEs should be added
     * @param AceInterface[] $removeAces list of ACEs to be removed or <code>null</code> if no ACEs should be removed
     * @param AclPropagation|null $aclPropagation value that defines the propagation of the ACE changes;
     *      <code>null</code> is equal to AclPropagation.REPOSITORYDETERMINED
     * @return AclInterface the new ACL of the object
     */
    public function applyAcl(
        ObjectIdInterface $objectId,
        $addAces = array(),
        $removeAces = array(),
        AclPropagation $aclPropagation = null
    );

    /**
     * Applies a set of policies to an object.
     *
     * @param ObjectIdInterface $objectId the ID the object
     * @param ObjectIdInterface[] $policyIds the IDs of the policies to be applied
     * @return mixed
     */
    public function applyPolicies(ObjectIdInterface $objectId, array $policyIds);

    /**
     * Updates multiple objects in one request.
     *
     * @param CmisObjectInterface[] $objects
     * @param mixed[] $properties
     * @param string[] $addSecondaryTypeIds
     * @param string[] $removeSecondaryTypeIds
     * @return BulkUpdateObjectIdAndChangeTokenInterface[]
     */
    public function bulkUpdateProperties(
        array $objects,
        array $properties,
        array $addSecondaryTypeIds,
        array $removeSecondaryTypeIds
    );

    /**
     * Clears all cached data.
     */
    public function clear();

    /**
     * Creates a new document. The stream in contentStream is consumed but not closed by this method.
     *
     * @param string[] $properties The property values that MUST be applied to the newly-created document object.
     * @param ObjectIdInterface|null $folderId If specified, the identifier for the folder that MUST be the parent
     *      folder for the newly-created document object. This parameter MUST be specified if the repository does NOT
     *      support the optional "unfiling" capability.
     * @param StreamInterface|null $contentStream The content stream that MUST be stored for the newly-created document
     *      object. The method of passing the contentStream to the server and the encoding mechanism will be specified
     *      by each specific binding. MUST be required if the type requires it.
     * @param VersioningState|null $versioningState An enumeration specifying what the versioning state of the
     *     newly-created object MUST be. Valid values are:
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
     * @param PolicyInterface[] $policies A list of policy ids that MUST be applied to the newly-created
     *      document object.
     * @param AceInterface[] $addAces A list of ACEs that MUST be added to the newly-created document object,
     *      either using the ACL from folderId if specified, or being applied if no folderId is specified.
     * @param AceInterface[] $removeAces A list of ACEs that MUST be removed from the newly-created document
     *      object, either using the ACL from folderId if specified, or being ignored if no folderId is specified.
     * @return ObjectIdInterface the object ID of the new document
     */
    public function createDocument(
        array $properties,
        ObjectIdInterface $folderId = null,
        StreamInterface $contentStream = null,
        VersioningState $versioningState = null,
        array $policies = array(),
        array $addAces = array(),
        array $removeAces = array()
    );

    /**
     * Creates a new document from a source document.
     *
     * @param ObjectIdInterface $source The identifier for the source document.
     * @param string[] $properties The property values that MUST be applied to the object. This list of properties
     *      SHOULD only contain properties whose values differ from the source document.
     * @param ObjectIdInterface|null $folderId If specified, the identifier for the folder that MUST be the parent
     *      folder for the newly-created document object. This parameter MUST be specified if the repository does NOT
     *      support the optional "unfiling" capability.
     * @param VersioningState|null $versioningState An enumeration specifying what the versioning state of the
     *     newly-created object MUST be. Valid values are:
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
     * @param PolicyInterface[] $policies A list of policy ids that MUST be applied to the newly-created
     *      document object.
     * @param AceInterface[] $addAces A list of ACEs that MUST be added to the newly-created document object,
     *      either using the ACL from folderId if specified, or being applied if no folderId is specified.
     * @param AceInterface[] $removeAces A list of ACEs that MUST be removed from the newly-created document
     *      object, either using the ACL from folderId if specified, or being ignored if no folderId is specified.
     * @return ObjectIdInterface the object ID of the new document
     */
    public function createDocumentFromSource(
        ObjectIdInterface $source,
        array $properties = array(),
        ObjectIdInterface $folderId = null,
        VersioningState $versioningState = null,
        array $policies = array(),
        array $addAces = array(),
        array $removeAces = array()
    );

    /**
     * Creates a new folder.
     *
     * @param string[] $properties
     * @param ObjectIdInterface $folderId
     * @param PolicyInterface[] $policies
     * @param AceInterface[] $addAces
     * @param AceInterface[] $removeAces
     * @return ObjectIdInterface the object ID of the new folder
     */
    public function createFolder(
        array $properties,
        ObjectIdInterface $folderId,
        array $policies = array(),
        array $addAces = array(),
        array $removeAces = array()
    );

    /**
     * Creates a new item.
     *
     * @param string[] $properties
     * @param ObjectIdInterface $folderId
     * @param PolicyInterface[] $policies
     * @param AceInterface[] $addAces
     * @param AceInterface[] $removeAces
     * @return ObjectIdInterface the object ID of the new item
     */
    public function createItem(
        array $properties,
        ObjectIdInterface $folderId,
        array $policies = array(),
        array $addAces = array(),
        array $removeAces = array()
    );

    /**
     * Creates an object ID from a String.
     *
     * @param string $id
     * @return ObjectIdInterface the object ID object
     */
    public function createObjectId($id);

    /**
     * Creates a new operation context object with the given properties.
     *
     * @param string[] $filter the property filter, a comma separated string of query names or "*" for all
     *      properties or <code>null</code> to let the repository determine a set of properties
     * @param boolean $includeAcls indicates whether ACLs should be included or not
     * @param boolean $includeAllowableActions indicates whether Allowable Actions should be included or not
     * @param boolean $includePolicies indicates whether policies should be included or not
     * @param IncludeRelationships|null $includeRelationships enum that indicates if and which
     *      relationships should be includes
     * @param string[] $renditionFilter the rendition filter or <code>null</code> for no renditions
     * @param boolean $includePathSegments indicates whether path segment or the relative path segment should
     *      be included or not
     * @param string|null $orderBy the object order, a comma-separated list of query names and the ascending
     * modifier "ASC" or the descending modifier "DESC" for each query name
     * @param boolean $cacheEnabled flag that indicates if the object cache should be used
     * @param integer $maxItemsPerPage the max items per page/batch
     * @return OperationContextInterface the newly created operation context object
     */
    public function createOperationContext(
        $filter = array(),
        $includeAcls = false,
        $includeAllowableActions = true,
        $includePolicies = false,
        IncludeRelationships $includeRelationships = null,
        array $renditionFilter = array(),
        $includePathSegments = true,
        $orderBy = null,
        $cacheEnabled = false,
        $maxItemsPerPage = 100
    );

    /**
     * Creates a new policy.
     *
     * @param string[] $properties
     * @param ObjectIdInterface $folderId
     * @param PolicyInterface[] $policies
     * @param AceInterface[] $addAces
     * @param AceInterface[] $removeAces
     * @return ObjectIdInterface the object ID of the new policy
     */
    public function createPolicy(
        array $properties,
        ObjectIdInterface $folderId,
        array $policies = array(),
        array $addAces = array(),
        array $removeAces = array()
    );

    /**
     * Creates a query statement for a query of one primary type joined by zero or more secondary types.
     *
     * Generates something like this:
     * `SELECT d.cmis:name,s.SecondaryStringProp FROM cmis:document AS d JOIN MySecondaryType AS s ON
     * d.cmis:objectId=s.cmis:objectId WHERE d.cmis:name LIKE ? ORDER BY d.cmis:name,s.SecondaryIntegerProp`
     *
     * @param string[] $selectPropertyIds the property IDs in the SELECT statement,
     *      if <code>null</code> all properties are selected
     * @param string[] $fromTypes a Map of type aliases (keys) and type IDs (values), the Map must contain
     *      exactly one primary type and zero or more secondary types
     * @param string|null $whereClause an optional WHERE clause with placeholders ('?'), see QueryStatement for details
     * @param string[] $orderByPropertyIds an optional list of properties IDs for the ORDER BY clause
     * @return QueryStatementInterface a new query statement object
     */
    public function createQueryStatement(
        array $selectPropertyIds,
        array $fromTypes,
        $whereClause = null,
        array $orderByPropertyIds = array()
    );

    /**
     * Creates a new relationship between 2 objects.
     *
     * @param string[] $properties
     * @param PolicyInterface[] $policies
     * @param AceInterface[] $addAces
     * @param AceInterface[] $removeAces
     * @return ObjectIdInterface|null the object ID of the new relationship
     */
    public function createRelationship(
        array $properties,
        array $policies = array(),
        array $addAces = array(),
        array $removeAces = array()
    );

    /**
     * Creates a new type.
     *
     * @param TypeDefinitionInterface $type
     * @return ObjectTypeInterface the new type definition
     */
    public function createType(TypeDefinitionInterface $type);

    /**
     * Deletes an object and, if it is a document, all versions in the version series.
     *
     * @param ObjectIdInterface $objectId the ID of the object
     * @param boolean $allVersions if this object is a document this parameter defines
     *      if only this version or all versions should be deleted
     */
    public function delete(ObjectIdInterface $objectId, $allVersions = true);

    /**
     * Deletes a type.
     *
     * @param string $typeId the ID of the type to delete
     */
    public function deleteType($typeId);

    /**
     * Fetches the ACL of an object from the repository.
     *
     * @param ObjectIdInterface $objectId the ID the object
     * @param boolean $onlyBasicPermissions if <code>true</code> the repository should express the ACL only with the
     *      basic permissions defined in the CMIS specification; if <code>false</code> the repository can express the
     *      ACL with basic and repository specific permissions
     * @return AclInterface the ACL of the object
     */
    public function getAcl(ObjectIdInterface $objectId, $onlyBasicPermissions);

    /**
     * Returns the underlying binding object.
     *
     * @return CmisBindingInterface the binding object
     */
    public function getBinding();

    /**
     * Returns all checked out documents with the given OperationContext.
     *
     * @param OperationContextInterface|null $context
     * @return DocumentInterface[]
     */
    public function getCheckedOutDocs(OperationContextInterface $context = null);

    /**
     * Returns the content changes.
     *
     * @param string $changeLogToken the change log token to start from or <code>null</code> to start from
     *      the first available event in the repository
     * @param boolean $includeProperties indicates whether changed properties should be included in the result or not
     * @param integer|null $maxNumItems maximum numbers of events
     * @param OperationContextInterface|null $context the OperationContext
     * @return ChangeEventsInterface the change events
     */
    public function getContentChanges(
        $changeLogToken,
        $includeProperties,
        $maxNumItems = null,
        OperationContextInterface $context = null
    );

    /**
     * Retrieves the main content stream of a document.
     *
     * @param ObjectIdInterface $docId the ID of the document
     * @param string|null $streamId the stream ID
     * @param integer|null $offset the offset of the stream or <code>null</code> to read the stream from the beginning
     * @param integer|null $length the maximum length of the stream or <code>null</code> to read to the end of the
     *      stream
     * @return StreamInterface|null the content stream or <code>null</code> if the
     *      document has no content stream
     */
    public function getContentStream(ObjectIdInterface $docId, $streamId = null, $offset = null, $length = null);

    /**
     * Returns the current default operation parameters for filtering, paging and caching.
     *
     * @return OperationContextInterface the default operation context
     */
    public function getDefaultContext();

    /**
     * Returns the latest change log token.
     *
     * In contrast to the repository info, this change log token is *not cached*.
     * This method requests the token from the repository every single time it is called.
     *
     * @return string|null the latest change log token or <code>null</code> if the repository doesn't provide one
     */
    public function getLatestChangeLogToken();

    /**
     * Returns the latest version in a version series.
     *
     * @param ObjectIdInterface $objectId the document ID of an arbitrary version in the version series
     * @param boolean $major if <code>true</code> the latest major version will be returned,
     *      otherwise the very last version will be returned
     * @param OperationContextInterface|null $context the OperationContext to use
     * @return DocumentInterface the latest document version
     */
    public function getLatestDocumentVersion(
        ObjectIdInterface $objectId,
        $major = false,
        OperationContextInterface $context = null
    );

    /**
     * Get the current locale to be used for this session.
     *
     * @return \Locale the current locale, may be <code>null</code>
     */
    public function getLocale();

    /**
     * @param ObjectIdInterface $objectId the object ID
     * @param OperationContextInterface|null $context the OperationContext to use
     * @return CmisObjectInterface the requested object
     * @throws CmisObjectNotFoundException - if an object with the given ID doesn't exist
     */
    public function getObject(ObjectIdInterface $objectId, OperationContextInterface $context = null);

    /**
     * Returns a CMIS object from the session cache. If the object is not in the cache or the given OperationContext
     * has caching turned off, it will load the object from the repository and puts it into the cache.
     * This method might return a stale object if the object has been found in the cache and has been changed in or
     * removed from the repository. Use CmisObject::refresh() and CmisObject::refreshIfOld() to update the object
     * if necessary.
     *
     * @param string $path the object path
     * @param OperationContextInterface|null $context the OperationContext to use
     * @return CmisObjectInterface Returns a CMIS object from the session cache.
     * @throws CmisObjectNotFoundException - if an object with the given ID doesn't exist
     */
    public function getObjectByPath($path, OperationContextInterface $context = null);

    /**
     * Gets a factory object that provides methods to create the objects used by this API.
     *
     * @return ObjectFactoryInterface the repository info
     */
    public function getObjectFactory();

    /**
     * Fetches the relationships from or to an object from the repository.
     *
     * @param ObjectIdInterface $objectId
     * @param boolean $includeSubRelationshipTypes
     * @param RelationshipDirection $relationshipDirection
     * @param ObjectTypeInterface $type
     * @param OperationContextInterface|null $context
     * @return RelationshipInterface[]
     */
    public function getRelationships(
        ObjectIdInterface $objectId,
        $includeSubRelationshipTypes,
        RelationshipDirection $relationshipDirection,
        ObjectTypeInterface $type,
        OperationContextInterface $context = null
    );

    /**
     * Returns the repository info of the repository associated with this session.
     *
     * @return RepositoryInfoInterface the repository info
     */
    public function getRepositoryInfo();

    /**
     * Gets the root folder of the repository with the given OperationContext.
     *
     * @param OperationContextInterface|null $context
     * @return FolderInterface the root folder object
     */
    public function getRootFolder(OperationContextInterface $context = null);

    /**
     * Gets the type children of a type.
     *
     * @param string $typeId the type ID or <code>null</code> to request the base types
     * @param boolean $includePropertyDefinitions indicates whether the property definitions should be included or not
     * @return ObjectTypeInterface[] the type iterator
     * @throws CmisObjectNotFoundException - if a type with the given type ID doesn't exist
     */
    public function getTypeChildren($typeId, $includePropertyDefinitions);

    /**
     * Gets the definition of a type.
     *
     * @param string $typeId the ID of the type
     * @param boolean $useCache specifies if the type definition should be first looked up in the type definition
     *     cache, if it is set to <code>false</code> or the type definition is not in the cache, the type definition is
     *     loaded from the repository
     * @return ObjectTypeInterface the type definition
     * @throws CmisObjectNotFoundException - if a type with the given type ID doesn't exist
     */
    public function getTypeDefinition($typeId, $useCache = true);

    /**
     * Gets the type descendants of a type.
     *
     * @param string $typeId the type ID or <code>null</code> to request the base types
     * @param integer $depth indicates whether the property definitions should be included or not
     * @param boolean $includePropertyDefinitions the tree depth, must be greater than 0 or -1 for infinite depth
     * @return TreeInterface A tree that contains ObjectTypeInterface objects
     * @see ObjectTypeInterface ObjectTypeInterface contained in returned Tree
     * @throws CmisObjectNotFoundException - if a type with the given type ID doesn't exist
     */
    public function getTypeDescendants($typeId, $depth, $includePropertyDefinitions);

    /**
     * Sends a query to the repository using the given OperationContext. (See CMIS spec "2.1.10 Query".)
     *
     * @param string $statement the query statement (CMIS query language)
     * @param boolean $searchAllVersions specifies whether non-latest document versions should be included or not,
     *      <code>true</code> searches all document versions, <code>false</code> only searches latest document versions
     * @param OperationContextInterface|null $context the operation context to use
     * @return QueryResultInterface[]
     */
    public function query($statement, $searchAllVersions = false, OperationContextInterface $context = null);

    /**
     * Builds a CMIS query and returns the query results as an iterator of CmisObject objects.
     *
     * @param string $typeId the ID of the object type
     * @param string|null $where the WHERE part of the query
     * @param boolean $searchAllVersions specifies whether non-latest document versions should be included or not,
     *      <code>true</code> searches all document versions, <code>false</code> only searches latest document versions
     * @param OperationContextInterface|null $context the operation context to use
     * @return CmisObjectInterface[]
     */
    public function queryObjects(
        $typeId,
        $where = null,
        $searchAllVersions = false,
        OperationContextInterface $context = null
    );

    /**
     * Removes the given object from the cache.
     *
     * @param ObjectIdInterface $objectId
     */
    public function removeObjectFromCache(ObjectIdInterface $objectId);

    /**
     * Removes a set of policies from an object. This operation is not atomic.
     * If it fails some policies might already be removed.
     *
     * @param ObjectIdInterface $objectId the ID the object
     * @param ObjectIdInterface[] $policyIds the IDs of the policies to be removed
     */
    public function removePolicy(ObjectIdInterface $objectId, array $policyIds);

    /**
     * Removes the direct ACEs of an object and sets the provided ACEs.
     * The changes are local to the given object and are not propagated to dependent objects.
     *
     * @param ObjectIdInterface $objectId
     * @param AceInterface[] $aces
     * @return AclInterface the new ACL of the object
     */
    public function setAcl(ObjectIdInterface $objectId, array $aces);

    /**
     * Sets the current session parameters for filtering, paging and caching.
     *
     * @param OperationContextInterface $context the OperationContext to be used for the session;
     *      if null, a default context is used
     */
    public function setDefaultContext(OperationContextInterface $context);

    /**
     * Updates an existing type.
     *
     * @param TypeDefinitionInterface $type the type definition updates
     * @return ObjectTypeInterface the updated type definition
     */
    public function updateType(TypeDefinitionInterface $type);
}
