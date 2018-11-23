<?php
namespace Dkd\PhpCmis\DataObjects;

/*
 * This file is part of php-cmis-client.
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
use Dkd\PhpCmis\Data\AllowableActionsInterface;
use Dkd\PhpCmis\Data\CmisExtensionElementInterface;
use Dkd\PhpCmis\Data\ObjectDataInterface;
use Dkd\PhpCmis\Data\ObjectIdInterface;
use Dkd\PhpCmis\Data\ObjectTypeInterface;
use Dkd\PhpCmis\Data\PolicyIdListInterface;
use Dkd\PhpCmis\Data\PolicyInterface;
use Dkd\PhpCmis\Data\PropertiesInterface;
use Dkd\PhpCmis\Data\PropertyInterface;
use Dkd\PhpCmis\Data\RelationshipInterface;
use Dkd\PhpCmis\Data\RenditionInterface;
use Dkd\PhpCmis\Data\SecondaryTypeInterface;
use Dkd\PhpCmis\Definitions\PropertyDefinitionInterface;
use Dkd\PhpCmis\Enum\AclPropagation;
use Dkd\PhpCmis\Enum\Action;
use Dkd\PhpCmis\Enum\BaseTypeId;
use Dkd\PhpCmis\Enum\ExtensionLevel;
use Dkd\PhpCmis\Enum\Updatability;
use Dkd\PhpCmis\Exception\CmisInvalidArgumentException;
use Dkd\PhpCmis\Exception\CmisObjectNotFoundException;
use Dkd\PhpCmis\Exception\IllegalStateException;
use Dkd\PhpCmis\ObjectFactoryInterface;
use Dkd\PhpCmis\OperationContextInterface;
use Dkd\PhpCmis\PropertyIds;
use Dkd\PhpCmis\SessionInterface;

/**
 * Class AbstractCmisObject
 */
