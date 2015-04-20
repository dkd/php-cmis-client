<?php
namespace Dkd\PhpCmis\DataObjects;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Data\AceInterface;
use Dkd\PhpCmis\Data\BindingsObjectFactoryInterface;
use Dkd\PhpCmis\Data\PropertyDataInterface;
use Dkd\PhpCmis\Definitions\PropertyBooleanDefinitionInterface;
use Dkd\PhpCmis\Definitions\PropertyDateTimeDefinitionInterface;
use Dkd\PhpCmis\Definitions\PropertyDecimalDefinitionInterface;
use Dkd\PhpCmis\Definitions\PropertyDefinitionInterface;
use Dkd\PhpCmis\Definitions\PropertyHtmlDefinitionInterface;
use Dkd\PhpCmis\Definitions\PropertyIdDefinitionInterface;
use Dkd\PhpCmis\Definitions\PropertyIntegerDefinitionInterface;
use Dkd\PhpCmis\Definitions\PropertyStringDefinitionInterface;
use Dkd\PhpCmis\Definitions\PropertyUriDefinitionInterface;
use Dkd\PhpCmis\Enum\BaseTypeId;
use Dkd\PhpCmis\Exception\CmisInvalidArgumentException;
use Dkd\PhpCmis\Exception\CmisRuntimeException;
use GuzzleHttp\Stream\StreamInterface;

/**
 * CMIS binding object factory implementation.
 */
class BindingsObjectFactory implements BindingsObjectFactoryInterface
{
    /**
     * Create a AccessControlEntry for the given principal and permissions
     *
     * @param string $principal
     * @param string[] $permissions
     * @return AccessControlEntry
     */
    public function createAccessControlEntry($principal, array $permissions)
    {
        return new AccessControlEntry(new Principal($principal), $permissions);
    }

    /**
     * @param AceInterface[] $aces
     * @return AccessControlList
     */
    public function createAccessControlList(array $aces)
    {
        return new AccessControlList($aces);
    }

    /**
     * TODO check if this method is required at all in the php implementation
     *
     * @param string $filename
     * @param integer $length
     * @param string $mimeType
     * @param mixed $stream @TODO define datatype
     * @return StreamInterface
     */
    public function createContentStream($filename, $length, $mimeType, $stream)
    {
        // TODO: Implement createContentStream() method.
    }

    /**
     * @param PropertyDataInterface[] $propertiesData
     * @return Properties
     */
    public function createPropertiesData(array $propertiesData)
    {
        $properties = new Properties();
        $properties->addProperties($propertiesData);
        return $properties;
    }

    /**
     * @param PropertyDefinitionInterface $propertyDefinition
     * @param array $values
     * @return PropertyDataInterface
     */
    public function createPropertyData(PropertyDefinitionInterface $propertyDefinition, array $values)
    {
        if ($propertyDefinition instanceof PropertyStringDefinitionInterface) {
            return $this->createPropertyStringData($propertyDefinition->getId(), $values);
        } elseif ($propertyDefinition instanceof PropertyBooleanDefinitionInterface) {
            return $this->createPropertyBooleanData($propertyDefinition->getId(), $values);
        } elseif ($propertyDefinition instanceof PropertyIdDefinitionInterface) {
            return $this->createPropertyIdData($propertyDefinition->getId(), $values);
        } elseif ($propertyDefinition instanceof PropertyDateTimeDefinitionInterface) {
            return $this->createPropertyDateTimeData($propertyDefinition->getId(), $values);
        } elseif ($propertyDefinition instanceof PropertyDecimalDefinitionInterface) {
            return $this->createPropertyDecimalData($propertyDefinition->getId(), $values);
        } elseif ($propertyDefinition instanceof PropertyHtmlDefinitionInterface) {
            return $this->createPropertyHtmlData($propertyDefinition->getId(), $values);
        } elseif ($propertyDefinition instanceof PropertyIntegerDefinitionInterface) {
            return $this->createPropertyIntegerData($propertyDefinition->getId(), $values);
        } elseif ($propertyDefinition instanceof PropertyUriDefinitionInterface) {
            return $this->createPropertyUriData($propertyDefinition->getId(), $values);
        }
        throw new CmisRuntimeException(sprintf('Unknown property definition: %s', get_class($propertyDefinition)));
    }

