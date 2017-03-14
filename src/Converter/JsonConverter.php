<?php
namespace Dkd\PhpCmis\Converter;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use function array_diff_key;
use function array_filter;
use function array_flip;
use function array_map;
use function array_walk;
use Dkd\Enumeration\Exception\InvalidEnumerationValueException;
use Dkd\PhpCmis\Bindings\Browser\JSONConstants;
use Dkd\PhpCmis\Converter\Types\TypeConverterInterface;
use Dkd\PhpCmis\Data\AclCapabilitiesInterface;
use Dkd\PhpCmis\Data\AclInterface;
use Dkd\PhpCmis\Data\AllowableActionsInterface;
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
use Dkd\PhpCmis\DataObjects\AbstractTypeDefinition;
use Dkd\PhpCmis\DataObjects\AccessControlEntry;
use Dkd\PhpCmis\DataObjects\AccessControlList;
use Dkd\PhpCmis\DataObjects\AclCapabilities;
use Dkd\PhpCmis\DataObjects\AllowableActions;
use Dkd\PhpCmis\DataObjects\BindingsObjectFactory;
use Dkd\PhpCmis\DataObjects\ChangeEventInfo;
use Dkd\PhpCmis\DataObjects\CmisExtensionElement;
use Dkd\PhpCmis\DataObjects\CreatablePropertyTypes;
use Dkd\PhpCmis\DataObjects\ExtensionFeature;
use Dkd\PhpCmis\DataObjects\FailedToDeleteData;
use Dkd\PhpCmis\DataObjects\NewTypeSettableAttributes;
use Dkd\PhpCmis\DataObjects\ObjectData;
use Dkd\PhpCmis\DataObjects\ObjectInFolderContainer;
use Dkd\PhpCmis\DataObjects\ObjectInFolderData;
use Dkd\PhpCmis\DataObjects\ObjectInFolderList;
use Dkd\PhpCmis\DataObjects\ObjectList;
use Dkd\PhpCmis\DataObjects\ObjectParentData;
use Dkd\PhpCmis\DataObjects\PermissionDefinition;
use Dkd\PhpCmis\DataObjects\PermissionMapping;
use Dkd\PhpCmis\DataObjects\PolicyIdList;
use Dkd\PhpCmis\DataObjects\Principal;
use Dkd\PhpCmis\DataObjects\Properties;
use Dkd\PhpCmis\DataObjects\PropertyBoolean;
use Dkd\PhpCmis\DataObjects\PropertyBooleanDefinition;
use Dkd\PhpCmis\DataObjects\PropertyDateTime;
use Dkd\PhpCmis\DataObjects\PropertyDateTimeDefinition;
use Dkd\PhpCmis\DataObjects\PropertyDecimal;
use Dkd\PhpCmis\DataObjects\PropertyDecimalDefinition;
use Dkd\PhpCmis\DataObjects\PropertyHtml;
use Dkd\PhpCmis\DataObjects\PropertyHtmlDefinition;
use Dkd\PhpCmis\DataObjects\PropertyId;
use Dkd\PhpCmis\DataObjects\PropertyIdDefinition;
use Dkd\PhpCmis\DataObjects\PropertyInteger;
use Dkd\PhpCmis\DataObjects\PropertyIntegerDefinition;
use Dkd\PhpCmis\DataObjects\PropertyString;
use Dkd\PhpCmis\DataObjects\PropertyStringDefinition;
use Dkd\PhpCmis\DataObjects\PropertyUri;
use Dkd\PhpCmis\DataObjects\PropertyUriDefinition;
use Dkd\PhpCmis\DataObjects\RenditionData;
use Dkd\PhpCmis\DataObjects\RepositoryCapabilities;
use Dkd\PhpCmis\DataObjects\RepositoryInfoBrowserBinding;
use Dkd\PhpCmis\DataObjects\TypeDefinitionContainer;
use Dkd\PhpCmis\DataObjects\TypeDefinitionList;
use Dkd\PhpCmis\DataObjects\TypeMutability;
use Dkd\PhpCmis\Definitions\MutableDocumentTypeDefinitionInterface;
use Dkd\PhpCmis\Definitions\MutableRelationshipTypeDefinitionInterface;
use Dkd\PhpCmis\Definitions\MutableTypeDefinitionInterface;
use Dkd\PhpCmis\Definitions\PropertyDefinitionInterface;
use Dkd\PhpCmis\Definitions\RelationshipTypeDefinitionInterface;
use Dkd\PhpCmis\Definitions\TypeDefinitionContainerInterface;
use Dkd\PhpCmis\Definitions\TypeDefinitionInterface;
use Dkd\PhpCmis\Definitions\TypeDefinitionListInterface;
use Dkd\PhpCmis\Enum\AclPropagation;
use Dkd\PhpCmis\Enum\Action;
use Dkd\PhpCmis\Enum\BaseTypeId;
use Dkd\PhpCmis\Enum\CapabilityAcl;
use Dkd\PhpCmis\Enum\CapabilityChanges;
use Dkd\PhpCmis\Enum\CapabilityContentStreamUpdates;
use Dkd\PhpCmis\Enum\CapabilityJoin;
use Dkd\PhpCmis\Enum\CapabilityOrderBy;
use Dkd\PhpCmis\Enum\CapabilityQuery;
use Dkd\PhpCmis\Enum\CapabilityRenditions;
use Dkd\PhpCmis\Enum\Cardinality;
use Dkd\PhpCmis\Enum\ChangeType;
use Dkd\PhpCmis\Enum\CmisVersion;
use Dkd\PhpCmis\Enum\ContentStreamAllowed;
use Dkd\PhpCmis\Enum\DateTimeResolution;
use Dkd\PhpCmis\Enum\DecimalPrecision;
use Dkd\PhpCmis\Enum\PropertyType;
use Dkd\PhpCmis\Enum\SupportedPermissions;
use Dkd\PhpCmis\Enum\Updatability;
use Dkd\PhpCmis\Exception\CmisInvalidArgumentException;
use Dkd\PhpCmis\Exception\CmisRuntimeException;
use function is_array;

/**
 * Convert PHP CMIS Objects to JSON and JSON Responses TO PHP CMIS Objects
 *
 * @TODO: To reduce the complexity of this class there should be some kind of schema mapping in the future.
 */
class JsonConverter extends AbstractDataConverter
{
    /**
     * @param array|null $data
     * @return AllowableActions|null
     */
    public function convertAllowableActions(array $data = null)
    {
        if (empty($data)) {
            return null;
        }

        $allowableActions = new AllowableActions();
        $actions = [];
        $extensions = [];

        foreach ($data as $key => $value) {
            try {
                if ((boolean) $value === true) {
                    $actions[] = Action::cast($key);
                }
            } catch (InvalidEnumerationValueException $exception) {
                $extensions[$key] = $value;
            }
        }

        $allowableActions->setAllowableActions($actions);
        $allowableActions->setExtensions($this->convertExtension($extensions));

        return $allowableActions;
    }

    /**
     * @param array|null $data The JSON that contains the repository info
     * @return null|RepositoryInfoBrowserBinding
     */
    public function convertRepositoryInfo(array $data = null)
    {
        return empty($data) ? null : $this->setRepositoryInfoValues(new RepositoryInfoBrowserBinding(), $data);
    }

