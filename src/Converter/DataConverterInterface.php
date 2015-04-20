<?php
namespace Dkd\PhpCmis\Converter;

use Dkd\PhpCmis\Data\AclCapabilitiesInterface;
use Dkd\PhpCmis\Data\AclInterface;
use Dkd\PhpCmis\Data\AllowableActionsInterface;
use Dkd\PhpCmis\Data\CmisExtensionElementInterface;
use Dkd\PhpCmis\Data\ExtensionFeatureInterface;
use Dkd\PhpCmis\Data\ObjectDataInterface;
use Dkd\PhpCmis\Data\ObjectInFolderContainerInterface;
use Dkd\PhpCmis\Data\ObjectInFolderDataInterface;
use Dkd\PhpCmis\Data\ObjectInFolderListInterface;
use Dkd\PhpCmis\Data\ObjectListInterface;
use Dkd\PhpCmis\Data\ObjectParentDataInterface;
use Dkd\PhpCmis\Data\PropertiesInterface;
use Dkd\PhpCmis\Data\PropertyDataInterface;
use Dkd\PhpCmis\Data\RenditionDataInterface;
use Dkd\PhpCmis\Data\RepositoryCapabilitiesInterface;
use Dkd\PhpCmis\Data\RepositoryInfoInterface;
use Dkd\PhpCmis\DataObjects\ObjectInFolderData;
use Dkd\PhpCmis\DataObjects\PolicyIdList;
use Dkd\PhpCmis\Definitions\PropertyDefinitionInterface;
use Dkd\PhpCmis\Definitions\TypeDefinitionContainerInterface;
use Dkd\PhpCmis\Definitions\TypeDefinitionInterface;
use Dkd\PhpCmis\Definitions\TypeDefinitionListInterface;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer \sascha.egerer@dkd.de\
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
interface DataConverterInterface
{
    /**
     * Convert an acl object to a custom format
     *
     * @param AclInterface $acl
     * @return mixed
     */
    public function convertFromAcl(AclInterface $acl);

    /**
     * Convert an acl capabilities object to a custom format
     *
     * @param AclCapabilitiesInterface $aclCapabilities
     * @return mixed
     */
    public function convertFromAclCapabilities(AclCapabilitiesInterface $aclCapabilities);

    /**
     * Convert an allowable actions object to a custom format
     *
     * @param AllowableActionsInterface $allowableActions
     * @return mixed
     */
    public function convertFromAllowableActions(AllowableActionsInterface $allowableActions);

    /**
     * Convert a repository info object to a custom format
     *
     * @param RepositoryInfoInterface $repositoryInfo
     * @return mixed
     */
    public function convertFromRepositoryInfo(RepositoryInfoInterface $repositoryInfo);

    /**
     * Convert a repository capabilities object to a custom format
     *
     * @param RepositoryCapabilitiesInterface $repositoryCapabilities
     * @return mixed
     */
    public function convertFromRepositoryCapabilities(RepositoryCapabilitiesInterface $repositoryCapabilities);

    /**
     * Convert a rendition data object to a custom format
     *
     * @param RenditionDataInterface $rendition
     * @return mixed
     */
    public function convertFromRenditionData(RenditionDataInterface $rendition);

    /**
     * Convert a object data object to a custom format
     *
     * @param ObjectDataInterface $objectData
     * @return mixed
     */
    public function convertFromObjectData(ObjectDataInterface $objectData);

    /**
     * Convert a properties object to a custom format
     *
     * @param PropertiesInterface $properties
     * @return mixed
     */
    public function convertFromProperties(PropertiesInterface $properties);

    /**
     * Convert a property data object to a custom format
     *
     * @param PropertyDataInterface $propertyData
     * @return mixed
     */
    public function convertFromPropertyData(PropertyDataInterface $propertyData);

    /**
     * Convert a type definition object to a custom format
     *
     * @param TypeDefinitionInterface $typeDefinition
     * @return mixed
     */
    public function convertFromTypeDefinition(TypeDefinitionInterface $typeDefinition);

    /**
     * Convert a property definition object to a custom format
     *
     * @param PropertyDefinitionInterface $propertyDefinition
     * @return mixed
     */
    public function convertFromPropertyDefinition(PropertyDefinitionInterface $propertyDefinition);

    /**
     * Convert a type definition list object to a custom format
     *
     * @param TypeDefinitionListInterface $typeDefinitionList
     * @return mixed
     */
    public function convertFromTypeDefinitionList(TypeDefinitionListInterface $typeDefinitionList);

    /**
     * Convert a type definition container object to a custom format
     *
     * @param TypeDefinitionContainerInterface $typeDefinitionContainer
     * @return mixed
     */
    public function convertFromTypeDefinitionContainer(TypeDefinitionContainerInterface $typeDefinitionContainer);

    /**
     * Convert a object list object to a custom format
     *
     * @param ObjectListInterface $list
     * @return mixed
     */
    public function convertFromObjectList(ObjectListInterface $list);

