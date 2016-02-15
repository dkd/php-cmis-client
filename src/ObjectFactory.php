<?php
namespace Dkd\PhpCmis;

/**
 * This file is part of php-cmis-client
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\CmisObject\CmisObjectInterface;
use Dkd\PhpCmis\Data\AceInterface;
use Dkd\PhpCmis\Data\AclInterface;
use Dkd\PhpCmis\Data\BindingsObjectFactoryInterface;
use Dkd\PhpCmis\Data\ChangeEventInfoInterface;
use Dkd\PhpCmis\Data\ObjectDataInterface;
use Dkd\PhpCmis\Data\ObjectListInterface;
use Dkd\PhpCmis\Data\ObjectTypeInterface;
use Dkd\PhpCmis\Data\PolicyInterface;
use Dkd\PhpCmis\Data\PropertiesInterface;
use Dkd\PhpCmis\Data\PropertyDataInterface;
use Dkd\PhpCmis\Data\PropertyInterface;
use Dkd\PhpCmis\Data\RenditionDataInterface;
use Dkd\PhpCmis\Data\RenditionInterface;
use Dkd\PhpCmis\Data\RepositoryInfoInterface;
use Dkd\PhpCmis\Data\SecondaryTypeInterface;
use Dkd\PhpCmis\DataObjects\Document;
use Dkd\PhpCmis\DataObjects\DocumentType;
use Dkd\PhpCmis\DataObjects\DocumentTypeDefinition;
use Dkd\PhpCmis\DataObjects\Folder;
use Dkd\PhpCmis\DataObjects\FolderType;
use Dkd\PhpCmis\DataObjects\FolderTypeDefinition;
use Dkd\PhpCmis\DataObjects\Item;
use Dkd\PhpCmis\DataObjects\ItemType;
use Dkd\PhpCmis\DataObjects\ItemTypeDefinition;
use Dkd\PhpCmis\DataObjects\Policy;
use Dkd\PhpCmis\DataObjects\PolicyType;
use Dkd\PhpCmis\DataObjects\PolicyTypeDefinition;
use Dkd\PhpCmis\DataObjects\Property;
use Dkd\PhpCmis\DataObjects\PropertyId;
use Dkd\PhpCmis\DataObjects\Relationship;
use Dkd\PhpCmis\DataObjects\RelationshipType;
use Dkd\PhpCmis\DataObjects\RelationshipTypeDefinition;
use Dkd\PhpCmis\DataObjects\Rendition;
use Dkd\PhpCmis\DataObjects\SecondaryType;
use Dkd\PhpCmis\DataObjects\SecondaryTypeDefinition;
use Dkd\PhpCmis\Definitions\DocumentTypeDefinitionInterface;
use Dkd\PhpCmis\Definitions\FolderTypeDefinitionInterface;
use Dkd\PhpCmis\Definitions\ItemTypeDefinitionInterface;
use Dkd\PhpCmis\Definitions\PolicyTypeDefinitionInterface;
use Dkd\PhpCmis\Definitions\PropertyDefinitionInterface;
use Dkd\PhpCmis\Definitions\RelationshipTypeDefinitionInterface;
use Dkd\PhpCmis\Definitions\SecondaryTypeDefinitionInterface;
use Dkd\PhpCmis\Definitions\TypeDefinitionInterface;
use Dkd\PhpCmis\Definitions\TypeMutabilityInterface;
use Dkd\PhpCmis\Enum\BaseTypeId;
use Dkd\PhpCmis\Enum\Cardinality;
use Dkd\PhpCmis\Enum\Updatability;
use Dkd\PhpCmis\Exception\CmisInvalidArgumentException;
use Dkd\PhpCmis\Exception\CmisRuntimeException;
use GuzzleHttp\Stream\StreamInterface;

/**
 * Object Factory implementation
 *
 * @author Sascha Egerer <sascha.egerer@dkd.de>
 */