    /**
     * @param RepositoryInfoBrowserBinding $object
     * @param array $data
     * @return RepositoryInfoBrowserBinding
     */
    protected function setRepositoryInfoValues(RepositoryInfoBrowserBinding $object, $data)
    {
        $data[JSONConstants::JSON_REPINFO_CAPABILITIES] = $this->convertRepositoryCapabilities(
            $data[JSONConstants::JSON_REPINFO_CAPABILITIES] ?? null
        );

        $data[JSONConstants::JSON_REPINFO_ACL_CAPABILITIES] = $this->convertAclCapabilities(
            $data[JSONConstants::JSON_REPINFO_ACL_CAPABILITIES] ?? null
        );

        $data[JSONConstants::JSON_REPINFO_CHANGES_ON_TYPE] = array_map(
            function ($item) { return BaseTypeId::cast($item); },
            array_filter(
                $data[JSONConstants::JSON_REPINFO_CHANGES_ON_TYPE] ?? [],
                function ($item) { return !empty($item); }
            )
        );

        $data[JSONConstants::JSON_REPINFO_EXTENDED_FEATURES] = $this->convertExtensionFeatures(
            $data[JSONConstants::JSON_REPINFO_EXTENDED_FEATURES] ?? []
        );

        if (isset($data[JSONConstants::JSON_REPINFO_CMIS_VERSION_SUPPORTED])) {
            $data[JSONConstants::JSON_REPINFO_CMIS_VERSION_SUPPORTED] = CmisVersion::cast(
                $data[JSONConstants::JSON_REPINFO_CMIS_VERSION_SUPPORTED]
            );
        }

        $data = array_filter($data, function ($item) { return $item !== null; });

        $object->setExtensions($this->convertExtension($data, JSONConstants::getRepositoryInfoKeys()));

        $object->populate(
            $data,
            array_merge(
                array_combine(JSONConstants::getRepositoryInfoKeys(), JSONConstants::getRepositoryInfoKeys()),
                [
                    JSONConstants::JSON_REPINFO_DESCRIPTION => 'description',
                    JSONConstants::JSON_REPINFO_CMIS_VERSION_SUPPORTED => 'cmisVersion',
                    JSONConstants::JSON_REPINFO_ID => 'id',
                    JSONConstants::JSON_REPINFO_ROOT_FOLDER_URL => 'rootUrl',
                    JSONConstants::JSON_REPINFO_NAME => 'name',
                    JSONConstants::JSON_REPINFO_EXTENDED_FEATURES => 'extensionFeatures'
                ]
            ),
            true
        );

        return $object;
    }

    /**
     * @param array|null $data
     * @return RepositoryCapabilities|null
     */
    public function convertRepositoryCapabilities(array $data = null)
    {
        if (empty($data)) {
            return null;
        }

        if (isset($data[JSONConstants::JSON_CAP_CONTENT_STREAM_UPDATABILITY])) {
            $data[JSONConstants::JSON_CAP_CONTENT_STREAM_UPDATABILITY] = CapabilityContentStreamUpdates::cast(
                $data[JSONConstants::JSON_CAP_CONTENT_STREAM_UPDATABILITY]
            );
        }
        if (isset($data[JSONConstants::JSON_CAP_CHANGES])) {
            $data[JSONConstants::JSON_CAP_CHANGES] = CapabilityChanges::cast($data[JSONConstants::JSON_CAP_CHANGES]);
        }
        if (isset($data[JSONConstants::JSON_CAP_RENDITIONS])) {
            $data[JSONConstants::JSON_CAP_RENDITIONS] = CapabilityRenditions::cast(
                $data[JSONConstants::JSON_CAP_RENDITIONS]
            );
        }
        if (isset($data[JSONConstants::JSON_CAP_ORDER_BY])) {
            $data[JSONConstants::JSON_CAP_ORDER_BY] = CapabilityOrderBy::cast(
                $data[JSONConstants::JSON_CAP_ORDER_BY]
            );
        }
        if (isset($data[JSONConstants::JSON_CAP_QUERY])) {
            $data[JSONConstants::JSON_CAP_QUERY] = CapabilityQuery::cast($data[JSONConstants::JSON_CAP_QUERY]);
        }
        if (isset($data[JSONConstants::JSON_CAP_JOIN])) {
            $data[JSONConstants::JSON_CAP_JOIN] = CapabilityJoin::cast($data[JSONConstants::JSON_CAP_JOIN]);
        }
        if (isset($data[JSONConstants::JSON_CAP_ACL])) {
            $data[JSONConstants::JSON_CAP_ACL] = CapabilityAcl::cast($data[JSONConstants::JSON_CAP_ACL]);
        }

        if (isset($data[JSONConstants::JSON_CAP_CREATABLE_PROPERTY_TYPES])) {
            $data[JSONConstants::JSON_CAP_CREATABLE_PROPERTY_TYPES] = $this->convertCreatablePropertyTypes(
                $data[JSONConstants::JSON_CAP_CREATABLE_PROPERTY_TYPES]
            );
        }

        if (isset($data[JSONConstants::JSON_CAP_NEW_TYPE_SETTABLE_ATTRIBUTES])) {
            $data[JSONConstants::JSON_CAP_NEW_TYPE_SETTABLE_ATTRIBUTES] = $this->convertNewTypeSettableAttributes(
                $data[JSONConstants::JSON_CAP_NEW_TYPE_SETTABLE_ATTRIBUTES]
            );
        }

        $data = array_filter($data, function ($item) { return $item !== null; });

        $repositoryCapabilities = new RepositoryCapabilities();
        $repositoryCapabilities->setExtensions($this->convertExtension($data, JSONConstants::getCapabilityKeys()));

        $repositoryCapabilities->populate(
            $data,
            array_merge(
                array_combine(JSONConstants::getCapabilityKeys(), JSONConstants::getCapabilityKeys()),
                [
                    JSONConstants::JSON_CAP_CONTENT_STREAM_UPDATABILITY => 'contentStreamUpdatesCapability',
                    JSONConstants::JSON_CAP_CHANGES => 'changesCapability',
                    JSONConstants::JSON_CAP_RENDITIONS => 'renditionsCapability',
                    JSONConstants::JSON_CAP_GET_DESCENDANTS => 'supportsGetDescendants',
                    JSONConstants::JSON_CAP_GET_FOLDER_TREE => 'supportsGetFolderTree',
                    JSONConstants::JSON_CAP_MULTIFILING => 'supportsMultifiling',
                    JSONConstants::JSON_CAP_UNFILING => 'supportsUnfiling',
                    JSONConstants::JSON_CAP_VERSION_SPECIFIC_FILING => 'supportsVersionSpecificFiling',
                    JSONConstants::JSON_CAP_PWC_SEARCHABLE => 'supportsPwcSearchable',
                    JSONConstants::JSON_CAP_PWC_UPDATABLE => 'supportsPwcUpdatable',
                    JSONConstants::JSON_CAP_ALL_VERSIONS_SEARCHABLE => 'supportsAllVersionsSearchable',
                    JSONConstants::JSON_CAP_ORDER_BY => 'orderByCapability',
                    JSONConstants::JSON_CAP_QUERY => 'queryCapability',
                    JSONConstants::JSON_CAP_JOIN => 'joinCapability',
                    JSONConstants::JSON_CAP_ACL => 'aclCapability',
                    JSONConstants::JSON_CAP_CREATABLE_PROPERTY_TYPES => 'creatablePropertyTypes',
                    JSONConstants::JSON_CAP_NEW_TYPE_SETTABLE_ATTRIBUTES => 'newTypeSettableAttributes'
                ]
            ),
            true
        );

        return $repositoryCapabilities;
    }

    /**
     * Create NewTypeSettableAttributes object and populate given data to it
     *
     * @param string[]|null $data
     * @return NewTypeSettableAttributes|null Returns object or <code>null</code> if given data is empty
     */
    public function convertNewTypeSettableAttributes(array $data = null)
    {
        if (empty($data)) {
            return null;
        }
        $object = new NewTypeSettableAttributes();

        $object->populate(
            $data,
            array_combine(
                JSONConstants::getCapabilityNewTypeSettableAttributeKeys(),
                array_map(
                    function ($key) {
                        // add a prefix "canSet" to all keys as this are the property names
                        return 'canSet' . ucfirst($key);
                    },
                    JSONConstants::getCapabilityNewTypeSettableAttributeKeys()
                )
            ),
            true
        );

        $object->setExtensions(
            $this->convertExtension(
                $data,
                JSONConstants::getCapabilityNewTypeSettableAttributeKeys()
            )
        );

        return $object;
    }

