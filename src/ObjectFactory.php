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
use Dkd\PhpCmis\Data\ChangeEventInfoInterface;
use Dkd\PhpCmis\Data\ObjectDataInterface;
use Dkd\PhpCmis\Data\ObjectListInterface;
use Dkd\PhpCmis\Data\ObjectTypeInterface;
use Dkd\PhpCmis\Data\PolicyInterface;
use Dkd\PhpCmis\Data\PropertiesInterface;
use Dkd\PhpCmis\Data\PropertyDataInterface;
use Dkd\PhpCmis\Data\RenditionDataInterface;
use Dkd\PhpCmis\Data\RepositoryInfoInterface;
use Dkd\PhpCmis\Data\SecondaryTypeInterface;
use Dkd\PhpCmis\Definitions\PropertyDefinitionInterface;
use Dkd\PhpCmis\Definitions\TypeDefinitionInterface;
use Dkd\PhpCmis\Enum\Updatability;
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
     * @return void
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
                $secondaryType = $this->session->getTypeDefinition((string) $secondaryTypeId);

                if (!$secondaryType instanceof SecondaryType) {
                    throw new CmisInvalidArgumentException(
                        "Secondary types property contains a type that is not a secondary type: " . $secondaryTypeId
                    );
                }
                $allSecondaryTypes[] = $secondaryType;
            }
        }

        if (!empty($secondaryTypes) && empty($allSecondaryTypes)) {
            $allSecondaryTypes = $secondaryTypes;
        }

        $propertyList = array();

        foreach ($properties as $propertyId => $propertyValue) {
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

            if (is_array($propertyValue)) {
                if (!$definition->getCardinality()->equals(Cardinality::MULTI)) {
                    throw new CmisInvalidArgumentException(
                        sprintf('Property "%s" is not a multi value property but multiple values given!', $propertyId)
                    );
                }
                $values = $propertyValue;
            } else {
                if (!$definition->getCardinality()->equals(Cardinality::SINGLE)) {
                    throw new CmisInvalidArgumentException(
                        sprintf('Property "%s" is not a single value property but single value given!', $propertyId)
                    );
                }
                $values = array();
                $values[] = $propertyValue;
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
        if (empty($objectTypeId)) {
            throw new CmisInvalidArgumentException(
                'Type property must be set and must be of type string but is empty.'
            );
        }

        return $this->session->getTypeDefinition($objectTypeId);
    }

    /**
     * Get a value from an array. Return null if the key does not exist in the array.
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

    /**
     * Get the bindings object factory for the current session binding
     *
     * @return BindingsObjectFactoryInterface
     */
    protected function getBindingsObjectFactory()
    {
        return $this->session->getBinding()->getObjectFactory();
    }
}
