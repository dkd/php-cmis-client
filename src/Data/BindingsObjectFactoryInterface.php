<?php
namespace Dkd\PhpCmis\Data;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\DataObjects\DocumentTypeDefinition;
use Dkd\PhpCmis\DataObjects\FolderTypeDefinition;
use Dkd\PhpCmis\DataObjects\ItemTypeDefinition;
use Dkd\PhpCmis\DataObjects\PolicyTypeDefinition;
use Dkd\PhpCmis\DataObjects\RelationshipTypeDefinition;
use Dkd\PhpCmis\DataObjects\SecondaryTypeDefinition;
use Dkd\PhpCmis\Definitions\PropertyDefinitionInterface;
use Dkd\PhpCmis\Exception\CmisInvalidArgumentException;
use GuzzleHttp\Stream\StreamInterface;

/**
 * Factory for CMIS binding objects.
 */
interface BindingsObjectFactoryInterface
{
    /**
     * Create an access control entry object
     *
     * @param string $principal
     * @param string[] $permissions
     * @return AceInterface
     */
    public function createAccessControlEntry($principal, array $permissions);

    /**
     * Create an access control list object
     *
     * @param AceInterface[] $aces
     * @return AclInterface
     */
    public function createAccessControlList(array $aces);

    /**
     * Create an content stream object
     *
     * @param string $filename
     * @param integer $length
     * @param string $mimeType
     * @param mixed $stream
     * @return StreamInterface
     */
    public function createContentStream($filename, $length, $mimeType, $stream);

    /**
     * Create an array of property data objects
     *
     * @param PropertyDataInterface[] $properties
     * @return PropertiesInterface
     */
    public function createPropertiesData(array $properties);

    /**
     * Create a property data object
     *
     * @param PropertyDefinitionInterface $propertyDefinition
     * @param array $values
     * @return PropertyDataInterface
     */
    public function createPropertyData(PropertyDefinitionInterface $propertyDefinition, array $values);

    /**
     * Create a boolean data property object
     *
     * @param string $id
     * @param boolean[] $values
     * @return PropertyBooleanInterface
     */
    public function createPropertyBooleanData($id, array $values);

    /**
     * Create a property data time data object
     *
     * @param string $id
     * @param \DateTime[] $values
     * @return PropertyDateTimeInterface
     */
    public function createPropertyDateTimeData($id, array $values);

    /**
     * Create a decimal data property object
     *
     * @param string $id
     * @param integer[] $values
     * @return PropertyDecimalInterface
     */
    public function createPropertyDecimalData($id, array $values);

    /**
     * Create a html data property object
     *
     * @param string $id
     * @param string[] $values
     * @return PropertyHtmlInterface
     */
    public function createPropertyHtmlData($id, array $values);

    /**
     * Create an id data property object
     *
     * @param string $id
     * @param string[] $values
     * @return PropertyIdInterface
     */
    public function createPropertyIdData($id, array $values);

    /**
     * Create an integer data property object
     *
     * @param string $id
     * @param integer[] $values
     * @return PropertyIntegerInterface
     */
    public function createPropertyIntegerData($id, array $values);

    /**
     * Create a string data property object
     *
     * @param string $id
     * @param string[] $values
     * @return PropertyStringInterface
     */
    public function createPropertyStringData($id, array $values);

    /**
     * Create a uri data property object
     *
     * @param string $id
     * @param string[] $values
     * @return PropertyUriInterface
     */
    public function createPropertyUriData($id, array $values);


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
    public function getTypeDefinitionByBaseTypeId($baseTypeIdString, $typeId);
}