    /**
     * Create CreatablePropertyTypes object and populate given data to it
     *
     * @param array|null $data The data that should be populated to the CreatablePropertyTypes object
     * @return CreatablePropertyTypes|null Returns a CreatablePropertyTypes object or <code>null</code> if empty data
     *      is given.
     */
    public function convertCreatablePropertyTypes(array $data = null)
    {
        if (empty($data)) {
            return null;
        }

        $object = new CreatablePropertyTypes();

        foreach ($data[JSONConstants::JSON_CAP_CREATABLE_PROPERTY_TYPES_CANCREATE] ?? [] as $canCreateItem) {
            try {
                $canCreate[] = PropertyType::cast($canCreateItem);
            } catch (InvalidEnumerationValueException $exception) {
                // ignore invalid types
            }
        }

        $object->setCanCreate($canCreate);

        $object->setExtensions(
            $this->convertExtension(
                $data,
                JSONConstants::getCapabilityCreatablePropertyKeys()
            )
        );

        return $object;
    }

    /**
     * @param array|null $data
     * @param boolean $isExact
     * @return AccessControlList
     */
    public function convertAcl(array $data = null, $isExact = false)
    {
        if (empty($data)) {
            return null;
        }

        $aces = [];
        if (isset($data[JSONConstants::JSON_ACL_ACES]) && is_array($data[JSONConstants::JSON_ACL_ACES])) {
            foreach ($data[JSONConstants::JSON_ACL_ACES] as $aceData) {
                if (empty($aceData[JSONConstants::JSON_ACE_PRINCIPAL][JSONConstants::JSON_ACE_PRINCIPAL_ID])) {
                    continue;
                }

                $permissions = array_filter(
                    $aceData[JSONConstants::JSON_ACE_PERMISSIONS] ?? [],
                    function($item) { return !empty($item); }
                );

                $principal = new Principal(
                    (string) $aceData[JSONConstants::JSON_ACE_PRINCIPAL][JSONConstants::JSON_ACE_PRINCIPAL_ID]
                );

                $principal->setExtensions(
                    $this->convertExtension(
                        $aceData[JSONConstants::JSON_ACE_PRINCIPAL],
                        JSONConstants::getAcePrincipalKeys()
                    )
                );

                $ace = new AccessControlEntry($principal, $permissions);

                if (isset($aceData[JSONConstants::JSON_ACE_IS_DIRECT])) {
                    $ace->setIsDirect((boolean) $aceData[JSONConstants::JSON_ACE_IS_DIRECT] ?? false);
                }

                $ace->setExtensions($this->convertExtension($aceData, JSONConstants::getAceKeys()));

                $aces[] = $ace;
            }
        }

        $acl = new AccessControlList($aces);
        $acl->setIsExact($isExact);
        $acl->setExtensions($this->convertExtension($data, JSONConstants::getAclKeys()));

        return $acl;
    }

    /**
     * @param array|null $data
     * @return AclCapabilities|null
     */
    public function convertAclCapabilities(array $data = null)
    {
        if (empty($data)) {
            return null;
        }

        $aclCapabilities = new AclCapabilities();
        $aclCapabilities->setSupportedPermissions(
            SupportedPermissions::cast($data[JSONConstants::JSON_ACLCAP_SUPPORTED_PERMISSIONS])
        );
        $aclCapabilities->setAclPropagation(
            AclPropagation::cast($data[JSONConstants::JSON_ACLCAP_ACL_PROPAGATION])
        );

        $permissionDefinitionList = [];

        foreach ($data[JSONConstants::JSON_ACLCAP_PERMISSIONS] ?? [] as $permissionData) {
            $permissionDefinition = new PermissionDefinition();

            $permissionDefinition->setId(
                (string) $permissionData[JSONConstants::JSON_ACLCAP_PERMISSION_PERMISSION]
            );
            $permissionDefinition->setDescription(
                (string) $permissionData[JSONConstants::JSON_ACLCAP_PERMISSION_DESCRIPTION]
            );

            // handle extensions

            $permissionDefinition->setExtensions(
                $this->convertExtension(
                    $permissionData,
                    JSONConstants::getAclCapabilityPermissionKeys()
                )
            );

            $permissionDefinitionList[] = $permissionDefinition;
        }

        $aclCapabilities->setPermissions($permissionDefinitionList);

        $permissionMappingList = [];

        foreach ($data[JSONConstants::JSON_ACLCAP_PERMISSION_MAPPING] ?? [] as $permissionMapping) {
            $mapping = new PermissionMapping();
            $key = (string) $permissionMapping[JSONConstants::JSON_ACLCAP_MAPPING_KEY];

            $mapping->setKey($key);

            $mapping->setPermissions(
                array_map(
                    'strval',
                    array_filter(
                    $permissionMapping[JSONConstants::JSON_ACLCAP_MAPPING_PERMISSION] ?? [],
                        function ($item) { return !empty($item); }
                    )
                )
            );

            $mapping->setExtensions(
                $this->convertExtension($permissionMapping, JSONConstants::getAclCapabilityMappingKeys())
            );

            $permissionMappingList[$key] = $mapping;
        }

        $aclCapabilities->setPermissionMapping($permissionMappingList);

        // handle extensions
        $aclCapabilities->setExtensions($this->convertExtension($data, JSONConstants::getAclCapabilityKeys()));

        return $aclCapabilities;
    }

    /**
     * Convert an array to a type definition object
     *
     * @param array|null $data
     * @return AbstractTypeDefinition|null
     * @throws CmisInvalidArgumentException
     */
    public function convertTypeDefinition(array $data = null)
    {
        if (empty($data)) {
            return null;
        }

        if (empty($data[JSONConstants::JSON_TYPE_ID])) {
            throw new CmisInvalidArgumentException('Id of type definition is empty but must not be empty!');
        }
        $typeDefinition = $this->getBindingsObjectFactory()->getTypeDefinitionByBaseTypeId(
            $data[JSONConstants::JSON_TYPE_BASE_ID] ?? '',
            $data[JSONConstants::JSON_TYPE_ID] ?? ''
        );

        $data = $this->convertTypeDefinitionSpecificData($data, $typeDefinition);

        $data[JSONConstants::JSON_TYPE_TYPE_MUTABILITY] = $this->convertTypeMutability(
            $data[JSONConstants::JSON_TYPE_TYPE_MUTABILITY] ?? null
        );

        foreach ($data[JSONConstants::JSON_TYPE_PROPERTY_DEFINITIONS] ?? [] as $propertyDefinitionData) {
            if (is_array($propertyDefinitionData)) {
                $propertyDefinition = $this->convertPropertyDefinition($propertyDefinitionData);
                if ($propertyDefinition !== null) {
                    $typeDefinition->addPropertyDefinition($propertyDefinition);
                }
            }
        }

        unset(
            $data[JSONConstants::JSON_TYPE_BASE_ID],
            $data[JSONConstants::JSON_TYPE_ID],
            $data[JSONConstants::JSON_TYPE_PROPERTY_DEFINITIONS]
        );

        $typeDefinition->populate(
            array_filter($data, function ($item) { return !empty($item); }),
            array_merge(
                array_combine(JSONConstants::getTypeKeys(), JSONConstants::getTypeKeys()),
                [
                    JSONConstants::JSON_TYPE_PARENT_ID => 'parentTypeId',
                    JSONConstants::JSON_TYPE_ALLOWED_TARGET_TYPES => 'allowedTargetTypeIds',
                    JSONConstants::JSON_TYPE_ALLOWED_SOURCE_TYPES => 'allowedSourceTypeIds',
                ]
            ),
            true
        );

        $typeDefinition->setExtensions($this->convertExtension($data, JSONConstants::getTypeKeys()));

        return $typeDefinition;
    }

