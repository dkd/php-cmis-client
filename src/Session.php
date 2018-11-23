<?php
namespace Dkd\PhpCmis;

/*
 * This file is part of php-cmis-client.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Bindings\CmisBindingInterface;
use Dkd\PhpCmis\Bindings\CmisBindingsHelper;
use Dkd\PhpCmis\CmisObject\CmisObjectInterface;
use Dkd\PhpCmis\Data\AceInterface;
use Dkd\PhpCmis\Data\AclInterface;
use Dkd\PhpCmis\Data\BulkUpdateObjectIdAndChangeTokenInterface;
use Dkd\PhpCmis\Data\DocumentInterface;
use Dkd\PhpCmis\Data\FolderInterface;
use Dkd\PhpCmis\Data\ObjectDataInterface;
use Dkd\PhpCmis\Data\ObjectIdInterface;
use Dkd\PhpCmis\Data\ObjectTypeInterface;
use Dkd\PhpCmis\Data\PolicyInterface;
use Dkd\PhpCmis\Data\RelationshipInterface;
use Dkd\PhpCmis\Data\RepositoryInfoInterface;
use Dkd\PhpCmis\DataObjects\ObjectId;
use Dkd\PhpCmis\Definitions\TypeDefinitionContainerInterface;
use Dkd\PhpCmis\Definitions\TypeDefinitionInterface;
use Dkd\PhpCmis\Definitions\TypeDefinitionListInterface;
use Dkd\PhpCmis\Enum\AclPropagation;
use Dkd\PhpCmis\Enum\BaseTypeId;
use Dkd\PhpCmis\Enum\CmisVersion;
use Dkd\PhpCmis\Enum\IncludeRelationships;
use Dkd\PhpCmis\Enum\RelationshipDirection;
use Dkd\PhpCmis\Enum\Updatability;
use Dkd\PhpCmis\Enum\VersioningState;
use Dkd\PhpCmis\Exception\CmisInvalidArgumentException;
use Dkd\PhpCmis\Exception\CmisNotSupportedException;
use Dkd\PhpCmis\Exception\CmisObjectNotFoundException;
use Dkd\PhpCmis\Exception\CmisRuntimeException;
use Dkd\PhpCmis\Exception\IllegalStateException;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\CacheProvider;
use GuzzleHttp\Stream\StreamInterface;

/**
 * Class Session
 */
class Session implements SessionInterface
{
    /**
     * @var CmisBindingInterface
     */
    protected $binding;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var OperationContextInterface
     */
    private $defaultContext;

    /**
     * @var CmisBindingsHelper
     */
    protected $cmisBindingHelper;

    /**
     * @var ObjectFactoryInterface
     */
    private $objectFactory;

    /**
     * @var RepositoryInfoInterface
     */
    protected $repositoryInfo;

    /**
     * @var array
     */
    protected $parameters = [];

    /**
     * @var Cache
     */
    protected $typeDefinitionCache;

    /**
     * @var Cache
     */
    protected $objectTypeCache;

    /**
     * @var Updatability[]
     */
    protected static $createUpdatability = [];

    /**
     * @var Updatability[]
     */
    protected static $createAndCheckoutUpdatability = [];