abstract class AbstractCmisObject implements CmisObjectInterface
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var ObjectTypeInterface
     */
    protected $objectType;

    /**
     * @var null|SecondaryTypeInterface[]
     */
    protected $secondaryTypes;

    /**
     * @var PropertyInterface[]
     */
    protected $properties = [];

    /**
     * @var AllowableActionsInterface|null
     */
    protected $allowableActions;

    /**
     * @var RenditionInterface[]
     */
    protected $renditions = [];

    /**
     * @var AclInterface|null
     */
    protected $acl;

    /**
     * @var PolicyInterface[]
     */
    protected $policies = [];

    /**
     * @var RelationshipInterface[]
     */
    protected $relationships = [];

    /**
     * A list that contains a list of <code>CmisExtensionElementInterface</code> identified by
     * <code>ExtensionLevel</code>. The key is the string representation of <code>ExtensionLevel</code> and the value
     * the array of <code>CmisExtensionElementInterface</code>
     *
     * @see ExtensionLevel
     * @see CmisExtensionElementInterface
     * @var array[]
     */
    protected $extensions = [];

    /**
     * @var OperationContextInterface
     */
    protected $creationContext;

    /**
     * @var integer
     */
    protected $refreshTimestamp = 0;

    /**
     * Initialize the CMIS Object
     *
     * @param SessionInterface $session
     * @param ObjectTypeInterface $objectType
     * @param OperationContextInterface $context
     * @param ObjectDataInterface|null $objectData
     */
    public function initialize(
        SessionInterface $session,
        ObjectTypeInterface $objectType,
        OperationContextInterface $context,
        ObjectDataInterface $objectData = null
    ) {
        if (count($this->getMissingBaseProperties($objectType->getPropertyDefinitions())) !== 0) {
            throw new CmisInvalidArgumentException(
                sprintf(
                    'Object type must have at least the base property definitions! '
                    . 'These property definitions are missing: %s',
                    implode(', ', PropertyIds::getBasePropertyKeys())
                )
            );
        };

        $this->session = $session;
        $this->objectType = $objectType;
        $this->secondaryTypes = null;
        $this->creationContext = clone $context;
        $this->refreshTimestamp = (integer) round(microtime(true) * 1000);

        if ($objectData !== null) {
            $this->initializeObjectData($objectData);
        }
    }

    /**
     * Handle initialization for objectData
     *
     * @param ObjectDataInterface $objectData
     */
    private function initializeObjectData(ObjectDataInterface $objectData)
    {
        // handle properties
        if ($objectData->getProperties() !== null) {
            $this->initializeObjectDataProperties($objectData->getProperties());
        }

        // handle allowable actions
        if ($objectData->getAllowableActions() !== null) {
            $this->allowableActions = $objectData->getAllowableActions();
            $this->extensions[(string) ExtensionLevel::cast(
                ExtensionLevel::ALLOWABLE_ACTIONS
            )] = $objectData->getAllowableActions()->getExtensions();
        }

        // handle renditions
        foreach ($objectData->getRenditions() as $rendition) {
            $this->renditions[] = $this->getObjectFactory()->convertRendition($this->getId(), $rendition);
        }

        // handle ACL
        if ($objectData->getAcl() !== null) {
            $this->acl = $objectData->getAcl();
            $this->extensions[(string) ExtensionLevel::cast(ExtensionLevel::ACL)] = $objectData->getAcl(
            )->getExtensions();
        }

        // handle policies
        if ($objectData->getPolicyIds() !== null) {
            $this->initializeObjectDataPolicies($objectData->getPolicyIds());
        }

        // handle relationships
        foreach ($objectData->getRelationships() as $relationshipData) {
            $relationship = $this->getObjectFactory()->convertObject(
                $relationshipData,
                $this->getCreationContext()
            );
            if ($relationship instanceof RelationshipInterface) {
                $this->relationships[] = $relationship;
            }
        }

        $this->extensions[(string) ExtensionLevel::OBJECT] = $objectData->getExtensions();
    }

    /**
     * Handle initialization of properties from the object data
     *
     * @param PropertiesInterface $properties
     */
    private function initializeObjectDataProperties(PropertiesInterface $properties)
    {
        // get secondary types
        $propertyList = $properties->getProperties();
        if (isset($propertyList[PropertyIds::SECONDARY_OBJECT_TYPE_IDS])) {
            $this->secondaryTypes = [];
            foreach ($propertyList[PropertyIds::SECONDARY_OBJECT_TYPE_IDS]->getValues() as $secondaryTypeId) {
                $type = $this->getSession()->getTypeDefinition($secondaryTypeId);
                if ($type instanceof SecondaryTypeInterface) {
                    $this->secondaryTypes[] = $type;
                }
            }
        }

        $this->properties = $this->getObjectFactory()->convertPropertiesDataToPropertyList(
            $this->getObjectType(),
            (array) $this->getSecondaryTypes(),
            $properties
        );

        $this->extensions[(string) ExtensionLevel::cast(
            ExtensionLevel::PROPERTIES
        )] = $properties->getExtensions();
    }

    /**
     * Handle initialization of policies from the object data
     *
     * @param PolicyIdListInterface $policies
     */
    private function initializeObjectDataPolicies(PolicyIdListInterface $policies)
    {
        foreach ($policies->getPolicyIds() as $policyId) {
            $policy = $this->getSession()->getObject($this->getSession()->createObjectId($policyId));
            if ($policy instanceof PolicyInterface) {
                $this->policies[] = $policy;
            }
        }

        $this->extensions[(string) ExtensionLevel::POLICIES] = $policies->getExtensions();
    }


    /**
     * Returns a list of missing property keys
     *
     * @param PropertyDefinitionInterface[]|null $properties
     * @return array
     */
    protected function getMissingBaseProperties(array $properties = null)
    {
        $basePropertyKeys = PropertyIds::getBasePropertyKeys();

        if ($properties === null) {
            return $basePropertyKeys;
        }

        foreach ($properties as $property) {
            $propertyId = $property->getId();
            $basePropertyKey = array_search($propertyId, $basePropertyKeys);
            if ($basePropertyKey !== false) {
                unset($basePropertyKeys[$basePropertyKey]);
            }
        }

        return $basePropertyKeys;
    }

    /**
     * Returns the session object
     *
     * @return SessionInterface
     */
    protected function getSession()
    {
        return $this->session;
    }

    /**
     * Returns the repository id
     *
     * @return string
     */
    protected function getRepositoryId()
    {
        return $this->getSession()->getRepositoryInfo()->getId();
    }

    /**
     * Returns the object type
     *
     * @return ObjectTypeInterface
     */
    protected function getObjectType()
    {
        return $this->objectType;
    }

    /**
     * Returns the object factory.
     *
     * @return ObjectFactoryInterface
     */
    protected function getObjectFactory()
    {
        return $this->getSession()->getObjectFactory();
    }

    /**
     * Get the binding object
     *
     * @return CmisBindingInterface
     */
    protected function getBinding()
    {
        return $this->getSession()->getBinding();
    }

    /**
     * Returns the OperationContext that was used to create this object.
     *
     * @return OperationContextInterface
     */
    protected function getCreationContext()
    {
        return $this->creationContext;
    }

    /**
     * Returns the query name of a property.
     *
     * @param string $propertyId
     * @return null|string
     */
    protected function getPropertyQueryName($propertyId)
    {
        $propertyDefinition = $this->getObjectType()->getPropertyDefinition($propertyId);
        if ($propertyDefinition === null) {
            return null;
        }

        return $propertyDefinition->getQueryName();
    }

    /**
     * Delete this object
     *
     * @param boolean $allVersions indicates if all versions of the object should be deleted
     */
    public function delete($allVersions = true)
    {
        $this->getSession()->delete($this, $allVersions);
    }

    /**
     * Updates the provided properties. If the repository created a new object, for example a new version,
     * the object ID of the new object is returned. Otherwise the object ID of the current object is returned.
     *
     * @param mixed[] $properties the properties to update
     * @param boolean $refresh <code>true</code> if this object should be refresh after the update,
     *      <code>false</code> if not
     * @return CmisObjectInterface|null the object ID of the updated object - can return <code>null</code> in case
     *      of a repository failure
     */
    public function updateProperties(array $properties, $refresh = true)
    {
        if (empty($properties)) {
            throw new CmisInvalidArgumentException('Properties must not be empty!');
        }

        $objectId = $this->getId();
        $changeToken = $this->getChangeToken();

        $updatability = [];
        $updatability[] = Updatability::cast(Updatability::READWRITE);
        if ((boolean) $this->getPropertyValue(PropertyIds::IS_VERSION_SERIES_CHECKED_OUT) === true) {
            $updatability[] = Updatability::cast(Updatability::WHENCHECKEDOUT);
        }

        $newObjectId = $objectId;
        $this->getBinding()->getObjectService()->updateProperties(
            $this->getRepositoryId(),
            $newObjectId,
            $this->getObjectFactory()->convertProperties(
                $properties,
                $this->getObjectType(),
                (array) $this->getSecondaryTypes(),
                $updatability
            ),
            $changeToken
        );

        // remove the object from the cache, it has been changed
        $this->getSession()->removeObjectFromCache($this->getSession()->createObjectId($objectId));

        if ($refresh === true) {
            $this->refresh();
        }

        if ($newObjectId === null) {
            return null;
        }

        return $this->getSession()->getObject(
            $this->getSession()->createObjectId($newObjectId),
            $this->getCreationContext()
        );
    }

    /**
     * Renames this object (changes the value of cmis:name).
     * If the repository created a new object, for example a new version, the object id of the
     * new object is returned. Otherwise the object id of the current object is returned.
     *
     * @param string $newName the new name, not <code>null</code> or empty
     * @param boolean $refresh <code>true</code> if this object should be refresh after the update,
     *     <code>false</code> if not
     * @return CmisObjectInterface|null the object ID of the updated object - can return <code>null</code> in case of
     *     a repository failure
     */
    public function rename($newName, $refresh = true)
    {
        if (empty($newName)) {
            throw new CmisInvalidArgumentException('New name must not be empty!');
        }

        $properties = [
            PropertyIds::NAME => $newName,
        ];

        $object = $this->updateProperties($properties, $refresh);

        return $object;
    }

    /**
     * Returns the type of this CMIS object (object type identified by <code>cmis:objectTypeId</code>).
     *
     * @return ObjectTypeInterface the type of the object or <code>null</code> if the property
     *         <code>cmis:objectTypeId</code> hasn't been requested or hasn't been provided by the repository
     */
    public function getType()
    {
        return $this->getObjectType();
    }

    /**
     * Returns the base type of this CMIS object (object type identified by cmis:baseTypeId).
     *
     * @return ObjectTypeInterface the base type of the object or <code>null</code> if the property cmis:baseTypeId
     *         hasn't been requested or hasn't been provided by the repository
     */
    public function getBaseType()
    {
        $baseType = $this->getBaseTypeId();
        if ($baseType === null) {
            return null;
        }

        return $this->getSession()->getTypeDefinition((string) $baseType);
    }

    /**
     * Returns the base type of this CMIS object (object type identified by cmis:baseTypeId).
     *
     * @return BaseTypeId|null the base type of the object or <code>null</code> if the property
     *         cmis:baseTypeId hasn't been requested or hasn't been provided by the repository
     */
    public function getBaseTypeId()
    {
        $baseTypeProperty = $this->getProperty(PropertyIds::BASE_TYPE_ID);
        if ($baseTypeProperty === null) {
            return null;
        }

        return BaseTypeId::cast($baseTypeProperty->getFirstValue());
    }

    /**
     * Returns the change token (CMIS property cmis:changeToken).
     *
     * @return string the change token of the object or <code>null</code> if the property hasn't been requested or
     *         hasn't been provided or isn't supported by the repository
     */
    public function getChangeToken()
    {
        return $this->getPropertyValue(PropertyIds::CHANGE_TOKEN);
    }

    /**
     * Returns the user who created this CMIS object (CMIS property cmis:createdBy).
     *
     * @return string the creator of the object or <code>null</code> if the property hasn't been requested or hasn't
     *         been provided by the repository
     */
    public function getCreatedBy()
    {
        return $this->getPropertyValue(PropertyIds::CREATED_BY);
    }

    /**
     * Returns the timestamp when this CMIS object has been created (CMIS property cmis:creationDate).
     *
     * @return \DateTime|null the creation time of the object or <code>null</code> if the property hasn't been
     *         requested or hasn't been provided by the repository
     */
    public function getCreationDate()
    {
        return $this->getPropertyValue(PropertyIds::CREATION_DATE);
    }

    /**
     * Returns the object ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->getPropertyValue(PropertyIds::OBJECT_ID);
    }

    /**
     * Returns the timestamp when this CMIS object has been modified (CMIS property cmis:lastModificationDate).
     *
     * @return \DateTime|null the last modification date of the object or <code>null</code> if the property hasn't been
     *         requested or hasn't been provided by the repository
     */
    public function getLastModificationDate()
    {
        return $this->getPropertyValue(PropertyIds::LAST_MODIFICATION_DATE);
    }

    /**
     * Returns the user who modified this CMIS object (CMIS property cmis:lastModifiedBy).
     *
     * @return string|null the last modifier of the object or <code>null</code> if the property hasn't
     *         been requested or hasn't been provided by the repository
     */
    public function getLastModifiedBy()
    {
        return $this->getPropertyValue(PropertyIds::LAST_MODIFIED_BY);
    }

    /**
     * Returns the name of this CMIS object (CMIS property cmis:name).
     *
     * @return string|null the name of the object or <code>null</code> if the property hasn't been requested
     *         or hasn't been provided by the repository
     */
    public function getName()
    {
        return $this->getPropertyValue(PropertyIds::NAME);
    }

    /**
     * Returns the description of this CMIS object (CMIS property cmis:description).
     *
     * @return string|null the description of the object or <code>null</code> if the property hasn't been requested,
     *         hasn't been provided by the repository, or the property value isn't set
     */
    public function getDescription()
    {
        return $this->getPropertyValue(PropertyIds::DESCRIPTION);
    }

    /**
     * Returns a list of all available CMIS properties.
     *
     * @return PropertyInterface[] all available CMIS properties
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Returns a property.
     *
     * @param string $id the ID of the property
     * @return PropertyInterface|null the property or <code>null</code> if the property hasn't been requested or
     *         hasn't been provided by the repository
     */
    public function getProperty($id)
    {
        if (!isset($this->properties[$id])) {
            return null;
        }

        return $this->properties[$id];
    }

    /**
     * Returns the value of a property.
     *
     * @param string $id the ID of the property
     * @return mixed the property value or <code>null</code> if the property hasn't been requested,
     *         hasn't been provided by the repository, or the property value isn't set
     */
    public function getPropertyValue($id)
    {
        $property = $this->getProperty($id);
        if ($property === null) {
            return null;
        }

        return $property->isMultiValued() ? $property->getValues() : $property->getFirstValue();
    }

    /**
     * Returns the secondary types of this CMIS object (object types identified by cmis:secondaryObjectTypeIds).
     *
     * @return SecondaryTypeInterface[]|null the secondary types of the object or <code>null</code> if the property
     *         cmis:secondaryObjectTypeIds hasn't been requested or hasn't been provided by the repository
     */
    public function getSecondaryTypes()
    {
        return $this->secondaryTypes;
    }

    /**
     * Returns a list of primary and secondary object types that define the given property.
     *
     * @param string $id the ID of the property
     * @return ObjectTypeInterface[]|null a list of object types that define the given property or <code>null</code>
     *         if the property could not be found in the object types that are attached to this object
     */
    public function findObjectType($id)
    {
        $result = [];

        if ($this->getObjectType()->getPropertyDefinition($id) !== null) {
            $result[] = $this->getObjectType();
        }

        $secondaryTypes = $this->getSecondaryTypes();
        if ($secondaryTypes !== null) {
            foreach ($secondaryTypes as $secondaryType) {
                if ($secondaryType->getPropertyDefinition($id) !== null) {
                    $result[] = $secondaryType;
                }
            }
        }

        return empty($result) ? null : $result;
    }

    /**
     * Returns the allowable actions if they have been fetched for this object.
     *
     * @return AllowableActionsInterface|null
     */
    public function getAllowableActions()
    {
        return $this->allowableActions;
    }

    /**
     * Checks if the given action is an allowed action for the object
     *
     * @param Action $action
     * @return boolean
     */
    public function hasAllowableAction(Action $action)
    {
        $currentAllowableActions = $this->getAllowableActions();
        if ($currentAllowableActions === null || count($currentAllowableActions->getAllowableActions()) === 0) {
            throw new IllegalStateException('Allowable Actions are not available!');
        }

        return in_array($action, $currentAllowableActions->getAllowableActions(), false);
    }

    /**
     * Returns the renditions if they have been fetched for this object.
     *
     * @return RenditionInterface[]
     */
    public function getRenditions()
    {
        return $this->renditions;
    }

    /**
     * Adds and removes ACEs to the object and refreshes this object afterwards.
     *
     * @param AceInterface[] $addAces
     * @param AceInterface[] $removeAces
     * @param AclPropagation $aclPropagation
     * @return AclInterface the new ACL of this object
     */
    public function applyAcl(array $addAces, array $removeAces, AclPropagation $aclPropagation)
    {
        $result = $this->getSession()->applyAcl($this, $addAces, $removeAces, $aclPropagation);

        $this->refresh();

        return $result;
    }

    /**
     * Adds ACEs to the object and refreshes this object afterwards.
     *
     * @param AceInterface[] $addAces
     * @param AclPropagation $aclPropagation
     * @return AclInterface the new ACL of this object
     */
    public function addAcl(array $addAces, AclPropagation $aclPropagation)
    {
        return $this->applyAcl($addAces, [], $aclPropagation);
    }

    /**
     * Removes ACEs to the object and refreshes this object afterwards.
     *
     * @param array $removeAces
     * @param AclPropagation $aclPropagation
     * @return AclInterface the new ACL of this object
     */
    public function removeAcl(array $removeAces, AclPropagation $aclPropagation)
    {
        return $this->applyAcl([], $removeAces, $aclPropagation);
    }

    /**
     * Removes the direct ACE of this object, sets the provided ACEs to the object and refreshes this object afterwards.
     *
     * @param AceInterface[] $aces
     * @return AclInterface
     */
    public function setAcl(array $aces)
    {
        $result = $this->getSession()->setAcl($this, $aces);

        $this->refresh();

        return $result;
    }

    /**
     * Returns the ACL if it has been fetched for this object.
     *
     * @return AclInterface|null
     */
    public function getAcl()
    {
        $result = $this->getSession()->getAcl($this, false);

        $this->refresh();

        return $result;
    }

    /**
     * Returns all permissions for the given principal from the ACL.
     *
     * @param string $principalId the principal ID
     * @return string[] the set of permissions for this user, or an empty set if principal is not in the ACL
     * @throws IllegalStateException if the ACL hasn't been fetched or provided by the repository
     */
    public function getPermissionsForPrincipal($principalId)
    {
        if (empty($principalId)) {
            throw new IllegalStateException('Principal ID must be set!');
        }

        $currentAcl = $this->getAcl();

        if ($currentAcl === null) {
            throw new IllegalStateException('ACLs are not available');
        }

        $result = [];

        foreach ($currentAcl->getAces() as $ace) {
            if ($principalId === $ace->getPrincipalId() && count($ace->getPermissions()) > 0) {
                $result = array_merge($result, $ace->getPermissions());
            }
        }

        return $result;
    }

    /**
     * Applies the provided policies and refreshes this object afterwards.
     *
     * @param ObjectIdInterface[] $policyIds
     */
    public function applyPolicies(array $policyIds)
    {
        $this->getSession()->applyPolicies($this, $policyIds);
    }

    /**
     * Returns the applied policies if they have been fetched for this object.
     *
     * @return PolicyInterface[]
     */
    public function getPolicies()
    {
        $this->policies;
    }

    /**
     * Removes the provided policies and refreshes this object afterwards.
     *
     * @param ObjectIdInterface[] $policyIds
     */
    public function removePolicy(array $policyIds)
    {
        $this->getSession()->removePolicy($this, $policyIds);
    }

    /**
     * Returns the relationships if they have been fetched for this object.
     *
     * @return RelationshipInterface[]
     */
    public function getRelationships()
    {
        return $this->relationships;
    }

    /**
     * Returns the extensions for the given level.
     *
     * @param ExtensionLevel $level the level
     * @return array[]|null A list of <code>CmisExtensionElementInterface</code> at the requested level or
     *      <code>null</code> if there are no extensions for the requested level
     * @see CmisExtensionElementInterface
     */
    public function getExtensions(ExtensionLevel $level)
    {
        if (!isset($this->extensions[(string) $level])) {
            return null;
        }

        return $this->extensions[(string) $level];
    }

    /**
     * Returns the timestamp of the last refresh.
     *
     * @return integer returns the Java-style milliseconds UNIX timestamp of last refresh
     */
    public function getRefreshTimestamp()
    {
        return $this->refreshTimestamp;
    }

    /**
     * Reloads this object from the repository.
     *
     * @throws CmisObjectNotFoundException - if the object doesn't exist anymore in the repository
     */
    public function refresh()
    {
        $operationContext = $this->getCreationContext();

        $objectData = $this->getSession()->getBinding()->getObjectService()->getObject(
            $this->getRepositoryId(),
            $this->getId(),
            $operationContext->getQueryFilterString(),
            $operationContext->isIncludeAllowableActions(),
            $operationContext->getIncludeRelationships(),
            $operationContext->getRenditionFilterString(),
            $operationContext->isIncludePolicies(),
            $operationContext->isIncludeAcls(),
            null
        );

        $this->initialize(
            $this->getSession(),
            $this->getSession()->getTypeDefinition($this->getObjectType()->getId()),
            $this->creationContext,
            $objectData
        );
    }

    /**
     * Reloads the data from the repository if the last refresh did not occur within durationInMillis.
     *
     * @param integer $durationInMillis
     * @throws CmisObjectNotFoundException - if the object doesn't exist anymore in the repository
     */
    public function refreshIfOld($durationInMillis = 0)
    {
        if ($this->getRefreshTimestamp() < ((round(microtime(true) * 1000)) - (integer) $durationInMillis)) {
            $this->refresh();
        }
    }
}