    /**
     * Convert specific type definition data so it can be populated to the type definition
     *
     * @param MutableTypeDefinitionInterface $typeDefinition The type definition to set the data to
     * @param array $data The data that contains the values that should be applied to the object
     * @return MutableTypeDefinitionInterface The type definition with the specific data applied
     */
    private function convertTypeDefinitionSpecificData(array $data, MutableTypeDefinitionInterface $typeDefinition)
    {
        if ($typeDefinition instanceof MutableDocumentTypeDefinitionInterface) {
            if (!empty($data[JSONConstants::JSON_TYPE_CONTENTSTREAM_ALLOWED])) {
                $data[JSONConstants::JSON_TYPE_CONTENTSTREAM_ALLOWED] = ContentStreamAllowed::cast(
                    $data[JSONConstants::JSON_TYPE_CONTENTSTREAM_ALLOWED]
                );
            }
        } elseif ($typeDefinition instanceof MutableRelationshipTypeDefinitionInterface) {

            $data[JSONConstants::JSON_TYPE_ALLOWED_SOURCE_TYPES] = array_map(
                'strval',
                array_filter(
                    $data[JSONConstants::JSON_TYPE_ALLOWED_SOURCE_TYPES] ?? [],
                    function ($item) { return !empty($item); }
                )
            );
            $data[JSONConstants::JSON_TYPE_ALLOWED_SOURCE_TYPES] = array_map(
                'strval',
                array_filter(
                    $data[JSONConstants::JSON_TYPE_ALLOWED_SOURCE_TYPES] ?? [],
                    function ($item) { return !empty($item); }
                )
            );
            $data[JSONConstants::JSON_TYPE_ALLOWED_TARGET_TYPES] = array_map(
                'strval',
                array_filter(
                    $data[JSONConstants::JSON_TYPE_ALLOWED_TARGET_TYPES] ?? [],
                    function ($item) { return !empty($item); }
                )
            );

        }

        return $data;
    }

    /**
     * Convert an array to a type mutability object
     *
     * @param array|null $data The data that should be populated to the object
     * @return TypeMutability|null Returns the type mutability object or <code>null</code> if empty array is given
     */
    public function convertTypeMutability(array $data = null)
    {
        if (empty($data)) {
            return null;
        }
        $typeMutability = new TypeMutability();
        $typeMutability->populate(
            $data,
            array_combine(
                JSONConstants::getTypeTypeMutabilityKeys(),
                array_map(
                    function ($key) {
                        // add a prefix "can" to all keys as this are the property names
                        return 'can' . ucfirst($key);
                    },
                    JSONConstants::getTypeTypeMutabilityKeys()
                )
            ),
            true
        );

        $typeMutability->setExtensions(
            $this->convertExtension($data, JSONConstants::getTypeTypeMutabilityKeys())
        );

        return $typeMutability;
    }

    /**
     * @param array|null $data
     * @return PropertyDefinitionInterface|null
     */
    public function convertPropertyDefinition(array $data = null)
    {
        if (empty($data)) {
            return null;
        }

        $data = $this->preparePropertyDefinitionData($data);

        $propertyDefinition = $this->getPropertyDefinitionByType(
            $data[JSONConstants::JSON_PROPERTY_TYPE_PROPERTY_TYPE],
            $data
        );

        // remove the id property as it has been already set to the property definition.
        unset($data[JSONConstants::JSON_PROPERTY_TYPE_ID]);

        // TODO
//        $propertyDefinition->setChoices(
//            $this->convertChoicesString($data[JSONConstants::JSON_PROPERTY_TYPE_CHOICE]) // TODO
//        );
//
//        // default value
//        Object defaultValue = json.get(JSON_PROPERTY_TYPE_DEAULT_VALUE);
//        if (defaultValue != null) {
//            if (defaultValue instanceof List) {
//                List values = new ArrayList();
//                for (Object value : (List) defaultValue) {
//                    values.add(getCMISValue(value, propertyType));
//                }
//                result.setDefaultValue(values);
//            } else {
//                result.setDefaultValue((List) Collections.singletonList(getCMISValue(defaultValue, propertyType)));
//            }
//        }

        $propertyDefinition->populate(
            $data,
            [JSONConstants::JSON_PROPERTY_TYPE_RESOLUTION => 'dateTimeResolution']
        );
        $propertyDefinition->setExtensions($this->convertExtension($data, JSONConstants::getPropertyTypeKeys()));

        return $propertyDefinition;
    }

    /**
     * Cast data values to the expected type
     *
     * @param array $data
     * @return array
     */
    protected function preparePropertyDefinitionData(array $data)
    {
        $data[JSONConstants::JSON_PROPERTY_TYPE_PROPERTY_TYPE] = PropertyType::cast(
            $data[JSONConstants::JSON_PROPERTY_TYPE_PROPERTY_TYPE]
        );

        if (isset($data[JSONConstants::JSON_PROPERTY_TYPE_DEAULT_VALUE])) {
            $data[JSONConstants::JSON_PROPERTY_TYPE_DEAULT_VALUE]
                = (array) $data[JSONConstants::JSON_PROPERTY_TYPE_DEAULT_VALUE];
        }

        if (isset($data[JSONConstants::JSON_PROPERTY_TYPE_RESOLUTION])) {
            $data[JSONConstants::JSON_PROPERTY_TYPE_RESOLUTION] = DateTimeResolution::cast(
                $data[JSONConstants::JSON_PROPERTY_TYPE_RESOLUTION]
            );
        }

        if (isset($data[JSONConstants::JSON_PROPERTY_TYPE_PRECISION])) {
            $data[JSONConstants::JSON_PROPERTY_TYPE_PRECISION] = DecimalPrecision::cast(
                $data[JSONConstants::JSON_PROPERTY_TYPE_PRECISION]
            );
        }

        if (isset($data[JSONConstants::JSON_PROPERTY_TYPE_CARDINALITY])) {
            $data[JSONConstants::JSON_PROPERTY_TYPE_CARDINALITY] = Cardinality::cast(
                $data[JSONConstants::JSON_PROPERTY_TYPE_CARDINALITY]
            );
        }

        if (isset($data[JSONConstants::JSON_PROPERTY_TYPE_UPDATABILITY])) {
            $data[JSONConstants::JSON_PROPERTY_TYPE_UPDATABILITY] = Updatability::cast(
                $data[JSONConstants::JSON_PROPERTY_TYPE_UPDATABILITY]
            );
        }

        return $data;
    }

    /**
     * @param PropertyType $propertyType
     * @param array $data
     * @return PropertyBooleanDefinition|PropertyDateTimeDefinition|PropertyDecimalDefinition|PropertyHtmlDefinition|PropertyIdDefinition|PropertyIntegerDefinition|PropertyStringDefinition
     */
    protected function getPropertyDefinitionByType(PropertyType $propertyType, array $data = [])
    {
        $id = null;
        if (!empty($data[JSONConstants::JSON_PROPERTY_TYPE_ID])) {
            $id = $data[JSONConstants::JSON_PROPERTY_TYPE_ID];
        }

        if ($propertyType->equals(PropertyType::STRING)) {
            $propertyDefinition = new PropertyStringDefinition($id);
        } elseif ($propertyType->equals(PropertyType::ID)) {
            $propertyDefinition = new PropertyIdDefinition($id);
        } elseif ($propertyType->equals(PropertyType::BOOLEAN)) {
            $propertyDefinition = new PropertyBooleanDefinition($id);
        } elseif ($propertyType->equals(PropertyType::INTEGER)) {
            $propertyDefinition = new PropertyIntegerDefinition($id);
        } elseif ($propertyType->equals(PropertyType::DATETIME)) {
            $propertyDefinition = new PropertyDateTimeDefinition($id);
        } elseif ($propertyType->equals(PropertyType::DECIMAL)) {
            $propertyDefinition = new PropertyDecimalDefinition($id);
        } elseif ($propertyType->equals(PropertyType::HTML)) {
            $propertyDefinition = new PropertyHtmlDefinition($id);
        } elseif ($propertyType->equals(PropertyType::URI)) {
            $propertyDefinition = new PropertyUriDefinition($id);
        } else {
            // @codeCoverageIgnoreStart
            // this could only happen if a new property type is added to the enumeration and not implemented here.
            throw new CmisInvalidArgumentException(
                sprintf('The given property definition "%s" could not be converted.', $propertyType)
            );
            // @codeCoverageIgnoreEnd
        }

        $propertyDefinition->setPropertyType($propertyType);

        return $propertyDefinition;
    }