    /**
     * Convert a object in folder data object to a custom format
     *
     * @param ObjectInFolderDataInterface $objectInFolder
     * @return mixed
     */
    public function convertFromObjectInFolderData(ObjectInFolderDataInterface $objectInFolder);

    /**
     * Convert a object in folder list object to a custom format
     *
     * @param ObjectInFolderListInterface $objectInFolder
     * @return mixed
     */
    public function convertFromObjectInFolderList(ObjectInFolderListInterface $objectInFolder);

    /**
     * Convert a object in folder container object to a custom format
     *
     * @param ObjectInFolderContainerInterface $container
     * @return mixed
     */
    public function convertFromObjectInFolderContainer(ObjectInFolderContainerInterface $container);

    /**
     * Convert a object in parent data object to a custom format
     *
     * @param ObjectParentDataInterface $container
     * @return mixed
     */
    public function convertFromObjectParentData(ObjectParentDataInterface $container);

    /**
     * Convert a extension feature object to a custom format
     *
     * @param ExtensionFeatureInterface $extensionFeature
     * @return mixed
     */
    public function convertFromExtensionFeature(ExtensionFeatureInterface $extensionFeature);

    /**
     * Convert given input data to a RepositoryInfo object
     *
     * @param array|null $data
     * @return RepositoryInfoInterface
     */
    public function convertRepositoryInfo(array $data = null);

    /**
     * Convert given input data to a RepositoryCapabilities object
     *
     * @param array|null $data
     * @return RepositoryCapabilitiesInterface
     */
    public function convertRepositoryCapabilities(array $data = null);

    /**
     * Convert given input data to a AclCapabilities object
     *
     * @param array|null $data
     * @return AclCapabilitiesInterface
     */
    public function convertAclCapabilities(array $data = null);
    
    /**
     * Convert given input data to a TypeDefinition object
     *
     * @param array|null $data
     * @return TypeDefinitionInterface
     */
    public function convertTypeDefinition(array $data = null);
    
    /**
     * Convert given input data to a PropertyDefinition object
     *
     * @param array|null $data
     * @return PropertyDefinitionInterface
     */
    public function convertPropertyDefinition(array $data = null);
    
    /**
     * Convert given input data to a TypeChildren object
     *
     * @param array|null $data
     * @return TypeDefinitionListInterface
     */
    public function convertTypeChildren(array $data = null);
    
    /**
     * Convert given input data to a TypeDescendants object
     *
     * @param array|null $data
     * @return TypeDefinitionContainerInterface[]
     */
    public function convertTypeDescendants(array $data = null);
    
    /**
     * Convert given input data to a ObjectData object
     *
     * @param array|null $data
     * @return ObjectDataInterface
     */
    public function convertObject(array $data = null);

    /**
     * Convert given input data to a ObjectData object
     *
     * @param array|null $data
     * @return ObjectDataInterface[]
     */
    public function convertObjects(array $data = null);

    /**
     * Convert given input data to a Acl object
     *
     * @param array|null $data
     * @param boolean $isExact
     * @return AclInterface
     */
    public function convertAcl(array $data = null, $isExact = false);

    /**
     * Convert given input data to a PolicyIdList object
     *
     * @param array|null $data
     * @return PolicyIdList
     */
    public function convertPolicyIdList(array $data = null);

    /**
     * Convert given input data to a Properties object
     *
     * @param array|null $data
     * @param array $extensions
     * @return PropertiesInterface
     */
    public function convertProperties(array $data = null, $extensions = array());

    /**
     * Convert given input data to a AllowableActions object
     *
     * @param array|null $data
     * @return AllowableActionsInterface
     */
    public function convertAllowableActions(array $data = null);

    /**
     * Convert given input data to a SuccinctProperties object
     *
     * @param array|null $data
     * @return PropertiesInterface
     */
    public function convertSuccinctProperties(array $data = null);

    /**
     * Convert given input data to a RenditionData object
     *
     * @param array|null $data
     * @return RenditionDataInterface
     */
    public function convertRendition(array $data = null);

    /**
     * Convert given input data to a list of RenditionData objects
     *
     * @param array|null $data
     * @return RenditionDataInterface[]
     */
    public function convertRenditions(array $data = null);

    /**
     * Convert given input data to a ObjectInFolderList object
     *
     * @param array|null $data
     * @return ObjectInFolderListInterface
     */
    public function convertObjectInFolderList(array $data = null);

    /**
     * Convert given input data to a ObjectInFolder object
     *
     * @param array|null $data
     * @return ObjectInFolderData|null
     */
    public function convertObjectInFolder(array $data = null);

    /**
     * Convert given input data to an Extension object
     *
     * @param array|null $data
     * @param array $cmisKeys
     * @return CmisExtensionElementInterface[]
     */
    public function convertExtension(array $data = null, array $cmisKeys = array());

    /**
     * Convert given input data to an ExtensionFeature object
     *
     * @param array|null $data
     * @return ExtensionFeatureInterface[]
     */
    public function convertExtensionFeatures(array $data = null);
}
