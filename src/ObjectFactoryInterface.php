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
use Dkd\PhpCmis\Data\RepositoryInfoInterface;
use Dkd\PhpCmis\Data\SecondaryTypeInterface;
use Dkd\PhpCmis\Definitions\PropertyDefinitionInterface;
use Dkd\PhpCmis\Definitions\TypeDefinitionInterface;
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
     * @param ObjectTypeInterface $type
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
     * @param RenditionDataInterface $rendition
     * @return RenditionInterface
     */
    public function convertRendition($objectId, RenditionDataInterface $rendition);

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
}