    /**
     * Converts an object.
     *
     * @param array|null $data
     * @return null|ObjectData
     */
    public function convertObject(array $data = null)
    {
        if (empty($data)) {
            return null;
        }

        $object = new ObjectData();
        $acl = $this->convertAcl(
            $data[JSONConstants::JSON_OBJECT_ACL] ?? [],
            (boolean) $data[JSONConstants::JSON_OBJECT_EXACT_ACL] ?? false
        );
        if ($acl !== null) {
            $object->setAcl($acl);
        }

        $allowableActions = $this->convertAllowableActions($data[JSONConstants::JSON_OBJECT_ALLOWABLE_ACTIONS] ?? []);
        if ($allowableActions !== null) {
            $object->setAllowableActions($allowableActions);
        }

        if (isset($data[JSONConstants::JSON_OBJECT_CHANGE_EVENT_INFO])
            && is_array($data[JSONConstants::JSON_OBJECT_CHANGE_EVENT_INFO])
        ) {
            $changeEventInfoData = $data[JSONConstants::JSON_OBJECT_CHANGE_EVENT_INFO];
            $changeEventInfo = new ChangeEventInfo();
            $changeEventInfo->setChangeTime(
                $this->convertDateTimeValue($changeEventInfoData[JSONConstants::JSON_CHANGE_EVENT_TIME])
            );
            $changeEventInfo->setChangeType(
                ChangeType::cast($changeEventInfoData[JSONConstants::JSON_CHANGE_EVENT_TYPE])
            );

            $changeEventInfo->setExtensions(
                $this->convertExtension($changeEventInfoData, JSONConstants::getChangeEventKeys())
            );

            $object->setChangeEventInfo($changeEventInfo);
        }

        $object->setIsExactAcl((boolean) $data[JSONConstants::JSON_OBJECT_EXACT_ACL] ?? false);
        $object->setPolicyIds($this->convertPolicyIdList($data[JSONConstants::JSON_OBJECT_POLICY_IDS] ?? null));

        /**
         * A client MAY add the query parameter succinct (HTTP GET) or the control succinct (HTTP POST) with the
         * value <code>true</code> to a request. If this is set, the repository MUST return properties in a succinct
         * format. That is, whenever the repository renders an object or a query result, it MUST populate the
         * succinctProperties value and MUST NOT populate the properties value.
         *
         * @see http://docs.oasis-open.org/cmis/CMIS/v1.1/os/CMIS-v1.1-os.html#x1-552027r554
         */
        if (isset($data[JSONConstants::JSON_OBJECT_SUCCINCT_PROPERTIES])) {
            $properties = $this->convertSuccinctProperties(
                $data[JSONConstants::JSON_OBJECT_SUCCINCT_PROPERTIES] ?? [],
                $data[JSONConstants::JSON_OBJECT_PROPERTIES_EXTENSION] ?? []);
        } else {
            $properties = $this->convertProperties(
                $data[JSONConstants::JSON_OBJECT_PROPERTIES] ?? [],
                (array) $data[JSONConstants::JSON_OBJECT_PROPERTIES_EXTENSION] ?? []
            );
        }

        $object->setProperties($properties ?? []);
        $object->setRelationships($this->convertObjects($data[JSONConstants::JSON_OBJECT_RELATIONSHIPS] ?? null));
        $object->setRenditions($this->convertRenditions($data[JSONConstants::JSON_OBJECT_RENDITIONS] ?? []));
        $object->setExtensions($this->convertExtension($data, JSONConstants::getObjectKeys()));

        return $object;
    }

    /**
     * @param array|null $data
     * @return ObjectDataInterface[]
     */
    public function convertObjects(array $data = null)
    {
        return array_filter(
            array_map(
                [$this, 'convertObject'],
                array_filter(
                    (array) $data,
                    'is_array'
                )
            ),
            function ($item) {
                // @TODO once a logger is available we should log an INFO message if the object could not be converted
                return !empty($item);
            }
        );
    }

    /**
     * @param array|null $data
     * @param array $extensions
     * @return null|Properties
     * @throws CmisRuntimeException
     */
    public function convertProperties(array $data = null, $extensions = [])
    {
        if (empty($data)) {
            return null;
        }
        $properties = new Properties();

        foreach ($data as $propertyData) {
            $id = $propertyData[JSONConstants::JSON_PROPERTY_ID] ?? null;
            $queryName = $propertyData[JSONConstants::JSON_PROPERTY_QUERYNAME] ?? null;

            // A Property must always have an ID except if it used in a query result.
            // In a query result a Property should have an ID and must have a query name.
            if ($id === null && $queryName === null) {
                throw new CmisRuntimeException('Invalid property! Neither a property ID nor a query name is provided!');
            }

            try {
                $propertyType = PropertyType::cast($propertyData[JSONConstants::JSON_PROPERTY_DATATYPE]);
            } catch (InvalidEnumerationValueException $exception) {
                throw new CmisRuntimeException(
                    sprintf('Unknown property type "%s"!', $propertyData[JSONConstants::JSON_PROPERTY_DATATYPE])
                );
            }

            // get property keys without JSON-response-specific cardinality properties
            $jsonPropertyKeys = JSONConstants::getPropertyKeys();
            $propertyKeys = array_values(
                array_diff(
                    $jsonPropertyKeys,
                    [
                        // remove the cardinality property here as this is not a property of a property but only
                        // required for the other way when converting the property to the JSON object for the
                        // browser binding.
                        JSONConstants::JSON_PROPERTY_CARDINALITY,
                        JSONConstants::JSON_PROPERTY_VALUE,
                        JSONConstants::JSON_PROPERTY_ID,
                        JSONConstants::JSON_PROPERTY_DATATYPE
                    ]
                )
            );

            $property = $this->getPropertyByPropertyType(
                $propertyType,
                $id,
                (array) $propertyData[JSONConstants::JSON_PROPERTY_VALUE] ?? []
            );
            $property->populate(
                $propertyData,
                $propertyKeys,
                true
            );
            $property->setExtensions($this->convertExtension($propertyData, $jsonPropertyKeys));

            $properties->addProperty($property);
        }

        if (!empty($extensions)) {
            $properties->setExtensions($this->convertExtension($extensions));
        }

        return $properties;
    }

    /**
     * @param PropertyType $propertyType
     * @param string $id
     * @param array $propertyValues
     * @return PropertyBoolean|PropertyDateTime|PropertyDecimal|PropertyHtml|PropertyId|PropertyInteger|PropertyString
     */
    protected function getPropertyByPropertyType(PropertyType $propertyType, $id, array $propertyValues)
    {
        if ($propertyType->equals(PropertyType::cast(PropertyType::STRING))) {
            $property = new PropertyString($id, $this->convertStringValues($propertyValues));
        } elseif ($propertyType->equals(PropertyType::cast(PropertyType::ID))) {
            $property = new PropertyId($id, $this->convertStringValues($propertyValues));
        } elseif ($propertyType->equals(PropertyType::cast(PropertyType::BOOLEAN))) {
            $property = new PropertyBoolean($id, $this->convertBooleanValues($propertyValues));
        } elseif ($propertyType->equals(PropertyType::cast(PropertyType::INTEGER))) {
            $property = new PropertyInteger($id, $this->convertIntegerValues($propertyValues));
        } elseif ($propertyType->equals(PropertyType::cast(PropertyType::DATETIME))) {
            $property = new PropertyDateTime($id, $this->convertDateTimeValues($propertyValues));
        } elseif ($propertyType->equals(PropertyType::cast(PropertyType::DECIMAL))) {
            $property = new PropertyDecimal($id, $this->convertDecimalValues($propertyValues));
        } elseif ($propertyType->equals(PropertyType::cast(PropertyType::HTML))) {
            $property = new PropertyHtml($id, $this->convertStringValues($propertyValues));
        } elseif ($propertyType->equals(PropertyType::cast(PropertyType::URI))) {
            $property = new PropertyUri($id, $this->convertStringValues($propertyValues));
        } else {
            // this could only happen if a new property type is added to the enumeration and not implemented here.
            throw new CmisInvalidArgumentException(
                sprintf('The given property type "%s" could not be converted.', $propertyType)
            );
        }

        return $property;
    }

