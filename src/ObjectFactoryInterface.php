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
use Dkd\PhpCmis\Data\ChangeEventInfoInterface;
use Dkd\PhpCmis\Data\ContentStreamInterface;
use Dkd\PhpCmis\Data\ObjectDataInterface;
use Dkd\PhpCmis\Data\ObjectListInterface;
use Dkd\PhpCmis\Data\PropertiesInterface;
use Dkd\PhpCmis\Data\PropertyDataInterface;
use Dkd\PhpCmis\Data\RenditionDataInterface;
use Dkd\PhpCmis\Data\RepositoryInfoInterface;
use Dkd\PhpCmis\Definitions\PropertyDefinitionInterface;
use Dkd\PhpCmis\Enum\Updatability;

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
     * @param ContentStreamInterface $contentStream the original ContentStream object
     * @return ContentStreamInterface the ContentStream object
     */
    public function convertContentStream(ContentStreamInterface $contentStream);

    /**
     * @param ObjectDataInterface $objectData
     * @param OperationContextInterface $context
     * @return CmisObjectInterface
     */
    public function convertObject(ObjectDataInterface $objectData, OperationContextInterface $context);

    /**
     * @param PolicyInterface[] $policies
     * @return string[]
     */
    public function convertPolicies($policies);

    /**
     * @param array $properties
     * @param ObjectTypeInterface $type
     * @param SecondaryTypeInterface[] $secondaryTypes
     * @param Updatability[] $updatabilityFilter
     * @return PropertiesInterface
     */
    public function convertProperties(
        array $properties,
        ObjectTypeInterface $type,
        array $secondaryTypes,
        array $updatabilityFilter
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
     * @param int $length
     * @param string $mimeType
     * @param mixed $stream  @TODO define datatype
     * @param boolean $partial
     * @return ContentStreamInterface
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
     * @return ObjectTypeInterface
     */
    public function getTypeFromObjectData(ObjectDataInterface $objectData);

    /**
     * @param SessionInterface $session
     * @param string[] $parameters
     * @return void
     */
    public function initialize(SessionInterface $session, $parameters);
}