    /**
     * @param string $id
     * @param boolean[] $values
     * @return PropertyBoolean
     */
    public function createPropertyBooleanData($id, array $values)
    {
        return new PropertyBoolean($id, $values);
    }

    /**
     * @param string $id
     * @param \DateTime[] $values
     * @return PropertyDateTime
     */
    public function createPropertyDateTimeData($id, array $values)
    {
        return new PropertyDateTime($id, $values);
    }

    /**
     * @param string $id
     * @param float[] $values
     * @return PropertyDecimal
     */
    public function createPropertyDecimalData($id, array $values)
    {
        return new PropertyDecimal($id, $values);
    }

    /**
     * @param string $id
     * @param string[] $values
     * @return PropertyHtml
     */
    public function createPropertyHtmlData($id, array $values)
    {
        return new PropertyHtml($id, $values);
    }

    /**
     * @param string $id
     * @param string[] $values
     * @return PropertyId
     */
    public function createPropertyIdData($id, array $values)
    {
        return new PropertyId($id, $values);
    }

    /**
     * @param string $id
     * @param integer[] $values
     * @return PropertyInteger
     */
    public function createPropertyIntegerData($id, array $values)
    {
        return new PropertyInteger($id, $values);
    }

    /**
     * @param string $id
     * @param string[] $values
     * @return PropertyString
     */
    public function createPropertyStringData($id, array $values)
    {
        return new PropertyString($id, $values);
    }

    /**
     * @param string $id
     * @param string[] $values
     * @return PropertyUri
     */
    public function createPropertyUriData($id, array $values)
    {
        return new PropertyUri($id, $values);
    }

    /**
     * Get a type definition object by its base type id
     *
     * @param string $baseTypeIdString
     * @param string $typeId
     * @return FolderTypeDefinition|DocumentTypeDefinition|RelationshipTypeDefinition|PolicyTypeDefinition|ItemTypeDefinition|SecondaryTypeDefinition
     * @throws CmisInvalidArgumentException Exception is thrown if the base type exists in the BaseTypeId enumeration
     *      but is not implemented here. This could only happen if the base type enumeration is extended which requires
     *      a CMIS specification change.
     */
    public function getTypeDefinitionByBaseTypeId($baseTypeIdString, $typeId)
    {
        $baseTypeId = BaseTypeId::cast($baseTypeIdString);

        if ($baseTypeId->equals(BaseTypeId::cast(BaseTypeId::CMIS_FOLDER))) {
            $baseType = new FolderTypeDefinition($typeId);
        } elseif ($baseTypeId->equals(BaseTypeId::cast(BaseTypeId::CMIS_DOCUMENT))) {
            $baseType = new DocumentTypeDefinition($typeId);
        } elseif ($baseTypeId->equals(BaseTypeId::cast(BaseTypeId::CMIS_RELATIONSHIP))) {
            $baseType = new RelationshipTypeDefinition($typeId);
        } elseif ($baseTypeId->equals(BaseTypeId::cast(BaseTypeId::CMIS_POLICY))) {
            $baseType = new PolicyTypeDefinition($typeId);
        } elseif ($baseTypeId->equals(BaseTypeId::cast(BaseTypeId::CMIS_ITEM))) {
            $baseType = new ItemTypeDefinition($typeId);
        } elseif ($baseTypeId->equals(BaseTypeId::cast(BaseTypeId::CMIS_SECONDARY))) {
            $baseType = new SecondaryTypeDefinition($typeId);
        } else {
            // @codeCoverageIgnoreStart
            // this could only happen if a new baseType is added to the enumeration and not implemented here.
            throw new CmisInvalidArgumentException(
                sprintf('The given type definition "%s" could not be converted.', $baseTypeId)
            );
            // @codeCoverageIgnoreEnd
        }

        $baseType->setBaseTypeId($baseTypeId);

        return $baseType;
    }
}
