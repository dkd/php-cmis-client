<?php
namespace Dkd\PhpCmis;

use Dkd\PhpCmis\Data\AceInterface;
use Dkd\PhpCmis\Data\AclInterface;
use Dkd\PhpCmis\Data\ObjectTypeInterface;
use Dkd\PhpCmis\Definitions\TypeDefinitionInterface;
use Dkd\PhpCmis\Enum\Updatability;
use Dkd\PhpCmis\CmisObject\CmisObjectInterface;
use Dkd\PhpCmis\Data\PropertiesInterface;
use Dkd\PhpCmis\Data\RenditionDataInterface;
use Dkd\PhpCmis\Data\RepositoryInfoInterface;
use GuzzleHttp\Stream\StreamInterface;
use Dkd\PhpCmis\Data\ObjectDataInterface;
use Dkd\PhpCmis\Definitions\PropertyDefinitionInterface;
use Dkd\PhpCmis\Data\PropertyDataInterface;
use Dkd\PhpCmis\Data\ObjectListInterface;
use Dkd\PhpCmis\Data\ChangeEventInfoInterface;

/**
 * This file is part of php-cmis-client
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
     * @param SessionInterface $session
     * @param string[] $parameters
     * @return void
     */
    public function initialize(SessionInterface $session, $parameters = array())
    {
        $this->session = $session;
    }

    /**
     * @param AceInterface[] $aces
     * @return AclInterface
     */
    public function convertAces(array $aces)
    {
        // TODO: Implement convertAces() method.
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
    }

    /**
     * @param ObjectDataInterface $objectData
     * @param OperationContextInterface $context
     * @return CmisObjectInterface
     */
    public function convertObject(ObjectDataInterface $objectData, OperationContextInterface $context)
    {
        // TODO: Implement convertObject() method.
    }

    /**
     * @param PolicyInterface[] $policies
     * @return string[]
     */
    public function convertPolicies($policies)
    {
        // TODO: Implement convertPolicies() method.
    }

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
    ) {
        // TODO: Implement convertProperties() method.
    }

    /**
     * @param PropertiesInterface $properties
     * @return PropertyDataInterface[]
     */
    public function convertQueryProperties(PropertiesInterface $properties)
    {
        // TODO: Implement convertQueryProperties() method.
    }

    /**
     * @param ObjectDataInterface $objectData
     * @return QueryResultInterface
     */
    public function convertQueryResult(ObjectDataInterface $objectData)
    {
        // TODO: Implement convertQueryResult() method.
    }

    /**
     * @param string $objectId
     * @param RenditionDataInterface $rendition
     * @return RenditionInterface
     */
    public function convertRendition($objectId, RenditionDataInterface $rendition)
    {
        // TODO: Implement convertRendition() method.
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
     * @param TypeDefinitionInterface $typeDefinition
     * @return ObjectTypeInterface
     */
    public function convertTypeDefinition(TypeDefinitionInterface $typeDefinition)
    {
        // TODO: Implement convertTypeDefinition() method.
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
     * @param PropertyDefinitionInterface $type
     * @param array $values
     * @return PropertyInterface
     */
    public function createProperty(PropertyDefinitionInterface $type, array $values)
    {
        // TODO: Implement createProperty() method.
    }

    /**
     * @param ObjectDataInterface $objectData
     * @return ObjectTypeInterface
     */
    public function getTypeFromObjectData(ObjectDataInterface $objectData)
    {
        // TODO: Implement getTypeFromObjectData() method.
    }
}