    /**
     * @param array $parameters
     * @param ObjectFactoryInterface|null $objectFactory
     * @param Cache|null $cache
     * @param Cache|null $typeDefinitionCache
     * @param Cache|null $objectTypeCache
     * @param CmisBindingsHelper|null $cmisBindingHelper
     * @throws CmisInvalidArgumentException
     * @throws IllegalStateException
     */
    public function __construct(
        array $parameters,
        ObjectFactoryInterface $objectFactory = null,
        Cache $cache = null,
        Cache $typeDefinitionCache = null,
        Cache $objectTypeCache = null,
        CmisBindingsHelper $cmisBindingHelper = null
    ) {
        if (empty($parameters)) {
            throw new CmisInvalidArgumentException('No parameters provided!', 1408115280);
        }

        $this->parameters = $parameters;
        $this->objectFactory = $objectFactory === null ? $this->createObjectFactory() : $objectFactory;
        $this->cache = $cache === null ? $this->createCache() : $cache;
        $this->typeDefinitionCache = $typeDefinitionCache === null ? $this->createCache() : $typeDefinitionCache;
        $this->objectTypeCache = $objectTypeCache === null ? $this->createCache() : $objectTypeCache;
        $this->cmisBindingHelper = $cmisBindingHelper === null ? new CmisBindingsHelper() : $cmisBindingHelper;

        $this->defaultContext = new OperationContext();
        $this->defaultContext->setCacheEnabled(true);

        $this->binding = $this->getCmisBindingHelper()->createBinding(
            $this->parameters,
            $this->typeDefinitionCache
        );

        if (!isset($this->parameters[SessionParameter::REPOSITORY_ID])) {
            throw new IllegalStateException('Repository ID is not set!');
        }

        $this->repositoryInfo = $this->getBinding()->getRepositoryService()->getRepositoryInfo(
            $this->parameters[SessionParameter::REPOSITORY_ID]
        );

        self::$createUpdatability = [
            Updatability::cast(Updatability::ONCREATE),
            Updatability::cast(Updatability::READWRITE)
        ];

        self::$createAndCheckoutUpdatability = [
            Updatability::cast(Updatability::ONCREATE),
            Updatability::cast(Updatability::READWRITE),
            Updatability::cast(Updatability::WHENCHECKEDOUT)
        ];
    }

    /**
     * Create an object factory based on the SessionParameter::OBJECT_FACTORY_CLASS. If not set it returns an instance
     * of ObjectFactory.
     *
     * @return ObjectFactoryInterface
     * @throws \RuntimeException
     */
    protected function createObjectFactory()
    {
        try {
            if (isset($this->parameters[SessionParameter::OBJECT_FACTORY_CLASS])) {
                $objectFactoryClass = new $this->parameters[SessionParameter::OBJECT_FACTORY_CLASS];
            } else {
                $objectFactoryClass = $this->createDefaultObjectFactoryInstance();
            }

            if (!($objectFactoryClass instanceof ObjectFactoryInterface)) {
                throw new \RuntimeException('Class does not implement ObjectFactoryInterface!', 1408354119);
            }

            $objectFactoryClass->initialize($this, $this->parameters);

            return $objectFactoryClass;
        } catch (\Exception $exception) {
            throw new \RuntimeException(
                'Unable to create object factory: ' . $exception,
                1408354120
            );
        }
    }

    /**
     * Returns an instance of the ObjectFactory.
     * This methods is primarily required for unit testing.
     *
     * @return ObjectFactory
     */
    protected function createDefaultObjectFactoryInstance()
    {
        return new ObjectFactory();
    }

    /**
     * Create a cache instance based on the given session parameter SessionParameter::CACHE_CLASS.
     * If no session parameter SessionParameter::CACHE_CLASS is defined, the default Cache implementation will be used.
     *
     * @return Cache
     * @throws \InvalidArgumentException
     */
    protected function createCache()
    {
        try {
            if (isset($this->parameters[SessionParameter::CACHE_CLASS])) {
                $cache = new $this->parameters[SessionParameter::CACHE_CLASS];
            } else {
                $cache = $this->createDefaultCacheInstance();
            }

            if (!($cache instanceof CacheProvider)) {
                throw new \RuntimeException(
                    sprintf(
                        'Class %s does not subclass %s!',
                        get_class($cache),
                        CacheProvider::class
                    ),
                    1408354124
                );
            }

            return $cache;
        } catch (\Exception $exception) {
            throw new CmisInvalidArgumentException(
                'Unable to create cache: ' . $exception,
                1408354123
            );
        }
    }

    /**
     * Returns an instance of the Doctrine ArrayCache.
     * This methods is primarily required for unit testing.
     *
     * @return CacheProvider
     */
    protected function createDefaultCacheInstance()
    {
        return new ArrayCache();
    }