    /**
     * TODO Add description
     *
     * @param array|null $data
     * @param array $extensions
     * @return PropertiesInterface
     * @throws \Exception
     */
    public function convertSuccinctProperties(array $data = null, $extensions = [])
    {
        throw new \Exception('Succinct properties are currently not supported.');
// TODO IMPLEMENT SUCCINCT PROPERTY SUPPORT
//        if (empty($data)) {
//            return null;
//        }
//        if (isset($data[PropertyIds::SECONDARY_OBJECT_TYPE_IDS])
//            && is_array($data[PropertyIds::SECONDARY_OBJECT_TYPE_IDS])
//        ) {
//            $secondaryTypeIds = $data[PropertyIds::SECONDARY_OBJECT_TYPE_IDS];
//            $secondaryTypeDefinitions = array();
//            foreach($secondaryTypeIds as $secondaryTypeId) {
//                if ((string) $secondaryTypeId !== "") {
//                    $secondaryTypeDefinitions[] =
//                }
//            }
//        }
//
//        $properties = array();
//
//        foreach ($data as $propertyId => $propertyData) {
//          ...
//        }
//
//        return $properties;
    }

    /**
     * Convert given input data to a RenditionData object
     *
     * @param array|null $data
     * @return null|RenditionData
     */
    public function convertRendition(array $data = null)
    {
        if (empty($data)) {
            return null;
        }
        $rendition = new RenditionData();

        if (isset($data[JSONConstants::JSON_RENDITION_HEIGHT])) {
            $rendition->setHeight((integer) $data[JSONConstants::JSON_RENDITION_HEIGHT]);
        }
        if (isset($data[JSONConstants::JSON_RENDITION_KIND])) {
            $rendition->setKind((string) $data[JSONConstants::JSON_RENDITION_KIND]);
        }
        if (isset($data[JSONConstants::JSON_RENDITION_LENGTH])) {
            $rendition->setLength((integer) $data[JSONConstants::JSON_RENDITION_LENGTH]);
        }
        if (isset($data[JSONConstants::JSON_RENDITION_MIMETYPE])) {
            $rendition->setMimeType((string) $data[JSONConstants::JSON_RENDITION_MIMETYPE]);
        }
        if (isset($data[JSONConstants::JSON_RENDITION_DOCUMENT_ID])) {
            $rendition->setRenditionDocumentId((string) $data[JSONConstants::JSON_RENDITION_DOCUMENT_ID]);
        }
        if (isset($data[JSONConstants::JSON_RENDITION_STREAM_ID])) {
            $rendition->setStreamId((string) $data[JSONConstants::JSON_RENDITION_STREAM_ID]);
        }
        if (isset($data[JSONConstants::JSON_RENDITION_TITLE])) {
            $rendition->setTitle((string) $data[JSONConstants::JSON_RENDITION_TITLE]);
        }
        if (isset($data[JSONConstants::JSON_RENDITION_WIDTH])) {
            $rendition->setWidth((integer) $data[JSONConstants::JSON_RENDITION_WIDTH]);
        }

        $rendition->setExtensions($this->convertExtension($data, JSONConstants::getRenditionKeys()));

        return $rendition;
    }

    /**
     * Convert given input data to a list of RenditionData objects
     *
     * @param array|null $data
     * @return RenditionData[]
     */
    public function convertRenditions(array $data = null)
    {
        return array_filter(
            array_map(
                [$this, 'convertRendition'],
                array_filter(
                    $data,
                    'is_array'
                )
            ),
            function ($item) {
                // @TODO once a logger is available we should log an INFO message if the rendition could not be converted
                return !empty($item);
            }
        );
    }

    /**
     * Convert given input data to an Extension object
     *
     * @param array|null $data
     * @param string[] $cmisKeys
     * @return CmisExtensionElement[]
     */
    public function convertExtension(array $data = null, array $cmisKeys = [])
    {
        $extensions = [];

        foreach (array_diff_key((array) $data, array_flip($cmisKeys)) as $key => $value) {

            if (!is_array($value)) {
                $value = (empty($value)) ? null : (string) $value;
                $extensions[] = new CmisExtensionElement(null, $key, [], $value);
            } else {
                $extension = $this->convertExtension($value, $cmisKeys);

                if (!empty($extension)) {
                    $extensions[] = new CmisExtensionElement(
                        null,
                        $key,
                        [],
                        null,
                        $extension
                    );
                }
            }
        }

        return $extensions;
    }

    /**
     * Convert given input data to an ExtensionFeature object
     *
     * @param array|null $data
     * @return ExtensionFeature[]
     */
    public function convertExtensionFeatures(array $data = null)
    {
        $features = [];
        $extendedFeatures = array_filter(array_filter((array) $data, 'is_array'), function ($item) { return !empty($item); });

        foreach ($extendedFeatures as $extendedFeature) {

            $feature = new ExtensionFeature();
            $feature->setId((string) $extendedFeature[JSONConstants::JSON_FEATURE_ID]);
            $feature->setUrl((string) $extendedFeature[JSONConstants::JSON_FEATURE_URL]);
            $feature->setCommonName((string) $extendedFeature[JSONConstants::JSON_FEATURE_COMMON_NAME]);
            $feature->setVersionLabel((string) $extendedFeature[JSONConstants::JSON_FEATURE_VERSION_LABEL]);
            $feature->setDescription((string) $extendedFeature[JSONConstants::JSON_FEATURE_DESCRIPTION]);

            $featureData = [];
            foreach ($extendedFeature[JSONConstants::JSON_FEATURE_DATA] ?? [] as $key => $value) {
                $featureData[$key] = $value;
            }

            $feature->setFeatureData($featureData);

            $feature->setExtensions($this->convertExtension($extendedFeature, JSONConstants::getFeatureKeys()));

            $features[] = $feature;
        }

        return $features;
    }

    /**
     * Converts a list of policy ids.
     *
     * @param array|null $data
     * @return PolicyIdList List of policy ids
     */
    public function convertPolicyIdList(array $data = null)
    {
        $policyIdsList = new PolicyIdList();
        $policyIdsList->setPolicyIds(
            array_filter(
                array_filter(
                    $data[JSONConstants::JSON_OBJECT_POLICY_IDS_IDS] ?? [],
                    'is_string'
                ),
                function ($item) { return !empty($item); }
            )
        );
        $policyIdsList->setExtensions($this->convertExtension($data, JSONConstants::getPolicyIdsKeys()));

        return $policyIdsList;
    }

    /**
     * Convert an acl object to a custom format
     *
     * @param AclInterface $acl
     * @return mixed
     */
    public function convertFromAcl(AclInterface $acl)
    {
        // TODO: Implement convertFromAcl() method.
    }

    /**
     * Convert an acl capabilities object to a custom format
     *
     * @param AclCapabilitiesInterface $aclCapabilities
     * @return mixed
     */
    public function convertFromAclCapabilities(AclCapabilitiesInterface $aclCapabilities)
    {
        // TODO: Implement convertFromAclCapabilities() method.
    }

    /**
     * Convert an allowable actions object to a custom format
     *
     * @param AllowableActionsInterface $allowableActions
     * @return mixed
     */
    public function convertFromAllowableActions(AllowableActionsInterface $allowableActions)
    {
        // TODO: Implement convertFromAllowableActions() method.
    }

    /**
     * Convert a repository info object to a custom format
     *
     * @param RepositoryInfoInterface $repositoryInfo
     * @return mixed
     */
    public function convertFromRepositoryInfo(RepositoryInfoInterface $repositoryInfo)
    {
        // TODO: Implement convertFromRepositoryInfo() method.
    }

    /**
     * Convert a repository capabilities object to a custom format
     *
     * @param RepositoryCapabilitiesInterface $repositoryCapabilities
     * @return mixed
     */
    public function convertFromRepositoryCapabilities(RepositoryCapabilitiesInterface $repositoryCapabilities)
    {
        // TODO: Implement convertFromRepositoryCapabilities() method.
    }

    /**
     * Convert a rendition data object to a custom format
     *
     * @param RenditionDataInterface $rendition
     * @return mixed
     */
    public function convertFromRenditionData(RenditionDataInterface $rendition)
    {
        // TODO: Implement convertFromRenditionData() method.
    }

    /**
     * Convert a object data object to a custom format
     *
     * @param ObjectDataInterface $objectData
     * @return mixed
     */
    public function convertFromObjectData(ObjectDataInterface $objectData)
    {
        // TODO: Implement convertFromObjectData() method.
    }

