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

use Dkd\PhpCmis\CmisObject\CmisObjectInterface;
use Dkd\PhpCmis\Data\AceInterface;
use Dkd\PhpCmis\Data\AclInterface;
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
use Dkd\PhpCmis\DataObjects\DocumentTypeDefinition;
use Dkd\PhpCmis\DataObjects\FolderTypeDefinition;
use Dkd\PhpCmis\DataObjects\ItemTypeDefinition;
use Dkd\PhpCmis\DataObjects\PolicyTypeDefinition;
use Dkd\PhpCmis\DataObjects\RelationshipTypeDefinition;
use Dkd\PhpCmis\DataObjects\SecondaryTypeDefinition;
use Dkd\PhpCmis\Definitions\PropertyDefinitionInterface;
use Dkd\PhpCmis\Definitions\TypeDefinitionInterface;
use Dkd\PhpCmis\Definitions\TypeMutabilityInterface;
use Dkd\PhpCmis\Enum\Updatability;
use GuzzleHttp\Stream\StreamInterface;

/**
 * A factory to create and convert CMIS objects.
 * Custom ObjectFactory implementations may use the convert methods to inject specific
 * implementations of the interfaces when the data is transferred from the low level API to the high level API.
 */
interface ObjectFactoryInterface
{
    /**
     * @param AceInterface[] $aces
     * @return AclInterface
     */
    public function convertAces(array $aces);

    /**
     * @param ObjectDataInterface $objectData
     * @return ChangeEventInfoInterface
     */
    public function convertChangeEvent(ObjectDataInterface $objectData);

    /**
     * @param string $changeLogToken
     * @param ObjectListInterface $objectList
     * @return ChangeEventsInterface
     */
    public function convertChangeEvents($changeLogToken, ObjectListInterface $objectList);

    /**
     * Converts a high level ContentStream object into a low level ContentStream object.
     *
     * @param StreamInterface $contentStream the original ContentStream object
     * @return StreamInterface the ContentStream object
     */
    public function convertContentStream(StreamInterface $contentStream);

    /**
     * @param ObjectDataInterface $objectData
     * @param OperationContextInterface $context
     * @return CmisObjectInterface
     */
    public function convertObject(ObjectDataInterface $objectData, OperationContextInterface $context);

    /**
     * Converts a list of Policy objects into a list of there string representations
     *
     * @param PolicyInterface[] $policies
     * @return string[]
     */
    public function convertPolicies(array $policies);

    /**
     * Convert Properties in Properties instance to a list of PropertyInterface objects
     *
     * @param ObjectTypeInterface $objectType
     * @param SecondaryTypeInterface[] $secondaryTypes
     * @param PropertiesInterface $properties
     * @return PropertyInterface[]
     */
    public function convertPropertiesDataToPropertyList(
        ObjectTypeInterface $objectType,
        array $secondaryTypes,
        PropertiesInterface $properties
    );

    /**
     * Convert properties to their property data objects and put them into a Properties object
     *
     * @param mixed[] $properties
     * @param ObjectTypeInterface|null $type
     * @param SecondaryTypeInterface[] $secondaryTypes
     * @param Updatability[] $updatabilityFilter
     * @return PropertiesInterface
     */
    public function convertProperties(
        array $properties,
        ObjectTypeInterface $type = null,
        array $secondaryTypes = array(),
        array $updatabilityFilter = array()
    );

    /**
     * @param PropertiesInterface $properties
     * @return PropertyDataInterface[]
     */
    public function convertQueryProperties(PropertiesInterface $properties);

    /**
     * @param ObjectDataInterface $objectData
     * @return QueryResultInterface
     */
    public function convertQueryResult(ObjectDataInterface $objectData);

    /**
     * @param string $objectId
     * @param RenditionDataInterface $renditionData
     * @return RenditionInterface
     */
    public function convertRendition($objectId, RenditionDataInterface $renditionData);

    /**
     * @param RepositoryInfoInterface $repositoryInfo
     * @return RepositoryInfoInterface
     */
    public function convertRepositoryInfo(RepositoryInfoInterface $repositoryInfo);

    /**
     * @param TypeDefinitionInterface $typeDefinition
     * @return ObjectTypeInterface
     */
    public function convertTypeDefinition(TypeDefinitionInterface $typeDefinition);

    /**
     * @param string $principal
     * @param string[] $permissions
     * @return AceInterface
     */
    public function createAce($principal, array $permissions);

    /**
     * @param AceInterface[] $aces
     * @return AclInterface
     */
    public function createAcl(array $aces);

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
    public function createContentStream($filename, $length, $mimeType, $stream, $partial = false);

    /**
     * @param PropertyDefinitionInterface $type
     * @param array $values
     * @return PropertyInterface
     */
    public function createProperty(PropertyDefinitionInterface $type, array $values);

    /**
     * @param ObjectDataInterface $objectData
     * @return ObjectTypeInterface|null
     */
    public function getTypeFromObjectData(ObjectDataInterface $objectData);

    /**
     * @param SessionInterface $session
     * @param string[] $parameters
     */
    public function initialize(SessionInterface $session, $parameters = array());

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
    );
}