    /**
     * Get the cache instance
     *
     * @return CacheProvider
     */
    public function getCache()
    {
        return $this->cache;
    }

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
        $addAces = [],
        $removeAces = [],
        AclPropagation $aclPropagation = null
    ) {
        // TODO: Implement applyAcl() method.
    }

    /**
     * Applies a set of policies to an object.
     *
     * @param ObjectIdInterface $objectId the ID the object
     * @param ObjectIdInterface[] $policyIds the IDs of the policies to be applied
     * @return mixed
     */
    public function applyPolicies(ObjectIdInterface $objectId, array $policyIds)
    {
        // TODO: Implement applyPolicy() method.
    }

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
    ) {
        // TODO: Implement bulkUpdateProperties() method.
    }

    /**
     * Clears all cached data.
     */
    public function clear()
    {
        $this->getCache()->flushAll();
    }

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
     * @return ObjectIdInterface|null the object ID of the new document or <code>null</code> if the document could not
     *      be created.
     * @throws CmisInvalidArgumentException Throws an <code>CmisInvalidArgumentException</code> if empty
     *      property list is given
     */
    public function createDocument(
        array $properties,
        ObjectIdInterface $folderId = null,
        StreamInterface $contentStream = null,
        VersioningState $versioningState = null,
        array $policies = [],
        array $addAces = [],
        array $removeAces = []
    ) {
        if (empty($properties)) {
            throw new CmisInvalidArgumentException('Properties must not be empty!');
        }

        $objectId = $this->getBinding()->getObjectService()->createDocument(
            $this->getRepositoryId(),
            $this->getObjectFactory()->convertProperties(
                $properties,
                null,
                [],
                self::$createAndCheckoutUpdatability
            ),
            $folderId === null ? null : $folderId->getId(),
            $contentStream,
            $versioningState,
            $this->getObjectFactory()->convertPolicies($policies),
            $this->getObjectFactory()->convertAces($addAces),
            $this->getObjectFactory()->convertAces($removeAces),
            null
        );

        if ($objectId === null) {
            return null;
        }

        return $this->createObjectId($objectId);
    }

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
     * @return ObjectIdInterface|null the object ID of the new document or <code>null</code> if the document could not
     *      be created.
     * @throws CmisInvalidArgumentException Throws an <code>CmisInvalidArgumentException</code> if empty
     *      property list is given
     */
    public function createDocumentFromSource(
        ObjectIdInterface $source,
        array $properties = [],
        ObjectIdInterface $folderId = null,
        VersioningState $versioningState = null,
        array $policies = [],
        array $addAces = [],
        array $removeAces = []
    ) {
        if (!$source instanceof CmisObjectInterface) {
            $sourceObject = $this->getObject($source);
        } else {
            $sourceObject = $source;
        }

        $type = $sourceObject->getType();
        $secondaryTypes = $sourceObject->getSecondaryTypes();

        if ($secondaryTypes === null) {
            $secondaryTypes = [];
        }

        if (!BaseTypeId::cast($type->getBaseTypeId())->equals(BaseTypeId::CMIS_DOCUMENT)) {
            throw new CmisInvalidArgumentException('Source object must be a document!');
        }

        $objectId = $this->getBinding()->getObjectService()->createDocumentFromSource(
            $this->getRepositoryId(),
            $source->getId(),
            $this->getObjectFactory()->convertProperties(
                $properties,
                $type,
                $secondaryTypes,
                self::$createAndCheckoutUpdatability
            ),
            $folderId === null ? null : $folderId->getId(),
            $versioningState,
            $this->getObjectFactory()->convertPolicies($policies),
            $this->getObjectFactory()->convertAces($addAces),
            $this->getObjectFactory()->convertAces($removeAces),
            null
        );

        if ($objectId === null) {
            return null;
        }

        return $this->createObjectId($objectId);
    }

    /**
     * Creates a new folder.
     *
     * @param string[] $properties
     * @param ObjectIdInterface $folderId
     * @param PolicyInterface[] $policies
     * @param AceInterface[] $addAces
     * @param AceInterface[] $removeAces
     * @return ObjectIdInterface the object ID of the new folder
     * @throws CmisInvalidArgumentException Throws an <code>CmisInvalidArgumentException</code> if empty
     *      property list is given
     */
    public function createFolder(
        array $properties,
        ObjectIdInterface $folderId,
        array $policies = [],
        array $addAces = [],
        array $removeAces = []
    ) {
        if (empty($properties)) {
            throw new CmisInvalidArgumentException('Properties must not be empty!');
        }

        $objectId = $this->getBinding()->getObjectService()->createFolder(
            $this->getRepositoryId(),
            $this->getObjectFactory()->convertProperties($properties),
            $folderId->getId(),
            $this->getObjectFactory()->convertPolicies($policies),
            $this->getObjectFactory()->convertAces($addAces),
            $this->getObjectFactory()->convertAces($removeAces),
            null
        );

        return $this->createObjectId($objectId);
    }

    /**
     * Creates a new item.
     *
     * @param string[] $properties
     * @param ObjectIdInterface $folderId
     * @param PolicyInterface[] $policies
     * @param AceInterface[] $addAces
     * @param AceInterface[] $removeAces
     * @return ObjectIdInterface the object ID of the new item
     * @throws CmisInvalidArgumentException Throws an <code>CmisInvalidArgumentException</code> if empty
     *      property list is given
     */
    public function createItem(
        array $properties,
        ObjectIdInterface $folderId,
        array $policies = [],
        array $addAces = [],
        array $removeAces = []
    ) {
        if (empty($properties)) {
            throw new CmisInvalidArgumentException('Properties must not be empty!');
        }

        $objectId = $this->getBinding()->getObjectService()->createItem(
            $this->getRepositoryId(),
            $this->getObjectFactory()->convertProperties($properties),
            $folderId->getId(),
            $this->getObjectFactory()->convertPolicies($policies),
            $this->getObjectFactory()->convertAces($addAces),
            $this->getObjectFactory()->convertAces($removeAces),
            null
        );

        return $this->createObjectId($objectId);
    }

    /**
     * Creates an object ID from a String.
     *
     * @param string $id
     * @return ObjectIdInterface the object ID object
     */
    public function createObjectId($id)
    {
        return new ObjectId($id);
    }

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
        $filter = [],
        $includeAcls = false,
        $includeAllowableActions = true,
        $includePolicies = false,
        IncludeRelationships $includeRelationships = null,
        array $renditionFilter = [],
        $includePathSegments = true,
        $orderBy = null,
        $cacheEnabled = false,
        $maxItemsPerPage = 100
    ) {
        $operationContext = new OperationContext();
        $operationContext->setFilter($filter);
        $operationContext->setIncludeAcls($includeAcls);
        $operationContext->setIncludeAllowableActions($includeAllowableActions);
        $operationContext->setIncludePolicies($includePolicies);
        if ($includeRelationships !== null) {
            $operationContext->setIncludeRelationships($includeRelationships);
        }
        $operationContext->setRenditionFilter($renditionFilter);
        $operationContext->setIncludePathSegments($includePathSegments);
        if (!empty($orderBy)) {
            $operationContext->setOrderBy($orderBy);
        }
        $operationContext->setCacheEnabled($cacheEnabled);
        $operationContext->setMaxItemsPerPage($maxItemsPerPage);

        return $operationContext;
    }

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
        array $policies = [],
        array $addAces = [],
        array $removeAces = []
    ) {
        // TODO: Implement createPolicy() method.
    }

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
     * @throws CmisInvalidArgumentException
     */
    public function createQueryStatement(
        array $selectPropertyIds,
        array $fromTypes,
        $whereClause = null,
        array $orderByPropertyIds = []
    ) {
        if (empty($selectPropertyIds)) {
            throw new CmisInvalidArgumentException('Select property IDs must not be empty');
        }
        if (empty($fromTypes)) {
            throw new CmisInvalidArgumentException('From types must not be empty');
        }

        return new QueryStatement($this, null, $selectPropertyIds, $fromTypes, $whereClause, $orderByPropertyIds);
    }

    /**
     * Creates a new relationship between 2 objects.
     *
     * @param string[] $properties
     * @param PolicyInterface[] $policies
     * @param AceInterface[] $addAces
     * @param AceInterface[] $removeAces
     * @return ObjectIdInterface|null the object ID of the new relationship or <code>null</code> if the relationship
     *      could not be created
     */
    public function createRelationship(
        array $properties,
        array $policies = [],
        array $addAces = [],
        array $removeAces = []
    ) {
        if (empty($properties)) {
            throw new CmisInvalidArgumentException('Properties must not be empty!');
        }

        $newObjectId = $this->getBinding()->getObjectService()->createRelationship(
            $this->getRepositoryId(),
            $this->getObjectFactory()->convertProperties($properties, null, [], self::$createUpdatability),
            $this->getObjectFactory()->convertPolicies($policies),
            $this->getObjectFactory()->convertAces($addAces),
            $this->getObjectFactory()->convertAces($removeAces)
        );

        if ($newObjectId === null) {
            return null;
        }

        return $this->createObjectId($newObjectId);
    }

    /**
     * Creates a new type.
     *
     * @param TypeDefinitionInterface $type
     * @return ObjectTypeInterface the new type definition
     * @throws CmisNotSupportedException If repository version 1.0
     */
    public function createType(TypeDefinitionInterface $type)
    {
        if ($this->getRepositoryInfo()->getCmisVersion() == CmisVersion::cast(CmisVersion::CMIS_1_0)) {
            throw new CmisNotSupportedException('This method is not supported for CMIS 1.0 repositories.');
        }

        return $this->convertTypeDefinition(
            $this->getBinding()->getRepositoryService()->createType($this->getRepositoryInfo()->getId(), $type)
        );
    }

    /**
     * Deletes an object and, if it is a document, all versions in the version series.
     *
     * @param ObjectIdInterface $objectId the ID of the object
     * @param boolean $allVersions if this object is a document this parameter defines
     *      if only this version or all versions should be deleted
     */
    public function delete(ObjectIdInterface $objectId, $allVersions = true)
    {
        $this->getBinding()->getObjectService()->deleteObject(
            $this->getRepositoryId(),
            $objectId->getId(),
            $allVersions
        );
        $this->removeObjectFromCache($objectId);
    }

    /**
     * Deletes a type.
     *
     * @param string $typeId the ID of the type to delete
     * @throws CmisNotSupportedException If repository version 1.0
     */
    public function deleteType($typeId)
    {
        if ($this->getRepositoryInfo()->getCmisVersion() == CmisVersion::cast(CmisVersion::CMIS_1_0)) {
            throw new CmisNotSupportedException('This method is not supported for CMIS 1.0 repositories.');
        }

        $this->getBinding()->getRepositoryService()->deleteType($this->getRepositoryId(), $typeId);
        $this->removeObjectFromCache($this->createObjectId($typeId));
    }

    /**
     * Fetches the ACL of an object from the repository.
     *
     * @param ObjectIdInterface $objectId the ID the object
     * @param boolean $onlyBasicPermissions if <code>true</code> the repository should express the ACL only with the
     *      basic permissions defined in the CMIS specification; if <code>false</code> the repository can express the
     *      ACL with basic and repository specific permissions
     * @return AclInterface the ACL of the object
     */
    public function getAcl(ObjectIdInterface $objectId, $onlyBasicPermissions)
    {
        return $this->getBinding()->getAclService()->getAcl($this->getRepositoryId(), $objectId->getId(), $onlyBasicPermissions);
    }

    /**
     * Returns the underlying binding object.
     *
     * @return CmisBindingInterface the binding object
     */
    public function getBinding()
    {
        return $this->binding;
    }

    /**
     * Returns all checked out documents with the given OperationContext.
     *
     * @param OperationContextInterface|null $context
     * @return DocumentInterface[]
     */
    public function getCheckedOutDocs(OperationContextInterface $context = null)
    {
        // TODO: Implement getCheckedOutDocs() method.
    }

    /**
     * @return CmisBindingsHelper
     */
    protected function getCmisBindingHelper()
    {
        return $this->cmisBindingHelper;
    }

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
    ) {
        if ($context === null) {
            $context = $this->getDefaultContext();
        }

        $objectList = $this->getBinding()->getDiscoveryService()->getContentChanges(
            $this->getRepositoryInfo()->getId(),
            $changeLogToken,
            $includeProperties,
            $context->isIncludePolicies(),
            $context->isIncludeAcls(),
            $maxNumItems
        );

        return $this->getObjectFactory()->convertChangeEvents($changeLogToken, $objectList);
    }

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
    public function getContentStream(ObjectIdInterface $docId, $streamId = null, $offset = null, $length = null)
    {
        return $this->getBinding()->getObjectService()->getContentStream(
            $this->getRepositoryId(),
            $docId->getId(),
            $streamId,
            $offset,
            $length
        );
    }

    /**
     * Returns the current default operation parameters for filtering, paging and caching.
     *
     * @return OperationContextInterface the default operation context
     */
    public function getDefaultContext()
    {
        return $this->defaultContext;
    }

    /**
     * Returns the latest change log token.
     *
     * In contrast to the repository info, this change log token is *not cached*.
     * This method requests the token from the repository every single time it is called.
     *
     * @return string|null the latest change log token or <code>null</code> if the repository doesn't provide one
     */
    public function getLatestChangeLogToken()
    {
        // TODO: Implement getLatestChangeLogToken() method.
    }

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
    ) {
        // TODO: Implement getLatestDocumentVersion() method.
    }

    /**
     * Get the current locale to be used for this session.
     *
     * @return \Locale the current locale, may be <code>null</code>
     */
    public function getLocale()
    {
        // TODO: Implement getLocale() method.
    }

    /**
     * @param ObjectIdInterface $objectId the object ID
     * @param OperationContextInterface|null $context the OperationContext to use
     * @return CmisObjectInterface the requested object
     * @throws CmisObjectNotFoundException - if an object with the given ID doesn't exist
     */
    public function getObject(ObjectIdInterface $objectId, OperationContextInterface $context = null)
    {
        if ($context === null) {
            $context = $this->getDefaultContext();
        }

        $objectIdString = $objectId->getId();
        $cacheIdentity = $objectIdString . '|' . $context->getCacheKey();

        if ($this->getCache()->contains($cacheIdentity)) {
            return $this->getCache()->fetch($cacheIdentity);
        }

        $objectData = $this->getBinding()->getObjectService()->getObject(
            $this->getRepositoryInfo()->getId(),
            $objectIdString,
            $context->getQueryFilterString(),
            $context->isIncludeAllowableActions(),
            $context->getIncludeRelationships(),
            $context->getRenditionFilterString(),
            $context->isIncludePolicies(),
            $context->isIncludeAcls(),
            null
        );

        if (!$objectData instanceof ObjectDataInterface) {
            throw new CmisObjectNotFoundException('Could not find object for given id.');
        }

        $object = $this->getObjectFactory()->convertObject($objectData, $context);
        $this->getCache()->save($cacheIdentity, $object);
        return $object;
    }

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
     * @throws CmisInvalidArgumentException Throws an <code>CmisInvalidArgumentException</code>
     *      if path is empty.
     * @throws CmisObjectNotFoundException - if an object with the given path doesn't exist
     */
    public function getObjectByPath($path, OperationContextInterface $context = null)
    {
        if (empty($path)) {
            throw new CmisInvalidArgumentException('Path must not be empty.');
        }

        if ($context === null) {
            $context = $this->getDefaultContext();
        }

        $objectData = $this->getBinding()->getObjectService()->getObjectByPath(
            $this->getRepositoryInfo()->getId(),
            $path,
            $context->getQueryFilterString(),
            $context->isIncludeAllowableActions(),
            $context->getIncludeRelationships(),
            $context->getRenditionFilterString(),
            $context->isIncludePolicies(),
            $context->isIncludeAcls()
        );

        if (!$objectData instanceof ObjectDataInterface) {
            throw new CmisObjectNotFoundException(sprintf('Could not find object for given path "%s".', $path));
        }

        // TODO: Implement cache!

        return $this->getObjectFactory()->convertObject($objectData, $context);
    }

    /**
     * Gets a factory object that provides methods to create the objects used by this API.
     *
     * @return ObjectFactoryInterface the repository info
     */
    public function getObjectFactory()
    {
        return $this->objectFactory;
    }

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
    ) {

        if ($context === null) {
            $context = $this->getDefaultContext();
        }

        // TODO: Implement cache!

        return $this->getBinding()->getRelationshipService()->getObjectRelationships(
            $this->getRepositoryId(),
            $objectId->getId(),
            $includeSubRelationshipTypes,
            $relationshipDirection,
            $type->getId()
        );
    }

    /**
     * Returns the repository info of the repository associated with this session.
     *
     * @return RepositoryInfoInterface the repository info
     */
    public function getRepositoryInfo()
    {
        return $this->repositoryInfo;
    }

    /**
     * Returns the repository id.
     *
     * @return string the repository id
     */
    public function getRepositoryId()
    {
        return $this->getRepositoryInfo()->getId();
    }

    /**
     * Gets the root folder of the repository with the given OperationContext.
     *
     * @param OperationContextInterface|null $context
     * @return FolderInterface the root folder object
     * @throws CmisRuntimeException
     */
    public function getRootFolder(OperationContextInterface $context = null)
    {
        $rootFolderId = $this->getRepositoryInfo()->getRootFolderId();

        $rootFolder = $this->getObject(
            $this->createObjectId($rootFolderId),
            $context === null ? $this->getDefaultContext() : $context
        );

        if (!($rootFolder instanceof FolderInterface)) {
            throw new CmisRuntimeException('Root folder object is not a folder!', 1423735889);
        }

        return $rootFolder;
    }

    /**
     * Gets the type children of a type.
     *
     * @param string $typeId the type ID or <code>null</code> to request the base types
     * @param boolean $includePropertyDefinitions indicates whether the property definitions should be included or not
     * @return TypeDefinitionListInterface the type iterator
     * @throws CmisObjectNotFoundException - if a type with the given type ID doesn't exist
     */
    public function getTypeChildren($typeId, $includePropertyDefinitions)
    {
        return $this->getBinding()->getRepositoryService()->getTypeChildren(
            $this->getRepositoryId(),
            $typeId,
            $includePropertyDefinitions,
            999, // set max items to 999 - TODO: Implement CollectionIterable() method to iterate over all objects.
            0 // TODO: Implement CollectionIterable() method to iterate over all objects.
        );
    }

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
    public function getTypeDefinition($typeId, $useCache = true)
    {
        // TODO: Implement cache!
        $typeDefinition = $this->getBinding()->getRepositoryService()->getTypeDefinition(
            $this->getRepositoryId(),
            $typeId
        );

        return $this->convertTypeDefinition($typeDefinition);
    }

    /**
     * Gets the type descendants of a type.
     *
     * @param string $typeId the type ID or <code>null</code> to request the base types
     * @param integer $depth indicates whether the property definitions should be included or not
     * @param boolean $includePropertyDefinitions the tree depth, must be greater than 0 or -1 for infinite depth
     * @return TypeDefinitionContainerInterface[] A tree that contains ObjectTypeInterface objects
     * @see ObjectTypeInterface ObjectTypeInterface contained in returned Tree
     * @throws CmisObjectNotFoundException - if a type with the given type ID doesn't exist
     */
    public function getTypeDescendants($typeId, $depth, $includePropertyDefinitions)
    {
        return $this->getBinding()->getRepositoryService()->getTypeDescendants(
            $this->getRepositoryId(),
            $typeId,
            (integer) $depth,
            $includePropertyDefinitions
        );
    }

    /**
     * Sends a query to the repository using the given OperationContext. (See CMIS spec "2.1.10 Query".)
     *
     * @param string $statement the query statement (CMIS query language)
     * @param boolean $searchAllVersions specifies whether non-latest document versions should be included or not,
     *      <code>true</code> searches all document versions, <code>false</code> only searches latest document versions
     * @param OperationContextInterface|null $context the operation context to use
     * @return QueryResultInterface[]
     * @throws CmisInvalidArgumentException If statement is empty
     */
    public function query($statement, $searchAllVersions = false, OperationContextInterface $context = null)
    {
        if (empty($statement)) {
            throw new CmisInvalidArgumentException('Statement must not be empty.');
        }

        if ($context === null) {
            $context = $this->getDefaultContext();
        }

        $queryResults = [];
        $skipCount = 0;

        $objectList = $this->getBinding()->getDiscoveryService()->query(
            $this->getRepositoryInfo()->getId(),
            $statement,
            $searchAllVersions,
            $context->getIncludeRelationships(),
            $context->getRenditionFilterString(),
            $context->isIncludeAllowableActions(),
            $context->getMaxItemsPerPage(),
            $skipCount
        );

        foreach ($objectList->getObjects() as $objectData) {
            $queryResult = $this->getObjectFactory()->convertQueryResult($objectData);
            if ($queryResult instanceof QueryResultInterface) {
                $queryResults[] = $queryResult;
            }
        }

        // TODO: Implement pagination using skipCount

        return $queryResults;
    }

    /**
     * Builds a CMIS query and returns the query results as an iterator of CmisObject objects.
     *
     * @param string $typeId the ID of the object type
     * @param string|null $where the WHERE part of the query
     * @param boolean $searchAllVersions specifies whether non-latest document versions should be included or not,
     *      <code>true</code> searches all document versions, <code>false</code> only searches latest document versions
     * @param OperationContextInterface|null $context the operation context to use
     * @return CmisObjectInterface[]
     * @throws CmisInvalidArgumentException If type id is empty
     */
    public function queryObjects(
        $typeId,
        $where = null,
        $searchAllVersions = false,
        OperationContextInterface $context = null
    ) {
        if (empty($typeId)) {
            throw new CmisInvalidArgumentException('Type id must not be empty.');
        }

        if ($context === null) {
            $context = $this->getDefaultContext();
        }

        $queryFilterString = $context->getQueryFilterString();
        if (!empty($queryFilterString)) {
            $querySelect = $queryFilterString;
        } else {
            $querySelect = '*';
        }

        $whereClause = '';
        if (!empty($where)) {
            $whereClause = ' WHERE ' . $where;
        }

        $orderBy = $context->getOrderBy();
        if (!empty($orderBy)) {
            $orderBy = ' ORDER BY ' . $orderBy;
        }

        $typeDefinition = $this->getTypeDefinition($typeId);
        $statement = 'SELECT ' . $querySelect . ' FROM ' . $typeDefinition->getQueryName() . $whereClause . $orderBy;
        $queryStatement = new QueryStatement($this, $statement);

        $resultObjects = [];
        $skipCount = 0;

        $objectList = $this->getBinding()->getDiscoveryService()->query(
            $this->getRepositoryInfo()->getId(),
            $queryStatement->toQueryString(),
            $searchAllVersions,
            $context->getIncludeRelationships(),
            $context->getRenditionFilterString(),
            $context->isIncludeAllowableActions(),
            $context->getMaxItemsPerPage(),
            $skipCount
        );

        foreach ($objectList->getObjects() as $objectData) {
            $object = $this->getObjectFactory()->convertObject($objectData, $context);
            if ($object instanceof CmisObjectInterface) {
                $resultObjects[] = $object;
            }
        }

        // TODO: Implement pagination using skipCount

        return $resultObjects;
    }

    /**
     * Removes the given object from the cache.
     *
     * Note about specific implementation: in order to avoid an over-engineered
     * taggable cache mechanism and with it, extensive traversal of objects when
     * creating tags, objects are not tagged in the cache. In addition, objects
     * are added to the cache with a secondary identification; the context - and
     * the context is not available here when removing a single object from the
     * cache. Instead, we opt to simply flush the entire cache and let it refill.
     *
     * @param ObjectIdInterface $objectId
     */
    public function removeObjectFromCache(ObjectIdInterface $objectId)
    {
        $this->clear();
    }

    /**
     * Removes a set of policies from an object. This operation is not atomic.
     * If it fails some policies might already be removed.
     *
     * @param ObjectIdInterface $objectId the ID the object
     * @param ObjectIdInterface[] $policyIds the IDs of the policies to be removed
     */
    public function removePolicy(ObjectIdInterface $objectId, array $policyIds)
    {
        // TODO: Implement removePolicy() method.
    }

    /**
     * Removes the direct ACEs of an object and sets the provided ACEs.
     * The changes are local to the given object and are not propagated to dependent objects.
     *
     * @param ObjectIdInterface $objectId
     * @param AceInterface[] $aces
     * @return AclInterface the new ACL of the object
     */
    public function setAcl(ObjectIdInterface $objectId, array $aces)
    {
        // TODO: Implement setAcl() method.
    }

    /**
     * Sets the current session parameters for filtering, paging and caching.
     *
     * @param OperationContextInterface $context the OperationContext to be used for the session;
     *      if null, a default context is used
     */
    public function setDefaultContext(OperationContextInterface $context)
    {
        $this->defaultContext = $context;
    }

    /**
     * Updates an existing type.
     *
     * @param TypeDefinitionInterface $type the type definition updates
     * @return ObjectTypeInterface the updated type definition
     */
    public function updateType(TypeDefinitionInterface $type)
    {
        // TODO: Implement updateType() method.
    }

    /**
     * Converts a type definition into an object type. If the object type is
     * cached, it returns the cached object. Otherwise it creates an object type
     * object and puts it into the cache.
     *
     * @param TypeDefinitionInterface $typeDefinition
     * @return ObjectTypeInterface
     */
    private function convertTypeDefinition(TypeDefinitionInterface $typeDefinition)
    {
        // TODO: Implement cache. See Java Implementation for example
        return $this->getObjectFactory()->convertTypeDefinition($typeDefinition);
    }
}