    /**
     * Convert a properties object to a custom format
     *
     * @param PropertiesInterface $properties
     * @return mixed
     */
    public function convertFromProperties(PropertiesInterface $properties)
    {
        // TODO: Implement convertFromProperties() method.
    }

    /**
     * Convert a property data object to a custom format
     *
     * @param PropertyDataInterface $propertyData
     * @return mixed
     */
    public function convertFromPropertyData(PropertyDataInterface $propertyData)
    {
        // TODO: Implement convertFromPropertyData() method.
    }

    /**
     * Convert a type definition object to a custom format
     *
     * @param TypeDefinitionInterface $typeDefinition
     * @return string JSON representation of the type definition
     */
    public function convertFromTypeDefinition(TypeDefinitionInterface $typeDefinition)
    {
        $propertyList = [
            'baseTypeId' => JSONConstants::JSON_TYPE_BASE_ID,
            'parentTypeId' => JSONConstants::JSON_TYPE_PARENT_ID
        ];

        if ($typeDefinition instanceof RelationshipTypeDefinitionInterface) {
            $propertyList['allowedTargetTypeIds'] = JSONConstants::JSON_TYPE_ALLOWED_TARGET_TYPES;
            $propertyList['allowedSourceTypeIds'] = JSONConstants::JSON_TYPE_ALLOWED_SOURCE_TYPES;
        }

        $data = $typeDefinition->exportGettableProperties(
            $propertyList
        );
        $data = $this->castArrayValuesToSimpleTypes($data);

        return json_encode($data, JSON_FORCE_OBJECT);
    }

    /**
     * Cast values of an array to simple types
     *
     * @param array $data
     * @return array
     */
    protected function castArrayValuesToSimpleTypes(array $data)
    {
        foreach ($data as $key => $item) {
            if (is_array($item)) {
                $data[$key] = $this->castArrayValuesToSimpleTypes($item);
            } elseif (is_object($item)) {
                $data[$key] = $this->convertObjectToSimpleType($item);
            }
        }

        return $data;
    }

    /**
     * Convert an object to a simple type representation
     *
     * @param mixed $data
     * @return mixed
     * @throws CmisRuntimeException Exception is thrown if no type converter could be found to convert the given data
     *      to a simple type
     */
    protected function convertObjectToSimpleType($data)
    {
        /** @var null|TypeConverterInterface $converterClassName */
        $converterClassName = null;
        if (class_exists($this->buildConverterClassName(get_class($data)))) {
            $converterClassName = $this->buildConverterClassName(get_class($data));
        } else {
            $classInterfaces = class_implements($data);
            foreach ((array) $classInterfaces as $classInterface) {
                if (class_exists($this->buildConverterClassName($classInterface))) {
                    $converterClassName = $this->buildConverterClassName($classInterface);
                    break;
                }
            }
            if ($converterClassName === null) {
                $classParents = class_parents($data);
                foreach ((array) $classParents as $classParent) {
                    if (class_exists($this->buildConverterClassName($classParent))) {
                        $converterClassName = $this->buildConverterClassName($classParent);
                        break;
                    }
                }
            }
        }

        if ($converterClassName === null) {
            throw new CmisRuntimeException(
                'Could not find a converter that converts "' . get_class($data) . '" to a simple type.'
            );
        }

        return $converterClassName::convertToSimpleType($data);
    }

    /**
     * Build a converter class name with namespace.
     *
     * The Dkd\PhpCmis namespace will be stripped.
     *
     * @param $className
     * @return string
     */
    protected function buildConverterClassName($className)
    {
        $converterClassName =  '\\Dkd\\PhpCmis\\Converter\\Types\\';
        $converterClassName .= str_replace('Dkd\\PhpCmis\\', '', $className);
        $converterClassName .= 'Converter';
        return $converterClassName;
    }

    /**
     * Convert a property definition object to a custom format
     *
     * @param PropertyDefinitionInterface $propertyDefinition
     * @return mixed
     */
    public function convertFromPropertyDefinition(PropertyDefinitionInterface $propertyDefinition)
    {
        // TODO: Implement convertFromPropertyDefinition() method.
    }

    /**
     * Convert a type definition list object to a custom format
     *
     * @param TypeDefinitionListInterface $typeDefinitionList
     * @return mixed
     */
    public function convertFromTypeDefinitionList(TypeDefinitionListInterface $typeDefinitionList)
    {
        // TODO: Implement convertFromTypeDefinitionList() method.
    }

    /**
     * Convert a type definition container object to a custom format
     *
     * @param TypeDefinitionContainerInterface $typeDefinitionContainer
     * @return mixed
     */
    public function convertFromTypeDefinitionContainer(TypeDefinitionContainerInterface $typeDefinitionContainer)
    {
        // TODO: Implement convertFromTypeDefinitionContainer() method.
    }

    /**
     * Convert a object list object to a custom format
     *
     * @param ObjectListInterface $list
     * @return mixed
     */
    public function convertFromObjectList(ObjectListInterface $list)
    {
        // TODO: Implement convertFromObjectList() method.
    }

    /**
     * Convert a object in folder data object to a custom format
     *
     * @param ObjectInFolderDataInterface $objectInFolder
     * @return mixed
     */
    public function convertFromObjectInFolderData(ObjectInFolderDataInterface $objectInFolder)
    {
        // TODO: Implement convertFromObjectInFolderData() method.
    }

    /**
     * Convert a object in folder list object to a custom format
     *
     * @param ObjectInFolderListInterface $objectInFolder
     * @return mixed
     */
    public function convertFromObjectInFolderList(ObjectInFolderListInterface $objectInFolder)
    {
        // TODO: Implement convertFromObjectInFolderList() method.
    }

    /**
     * Convert a object in folder container object to a custom format
     *
     * @param ObjectInFolderContainerInterface $container
     * @return mixed
     */
    public function convertFromObjectInFolderContainer(ObjectInFolderContainerInterface $container)
    {
        // TODO: Implement convertFromObjectInFolderContainer() method.
    }

    /**
     * Convert a object in parent data object to a custom format
     *
     * @param ObjectParentDataInterface $container
     * @return mixed
     */
    public function convertFromObjectParentData(ObjectParentDataInterface $container)
    {
        // TODO: Implement convertFromObjectParentData() method.
    }

    /**
     * Convert an extension feature object to a custom format
     *
     * @param ExtensionFeatureInterface $extensionFeature
     * @return mixed
     */
    public function convertFromExtensionFeature(ExtensionFeatureInterface $extensionFeature)
    {
        // TODO: Implement convertFromExtensionFeature() method.
    }

    /**
     * Convert given input data to a TypeChildren object
     *
     * @param array|null $data
     * @return TypeDefinitionListInterface|null Returns a TypeDefinitionListInterface object or <code>null</code>
     *      if empty data is given.
     */
    public function convertTypeChildren(array $data = null)
    {
        if (empty($data)) {
            return null;
        }

        $result = new TypeDefinitionList();
        $types = [];

        $typesList = $data[JSONConstants::JSON_TYPESLIST_TYPES] ?? [];

        foreach (array_filter($typesList, 'is_array') as $typeData) {
            $type = $this->convertTypeDefinition($typeData);
            if ($type !== null) {
                $types[] = $type;
            }
        }

        $result->setList($types);
        $result->setHasMoreItems($data[JSONConstants::JSON_TYPESLIST_HAS_MORE_ITEMS] ?? false);
        $result->setNumItems($data[JSONConstants::JSON_TYPESLIST_NUM_ITEMS] ?? 0);
        $result->setExtensions($this->convertExtension($data, JSONConstants::getTypesListKeys()));

        return $result;
    }