class ObjectFactory implements ObjectFactoryInterface
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * Initialize the object factory with a session.
     *
     * @param SessionInterface $session
     * @param string[] $parameters
     */
    public function initialize(SessionInterface $session, $parameters = array())
    {
        $this->session = $session;
    }

    /**
     * Convert ACEs to an ACL.
     *
     * This method does not create a copy of the ACE as the Java OpenCMIS implementation does. #
     * For details see the discussion in the mailing list
     *
     * @see http://mail-archives.apache.org/mod_mbox/chemistry-dev/201501.mbox/<843F5117-E79B-47AF-B4E3-32760263CF26@dkd.de>
     *
     * @param AceInterface[] $aces
     * @return AclInterface
     */
    public function convertAces(array $aces)
    {
        return $this->getBindingsObjectFactory()->createAccessControlList($aces);
    }

    /**
     * @param ObjectDataInterface $objectData
     * @return ChangeEventInfoInterface
     */
    public function convertChangeEvent(ObjectDataInterface $objectData)
    {
        // TODO: Implement convertChangeEvent() method.
    }

    /**
     * @param string $changeLogToken
     * @param ObjectListInterface $objectList
     * @return ChangeEventsInterface
     */
    public function convertChangeEvents($changeLogToken, ObjectListInterface $objectList)
    {
        // TODO: Implement convertChangeEvents() method.
    }

    /**
     * Converts a high level ContentStream object into a low level ContentStream object.
     *
     * @param StreamInterface $contentStream the original ContentStream object
     * @return StreamInterface the ContentStream object
     */
    public function convertContentStream(StreamInterface $contentStream)
    {
        // TODO: Implement convertContentStream() method.
        return $contentStream;
    }

    /**
     * Convert given ObjectData to a high level API object
     *
     * @param ObjectDataInterface $objectData
     * @param OperationContextInterface $context
     * @return CmisObjectInterface
     * @throws CmisRuntimeException
     */
    public function convertObject(ObjectDataInterface $objectData, OperationContextInterface $context)
    {
        $type = $this->getTypeFromObjectData($objectData);
        if ($type === null) {
            throw new CmisRuntimeException('Could not get type from object data.');
        }
        $baseTypeId = $objectData->getBaseTypeId();

        if ($baseTypeId->equals(BaseTypeId::CMIS_DOCUMENT)) {
            return new Document($this->session, $type, $context, $objectData);
        } elseif ($baseTypeId->equals(BaseTypeId::CMIS_FOLDER)) {
            return new Folder($this->session, $type, $context, $objectData);
        } elseif ($baseTypeId->equals(BaseTypeId::CMIS_POLICY)) {
            return new Policy($this->session, $type, $context, $objectData);
        } elseif ($baseTypeId->equals(BaseTypeId::CMIS_RELATIONSHIP)) {
            return new Relationship($this->session, $type, $context, $objectData);
        } elseif ($baseTypeId->equals(BaseTypeId::CMIS_ITEM)) {
            return new Item($this->session, $type, $context, $objectData);
        } elseif ($baseTypeId->equals(BaseTypeId::CMIS_SECONDARY)) {
            throw new CmisRuntimeException('Secondary type is used as object type: ' . $baseTypeId);
        } else {
            throw new CmisRuntimeException('Unsupported base type: ' . $baseTypeId);
        }
    }

    /**
     * Converts a list of Policy objects into a list of there string representations
     *
     * @param PolicyInterface[] $policies
     * @return string[]
     */
    public function convertPolicies(array $policies)
    {
        $result = array();

        foreach ($policies as $policy) {
            if ($policy->getId() !== null) {
                $result[] = $policy->getId();
            }
        }

        return $result;
    }

    /**
     * Convert Properties in Properties instance to a list of PropertyInterface objects
     *
     * @param ObjectTypeInterface $objectType
     * @param SecondaryTypeInterface[] $secondaryTypes
     * @param PropertiesInterface $properties
     * @return PropertyInterface[]
     * @throws CmisInvalidArgumentException
     */
    public function convertPropertiesDataToPropertyList(
        ObjectTypeInterface $objectType,
        array $secondaryTypes,
        PropertiesInterface $properties
    ) {
        if (count($objectType->getPropertyDefinitions()) === 0) {
            throw new CmisInvalidArgumentException('Object type has no property definitions!');
        }
        if (count($properties->getProperties()) === 0) {
            throw new CmisInvalidArgumentException('Properties must be set');
        }

        // Iterate trough properties and convert them to Property objects
        $result = array();
        foreach ($properties->getProperties() as $propertyKey => $propertyData) {
            // find property definition
            $apiProperty = $this->convertProperty($objectType, $secondaryTypes, $propertyData);
            $result[$propertyKey] = $apiProperty;
        }

        return $result;
    }

    /**
     * Convert PropertyData into a property API object
     *
     * @param ObjectTypeInterface $objectType
     * @param SecondaryTypeInterface[] $secondaryTypes
     * @param PropertyDataInterface $propertyData
     * @return PropertyInterface
     * @throws CmisRuntimeException
     */
    protected function convertProperty(
        ObjectTypeInterface $objectType,
        array $secondaryTypes,
        PropertyDataInterface $propertyData
    ) {
        $definition = $objectType->getPropertyDefinition($propertyData->getId());

        // search secondary types
        if ($definition === null && !empty($secondaryTypes)) {
            foreach ($secondaryTypes as $secondaryType) {
                $propertyDefinitions = $secondaryType->getPropertyDefinitions();
                if (!empty($propertyDefinitions)) {
                    $definition = $secondaryType->getPropertyDefinition($propertyData->getId());
                    if ($definition !== null) {
                        break;
                    }
                }
            }
        }

        // the type might have changed -> reload type definitions
        if ($definition === null) {
            $reloadedObjectType = $this->session->getTypeDefinition($objectType->getId(), false);
            $definition = $reloadedObjectType->getPropertyDefinition($propertyData->getId());

            if ($definition === null && !empty($secondaryTypes)) {
                foreach ($secondaryTypes as $secondaryType) {
                    $reloadedSecondaryType = $this->session->getTypeDefinition($secondaryType->getId(), false);
                    $propertyDefinitions = $reloadedSecondaryType->getPropertyDefinitions();
                    if (!empty($propertyDefinitions)) {
                        $definition = $reloadedSecondaryType->getPropertyDefinition($propertyData->getId());
                        if ($definition !== null) {
                            break;
                        }
                    }
                }
            }
        }

        if ($definition === null) {
            // property without definition
            throw new CmisRuntimeException(sprintf('Property "%s" doesn\'t exist!', $propertyData->getId()));
        }

        return $this->createProperty($definition, $propertyData->getValues());
    }

    /**
     * Convert properties to their property data objects and put them into a Properties object
     *
     * @param mixed[] $properties
     * @param ObjectTypeInterface|null $type
     * @param SecondaryTypeInterface[] $secondaryTypes
     * @param Updatability[] $updatabilityFilter
     * @return PropertiesInterface
     * @throws CmisInvalidArgumentException
     */
    public function convertProperties(
        array $properties,
        ObjectTypeInterface $type = null,
        array $secondaryTypes = array(),
        array $updatabilityFilter = array()
    ) {
        if (empty($properties)) {
            return null;
        }

        if ($type === null) {
            $type = $this->getTypeDefinition(
                isset($properties[PropertyIds::OBJECT_TYPE_ID]) ? $properties[PropertyIds::OBJECT_TYPE_ID] : null
            );
        }

        // get secondary types
        $allSecondaryTypes = array();
        $secondaryTypeIds = $this->getValueFromArray(PropertyIds::SECONDARY_OBJECT_TYPE_IDS, $properties);

        if (is_array($secondaryTypeIds)) {
            foreach ($secondaryTypeIds as $secondaryTypeId) {
                $secondaryType = $this->getTypeDefinition((string) $secondaryTypeId);

                if (!$secondaryType instanceof SecondaryType) {
                    throw new CmisInvalidArgumentException(
                        'Secondary types property contains a type that is not a secondary type: ' . $secondaryTypeId,
                        1425479398
                    );
                }
                $allSecondaryTypes[] = $secondaryType;
            }
        } elseif ($secondaryTypeIds !== null) {
            throw new CmisInvalidArgumentException(
                sprintf(
                    'The property "%s" must be of type array or undefined but is of type "%s"',
                    PropertyIds::SECONDARY_OBJECT_TYPE_IDS,
                    gettype($secondaryTypeIds)
                ),
                1425473414
            );
        }

        if (!empty($secondaryTypes) && empty($allSecondaryTypes)) {
            $allSecondaryTypes = $secondaryTypes;
        }

        $propertyList = array();

        foreach ($properties as $propertyId => $propertyValue) {
            $value = $propertyValue;

            if ($value instanceof PropertyInterface) {
                if ($value->getId() !== $propertyId) {
                    throw new CmisInvalidArgumentException(
                        sprintf('Property id mismatch: "%s" != "%s"', $propertyId, $value->getId())
                    );
                }
                $value = ($value->isMultiValued()) ? $value->getValues() : $value->getFirstValue();
            }

            $definition = $type->getPropertyDefinition($propertyId);

            if ($definition === null && !empty($allSecondaryTypes)) {
                foreach ($allSecondaryTypes as $secondaryType) {
                    $definition = $secondaryType->getPropertyDefinition($propertyId);

                    if ($definition !== null) {
                        break;
                    }
                }
            }

            if ($definition === null) {
                throw new CmisInvalidArgumentException(
                    sprintf('Property "%s" is not valid for this type or one of the secondary types!', $propertyId)
                );
            }

            // check updatability
            if (!empty($updatabilityFilter) && !in_array($definition->getUpdatability(), $updatabilityFilter)) {
                continue;
            }

            if (is_array($value)) {
                if (!$definition->getCardinality()->equals(Cardinality::MULTI)) {
                    throw new CmisInvalidArgumentException(
                        sprintf('Property "%s" is not a multi value property but multiple values given!', $propertyId)
                    );
                }
                $values = $value;
            } else {
                if (!$definition->getCardinality()->equals(Cardinality::SINGLE)) {
                    throw new CmisInvalidArgumentException(
                        sprintf('Property "%s" is not a single value property but single value given!', $propertyId)
                    );
                }
                $values = array();
                $values[] = $value;
            }

            $propertyList[] = $this->getBindingsObjectFactory()->createPropertyData($definition, $values);
        }

        return $this->getBindingsObjectFactory()->createPropertiesData($propertyList);
    }

    /**
     * Get a type definition for the given object type id. If an empty id is given throw an exception.
     *
     * @param string $objectTypeId
     * @return ObjectTypeInterface
     * @throws CmisInvalidArgumentException
     */
    private function getTypeDefinition($objectTypeId)
    {
        if (empty($objectTypeId) || !is_string($objectTypeId)) {
            throw new CmisInvalidArgumentException(
                'Type property must be set and must be of type string but is empty or not a string.'
            );
        }

        return $this->session->getTypeDefinition($objectTypeId);
    }

    /**
     * Get a value from an array. Return <code>null</code> if the key does not exist in the array.
     *
     * @param integer|string $needle
     * @param mixed $haystack
     * @return mixed
     */
    private function getValueFromArray($needle, $haystack)
    {
        if (!is_array($haystack) || !isset($haystack[$needle])) {
            return null;
        }

        return $haystack[$needle];
    }

    /**
     * @param PropertiesInterface $properties
     * @return PropertyDataInterface[]
     */
    public function convertQueryProperties(PropertiesInterface $properties)
    {
        return $properties->getProperties();
    }

    /**
     * Converts ObjectData to QueryResult
     *
     * @param ObjectDataInterface $objectData
     * @return QueryResult
     */
    public function convertQueryResult(ObjectDataInterface $objectData)
    {
        return new QueryResult($this->session, $objectData);
    }

    /**
     * Converts RenditionData to Rendition
     *
     * @param string $objectId
     * @param RenditionDataInterface $renditionData
     * @return RenditionInterface
     */
    public function convertRendition($objectId, RenditionDataInterface $renditionData)
    {
        $rendition = new Rendition($this->session, $objectId);
        $rendition->populate($renditionData);

        return $rendition;
    }

    /**
     * @param RepositoryInfoInterface $repositoryInfo
     * @return RepositoryInfoInterface
     */
    public function convertRepositoryInfo(RepositoryInfoInterface $repositoryInfo)
    {
        // TODO: Implement convertRepositoryInfo() method.
    }

    /**
     * Convert a type definition to a type
     *
     * @param TypeDefinitionInterface $typeDefinition
     * @return ObjectTypeInterface
     * @throws CmisRuntimeException
     */
    public function convertTypeDefinition(TypeDefinitionInterface $typeDefinition)
    {
        if ($typeDefinition instanceof DocumentTypeDefinitionInterface) {
            return new DocumentType($this->session, $typeDefinition);
        } elseif ($typeDefinition instanceof FolderTypeDefinitionInterface) {
            return new FolderType($this->session, $typeDefinition);
        } elseif ($typeDefinition instanceof RelationshipTypeDefinitionInterface) {
            return new RelationshipType($this->session, $typeDefinition);
        } elseif ($typeDefinition instanceof PolicyTypeDefinitionInterface) {
            return new PolicyType($this->session, $typeDefinition);
        } elseif ($typeDefinition instanceof ItemTypeDefinitionInterface) {
            return new ItemType($this->session, $typeDefinition);
        } elseif ($typeDefinition instanceof SecondaryTypeDefinitionInterface) {
            return new SecondaryType($this->session, $typeDefinition);
        } else {
            throw new CmisRuntimeException(
                sprintf('Unknown base type! Received "%s"', + get_class($typeDefinition)),
                1422028427
            );
        }
    }

    /**
     * @param string $principal
     * @param string[] $permissions
     * @return AceInterface
     */
    public function createAce($principal, array $permissions)
    {
        // TODO: Implement createAce() method.
    }

    /**
     * @param AceInterface[] $aces
     * @return AclInterface
     */
    public function createAcl(array $aces)
    {
        // TODO: Implement createAcl() method.
    }

    /**
     * Creates an object that implements the ContentStream interface.
     *
     * @param string $filename
     * @param integer $length
     * @param string $mimeType
     * @param mixed $stream @TODO define datatype
     * @param boolean $partial
     * @return StreamInterface
     */
    public function createContentStream($filename, $length, $mimeType, $stream, $partial = false)
    {
        // TODO: Implement createContentStream() method.
    }

    /**
     * Create a public API Property that contains the property definition and values.
     *
     * @param PropertyDefinitionInterface $type
     * @param array $values
     * @return Property
     */
    public function createProperty(PropertyDefinitionInterface $type, array $values)
    {
        return new Property($type, $values);
    }

    /**
     * Try to determined what object type the given objectData belongs to and return that type.
     *
     * @param ObjectDataInterface $objectData
     * @return ObjectTypeInterface|null The object type or <code>null</code> if type could not be determined
     */
    public function getTypeFromObjectData(ObjectDataInterface $objectData)
    {
        if ($objectData->getProperties() === null || count($objectData->getProperties()->getProperties()) === 0) {
            return null;
        }

        $typeProperty = $objectData->getProperties()->getProperties()[PropertyIds::OBJECT_TYPE_ID];

        if (!$typeProperty instanceof PropertyId) {
            return null;
        }

        return $this->session->getTypeDefinition($typeProperty->getFirstValue());
    }

    /**
     * Get the bindings object factory for the current session binding
     *
     * @return BindingsObjectFactoryInterface
     */
    protected function getBindingsObjectFactory()
    {
        return $this->session->getBinding()->getObjectFactory();
    }

    /**
     * Create a type definition with all required properties.
     *
     * @param string $id This opaque attribute identifies this object-type in the repository.
     * @param string $localName This attribute represents the underlying repository’s name for the object-type.
     *      This field is opaque and has no uniqueness constraint imposed by this specification.
     * @param string $baseTypeIdString A value that indicates whether the base type for this object-type is the
     *      document, folder, relationship, policy, item, or secondary base type.
     * @param string $parentId The id of the object-type’s immediate parent type. It MUST be "not set" for a base
     *      type. Depending on the binding this means it might not exist on the base type object-type definition.
     * @param boolean $creatable Indicates whether new objects of this type MAY be created. If the value of this
     *      attribute is FALSE, the repository MAY contain objects of this type already, but MUST NOT allow new objects
     *      of this type to be created.
     * @param boolean $fileable Indicates whether or not objects of this type are file-able.
     * @param boolean $queryable Indicates whether or not this object-type can appear in the FROM clause of a query
     *      statement. A non-queryable object-type is not visible through the relational view that is used for query,
     *      and CAN NOT appear in the FROM clause of a query statement.
     * @param boolean $controllablePolicy Indicates whether or not objects of this type are controllable via policies.
     *      Policy objects can only be applied to controllablePolicy objects.
     * @param boolean $controllableACL This attribute indicates whether or not objects of this type are controllable by
     *      ACL’s. Only objects that are controllableACL can have an ACL.
     * @param boolean $fulltextIndexed Indicates whether objects of this type are indexed for full-text search for
     *      querying via the CONTAINS() query predicate. If the value of this attribute is TRUE, the full-text index
     *      MUST cover the content and MAY cover the metadata.
     * @param boolean $includedInSupertypeQuery Indicates whether this type and its subtypes appear in a query of this
     *      type’s ancestor types. For example: if Invoice is a sub-type of cmis:document, if this is TRUE on Invoice
     *      then for a query on cmis:document, instances of Invoice will be returned if they match. If this attribute
     *      is FALSE, no instances of Invoice will be returned even if they match the query.
     * @param string $localNamespace This attribute allows repositories to represent the internal namespace of
     *      the underlying repository’s name for the object-type.
     * @param string $queryName Used for query and filter operations on object-types. This is an opaque string with
     *      limitations. See 2.1.2.1.3 Query Names of the CMIS 1.1 standard for details.
     * @param string $displayName Used for presentation by application.
     * @param string $description Description of this object-type, such as the nature of content, or its intended use.
     *      Used for presentation by application.
     * @param TypeMutabilityInterface|null $typeMutability
     *      typeMutability.create - Indicates whether new child types may be created with this type as the parent.
     *      typeMutability.update - Indicates whether clients may make changes to this type per the constraints
     *          defined in this specification.
     *      typeMutability.delete - Indicates whether clients may delete this type if there are no instances of it in
     *          the repository.
     * @return FolderTypeDefinition|DocumentTypeDefinition|RelationshipTypeDefinition|PolicyTypeDefinition|ItemTypeDefinition|SecondaryTypeDefinition
     */
    public function createTypeDefinition(
        $id,
        $localName,
        $baseTypeIdString,
        $parentId,
        $creatable,
        $fileable,
        $queryable,
        $controllablePolicy,
        $controllableACL,
        $fulltextIndexed,
        $includedInSupertypeQuery,
        $localNamespace = '',
        $queryName = '',
        $displayName = '',
        $description = '',
        TypeMutabilityInterface $typeMutability = null
    ) {
        $typeDefinition = $this->getBindingsObjectFactory()->getTypeDefinitionByBaseTypeId($baseTypeIdString, $id);

        $typeDefinition->setLocalName($localName);
        $typeDefinition->setParentTypeId($parentId);
        $typeDefinition->setIsCreatable($creatable);
        $typeDefinition->setIsFileable($fileable);
        $typeDefinition->setIsQueryable($queryable);
        $typeDefinition->setisControllablePolicy($controllablePolicy);
        $typeDefinition->setIsControllableAcl($controllableACL);
        $typeDefinition->setIsFulltextIndexed($fulltextIndexed);
        $typeDefinition->setIsIncludedInSupertypeQuery($includedInSupertypeQuery);
        $typeDefinition->setLocalNamespace($localNamespace);
        $typeDefinition->setQueryName($queryName);
        $typeDefinition->setDisplayName($displayName);
        $typeDefinition->setDescription($description);
        if ($typeMutability !== null) {
            $typeDefinition->setTypeMutability($typeMutability);
        }

        return $typeDefinition;
    }
}