    /**
     * Convert given input data to a TypeDescendants object
     *
     * @param array|null $data
     * @return TypeDefinitionContainerInterface[] Returns an array of TypeDefinitionContainerInterface objects
     */
    public function convertTypeDescendants(array $data = null)
    {
        $result = [];

        if (empty($data)) {
            return $result;
        }

        foreach (array_filter($data, 'is_array') as $itemData) {

            $container = new TypeDefinitionContainer();

            $typeDefinition = $this->convertTypeDefinition($itemData[JSONConstants::JSON_TYPESCONTAINER_TYPE] ?? []);
            if ($typeDefinition !== null) {
                $container->setTypeDefinition($typeDefinition);
            }

            $container->setChildren(
                $this->convertTypeDescendants($itemData[JSONConstants::JSON_TYPESCONTAINER_CHILDREN] ?? [])
            );

            $container->setExtensions($this->convertExtension($data, JSONConstants::getTypesContainerKeys()));

            $result[] = $container;
        }

        return $result;
    }

    /**
     * Convert given input data to a ObjectInFolderList object
     *
     * @param array|null $data
     * @return null|ObjectInFolderList
     */
    public function convertObjectInFolderList(array $data = null)
    {
        if (empty($data)) {
            return null;
        }

        $objectInFolderList = new ObjectInFolderList();
        $objectInFolderList->setObjects(
            array_filter(
                array_map(
                    [$this, 'convertObjectInFolder'],
                    $data[JSONConstants::JSON_OBJECTINFOLDERLIST_OBJECTS] ?? []
                ),
                function ($item) { return !empty($item); }
            )
        );
        $objectInFolderList->setHasMoreItems((boolean) $data[JSONConstants::JSON_OBJECTINFOLDERLIST_HAS_MORE_ITEMS] ?? false);
        $objectInFolderList->setNumItems((integer) $data[JSONConstants::JSON_OBJECTINFOLDERLIST_NUM_ITEMS] ?? count($objects));
        $objectInFolderList->setExtensions($this->convertExtension($data, JSONConstants::getObjectInFolderListKeys()));

        return $objectInFolderList;
    }

    /**
     * Convert given input data to a ObjectInFolderData object
     *
     * @param array|null $data
     * @return ObjectInFolderData|null
     */
    public function convertObjectInFolder(array $data = null)
    {
        if (empty($data)) {
            return null;
        }

        $objectInFolderData = new ObjectInFolderData();
        $object = $this->convertObject($data[JSONConstants::JSON_OBJECTINFOLDER_OBJECT] ?? []);

        if ($object !== null) {
            $objectInFolderData->setObject($object);
        }

        $objectInFolderData->setPathSegment((string) $data[JSONConstants::JSON_OBJECTINFOLDER_PATH_SEGMENT] ?? null);
        $objectInFolderData->setExtensions($this->convertExtension($data, JSONConstants::getObjectInFolderKeys()));

        return $objectInFolderData;
    }

    /**
     * Convert given input data to a list of ObjectParentData objects
     *
     * @param array|null $data
     * @return ObjectParentData[]
     */
    public function convertObjectParents(array $data = null)
    {
        return array_filter(
            array_map(
                [$this, 'convertObjectParentData'],
                (array) ($data ?? [])
            ),
            function ($item) {
                return !empty($item);
                // @TODO once a logger is available we should log an INFO message if the parent data could not be converted
            }
        );
    }

    /**
     * Convert given input data to a ObjectParentData object
     *
     * @param array|null $data
     * @return null|ObjectParentData
     */
    public function convertObjectParentData(array $data = null)
    {
        if (empty($data)) {
            return null;
        }
        $parent = new ObjectParentData();

        $object = $this->convertObject($data[JSONConstants::JSON_OBJECTPARENTS_OBJECT] ?? null);
        if ($object !== null) {
            $parent->setObject($object);
        }

        $parent->setRelativePathSegment((string) ($data[JSONConstants::JSON_OBJECTPARENTS_RELATIVE_PATH_SEGMENT] ?? ''));
        $parent->setExtensions($this->convertExtension($data, JSONConstants::getObjectParentsKeys()));

        return $parent;
    }

    /**
     * Convert given input data array to a ObjectList object
     *
     * @param array|null $data
     * @return null|ObjectList
     */
    public function convertObjectList(array $data = null)
    {
        if (empty($data)) {
            return null;
        }

        $objectList = new ObjectList();
        $objects = [];

        foreach ((array) $data[JSONConstants::JSON_OBJECTLIST_OBJECTS] ?? [] as $objectData) {
            $object = $this->convertObject($objectData);

            if ($object !== null) {
                $objects[] = $object;
            }
        }

        $objectList->setObjects($objects);

        $objectList->setHasMoreItems(
            (boolean) ($data[JSONConstants::JSON_OBJECTLIST_HAS_MORE_ITEMS] ?? false)
        );

        if (isset($data[JSONConstants::JSON_OBJECTLIST_NUM_ITEMS])) {
            $objectList->setNumItems((integer) $data[JSONConstants::JSON_OBJECTLIST_NUM_ITEMS]);
        }

        $objectList->setExtensions($this->convertExtension($data, JSONConstants::getObjectListKeys()));

        return $objectList;
    }

    /**
     * Convert given input data array from query result to a ObjectList object
     *
     * @param array|null $data
     * @return null|ObjectList
     */
    public function convertQueryResultList(array $data = null)
    {
        if (empty($data)) {
            return null;
        }

        $objectList = new ObjectList();
        $objects = [];

        foreach ((array) ($data[JSONConstants::JSON_QUERYRESULTLIST_RESULTS]) ?? [] as $objectData) {
            $object = $this->convertObject($objectData);

            if ($object !== null) {
                $objects[] = $object;
            }
        }

        $objectList->setObjects($objects);
        $objectList->setHasMoreItems((boolean) ($data[JSONConstants::JSON_QUERYRESULTLIST_HAS_MORE_ITEMS] ?? false));

        if (isset($data[JSONConstants::JSON_QUERYRESULTLIST_NUM_ITEMS])) {
            $objectList->setNumItems((integer) $data[JSONConstants::JSON_QUERYRESULTLIST_NUM_ITEMS]);
        }

        $objectList->setExtensions($this->convertExtension($data, JSONConstants::getQueryResultListKeys()));

        return $objectList;
    }

    /**
     * Convert given input data array to a ObjectList object
     *
     * @param array|null $data
     * @return ObjectInFolderContainer[]
     */
    public function convertDescendants(array $data = null)
    {
        return array_filter(
            array_map(
                [$this, 'convertDescendant'],
                $data ?? []
            ),
            function ($item) { return !empty($item); }
        );
    }

    /**
     * Convert given input data array to a ObjectInFolderContainer object
     *
     * @param array|null $data
     * @return null|ObjectInFolderContainer
     * @throws CmisRuntimeException
     */
    public function convertDescendant(array $data = null)
    {
        if (empty($data)) {
            return null;
        }

        $object = $this->convertObjectInFolder($data[JSONConstants::JSON_OBJECTINFOLDERCONTAINER_OBJECT] ?? null);

        if ($object === null) {
            throw new CmisRuntimeException('Given data could not be converted to ObjectInFolder!');
        }

        $objectInFolderContainer = new ObjectInFolderContainer($object);

        $objectInFolderContainer->setChildren(
            array_filter(
                array_map(
                    [$this, 'convertDescendant'],
                    (array) ($data[JSONConstants::JSON_OBJECTINFOLDERCONTAINER_CHILDREN] ?? [])
                ),
                function ($item) { return !empty($item); }
            )
        );

        $objectInFolderContainer->setExtensions(
            $this->convertExtension($data, JSONConstants::getObjectInFolderContainerKeys())
        );

        return $objectInFolderContainer;
    }

    /**
     * Converts FailedToDelete ids.
     *
     * @param array|null $data
     * @return FailedToDeleteData
     */
    public function convertFailedToDelete(array $data = null)
    {
        $result = new FailedToDeleteData();

        if (empty($data)) {
            return $result;
        }

        $result->setIds(array_map('strval', $data[JSONConstants::JSON_FAILEDTODELETE_ID] ?? []));
        $result->setExtensions($this->convertExtension($data, JSONConstants::getFailedToDeleteKeys()));

        return $result;
    }

    /**
     * @return BindingsObjectFactory
     * @codeCoverageIgnore
     */
    protected function getBindingsObjectFactory()
    {
        return new BindingsObjectFactory();
    }
}
