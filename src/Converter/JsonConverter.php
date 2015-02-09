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

use Dkd\Enumeration\Exception\InvalidEnumerationValueException;
use Dkd\PhpCmis\Bindings\Browser\JSONConstants;
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
use Dkd\PhpCmis\DataObjects\AccessControlEntry;
use Dkd\PhpCmis\DataObjects\AccessControlList;
use Dkd\PhpCmis\DataObjects\AclCapabilities;
use Dkd\PhpCmis\DataObjects\AllowableActions;
use Dkd\PhpCmis\DataObjects\ChangeEventInfo;
use Dkd\PhpCmis\DataObjects\CmisExtensionElement;
use Dkd\PhpCmis\DataObjects\CreatablePropertyTypes;
use Dkd\PhpCmis\DataObjects\DocumentTypeDefinition;
use Dkd\PhpCmis\DataObjects\ExtensionFeature;
use Dkd\PhpCmis\DataObjects\FolderTypeDefinition;
use Dkd\PhpCmis\DataObjects\ItemTypeDefinition;
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
use Dkd\PhpCmis\DataObjects\PolicyTypeDefinition;
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
use Dkd\PhpCmis\DataObjects\RelationshipTypeDefinition;
use Dkd\PhpCmis\DataObjects\RenditionData;
use Dkd\PhpCmis\DataObjects\RepositoryCapabilities;
use Dkd\PhpCmis\DataObjects\RepositoryInfoBrowserBinding;
use Dkd\PhpCmis\DataObjects\SecondaryTypeDefinition;
use Dkd\PhpCmis\DataObjects\TypeMutability;
use Dkd\PhpCmis\Definitions\PropertyDefinitionInterface;
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

/**
 * Convert PHP CMIS Objects to JSON and JSON Responses TO PHP CMIS Objects
 *
 * @TODO: To reduce the complexity of this class there should be some kind of schema mapping in the future.
 */
class JsonConverter extends AbstractDataConverter
{
    /**
     * @param array $data
     * @return AllowableActions|null
     */
    public function convertAllowableActions(array $data = null)
    {
        if (empty($data)) {
            return null;
        }

        $allowableActions = new AllowableActions();
        $actions = array();
        $extensions = array();

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
     * @param array $data The JSON that contains the repository info
     * @return RepositoryInfoInterface|null
     */
    public function convertRepositoryInfo(array $data = null)
    {
        if (empty($data)) {
            return null;
        }

        return $this->setRepositoryInfoValues(new RepositoryInfoBrowserBinding(), $data);
    }

    /**
     * @param RepositoryInfoBrowserBinding $object
     * @param array $data
     * @return RepositoryInfoInterface
     */
    protected function setRepositoryInfoValues(RepositoryInfoBrowserBinding $object, $data)
    {
        if (isset($data[JSONConstants::JSON_REPINFO_ID])) {
            $object->setId((string) $data[JSONConstants::JSON_REPINFO_ID]);
        }
        if (isset($data[JSONConstants::JSON_REPINFO_NAME])) {
            $object->setName((string) $data[JSONConstants::JSON_REPINFO_NAME]);
        }
        if (isset($data[JSONConstants::JSON_REPINFO_DESCRIPTION])) {
            $object->setDescription((string) $data[JSONConstants::JSON_REPINFO_DESCRIPTION]);
        }
        if (isset($data[JSONConstants::JSON_REPINFO_VENDOR])) {
            $object->setVendorName((string) $data[JSONConstants::JSON_REPINFO_VENDOR]);
        }
        if (isset($data[JSONConstants::JSON_REPINFO_PRODUCT])) {
            $object->setProductName((string) $data[JSONConstants::JSON_REPINFO_PRODUCT]);
        }
        if (isset($data[JSONConstants::JSON_REPINFO_PRODUCT_VERSION])) {
            $object->setProductVersion((string) $data[JSONConstants::JSON_REPINFO_PRODUCT_VERSION]);
        }
        if (isset($data[JSONConstants::JSON_REPINFO_ROOT_FOLDER_ID])) {
            $object->setRootFolderId((string) $data[JSONConstants::JSON_REPINFO_ROOT_FOLDER_ID]);
        }
        if (isset($data[JSONConstants::JSON_REPINFO_REPOSITORY_URL])) {
            $object->setRepositoryUrl((string) $data[JSONConstants::JSON_REPINFO_REPOSITORY_URL]);
        }
        if (isset($data[JSONConstants::JSON_REPINFO_ROOT_FOLDER_URL])) {
            $object->setRootUrl((string) $data[JSONConstants::JSON_REPINFO_ROOT_FOLDER_URL]);
        }
        if (isset($data[JSONConstants::JSON_REPINFO_CAPABILITIES])
            && is_array($data[JSONConstants::JSON_REPINFO_CAPABILITIES])
        ) {
            $repositoryCapabilities = $this->convertRepositoryCapabilities(
                $data[JSONConstants::JSON_REPINFO_CAPABILITIES]
            );
            if ($repositoryCapabilities !== null) {
                $object->setCapabilities(
                    $repositoryCapabilities
                );
            }
        }
        if (isset($data[JSONConstants::JSON_REPINFO_ACL_CAPABILITIES])
            && is_array($data[JSONConstants::JSON_REPINFO_ACL_CAPABILITIES])
        ) {
            $aclCapabilities = $this->convertAclCapabilities($data[JSONConstants::JSON_REPINFO_ACL_CAPABILITIES]);
            if ($aclCapabilities !== null) {
                $object->setAclCapabilities(
                    $aclCapabilities
                );
            }
        }
        if (isset($data[JSONConstants::JSON_REPINFO_CHANGE_LOG_TOKEN])) {
            $object->setLatestChangeLogToken((string) $data[JSONConstants::JSON_REPINFO_CHANGE_LOG_TOKEN]);
        }
        if (isset($data[JSONConstants::JSON_REPINFO_CMIS_VERSION_SUPPORTED])) {
            $object->setCmisVersion(CmisVersion::cast($data[JSONConstants::JSON_REPINFO_CMIS_VERSION_SUPPORTED]));
        }
        if (isset($data[JSONConstants::JSON_REPINFO_THIN_CLIENT_URI])) {
            $object->setThinClientUri((string) $data[JSONConstants::JSON_REPINFO_THIN_CLIENT_URI]);
        }
        if (isset($data[JSONConstants::JSON_REPINFO_CHANGES_INCOMPLETE])) {
            $object->setChangesIncomplete((boolean) $data[JSONConstants::JSON_REPINFO_CHANGES_INCOMPLETE]);
        }

        if (isset($data[JSONConstants::JSON_REPINFO_CHANGES_ON_TYPE])
            && is_array($data[JSONConstants::JSON_REPINFO_CHANGES_ON_TYPE])
        ) {
            $types = array();
            foreach ($data[JSONConstants::JSON_REPINFO_CHANGES_ON_TYPE] as $type) {
                if (!empty($type)) {
                    $types[] = BaseTypeId::cast($type);
                }
            }

            $object->setChangesOnType($types);
        }

        if (isset($data[JSONConstants::JSON_REPINFO_PRINCIPAL_ID_ANONYMOUS])) {
            $object->setPrincipalIdAnonymous((string) $data[JSONConstants::JSON_REPINFO_PRINCIPAL_ID_ANONYMOUS]);
        }
        if (isset($data[JSONConstants::JSON_REPINFO_PRINCIPAL_ID_ANYONE])) {
            $object->setPrincipalIdAnyone((string) $data[JSONConstants::JSON_REPINFO_PRINCIPAL_ID_ANYONE]);
        }

        if (isset($data[JSONConstants::JSON_REPINFO_EXTENDED_FEATURES])
            && is_array($data[JSONConstants::JSON_REPINFO_EXTENDED_FEATURES])
        ) {
            $object->setExtensionFeatures(
                $this->convertExtensionFeatures($data[JSONConstants::JSON_REPINFO_EXTENDED_FEATURES])
            );
        }

        $object->setExtensions($this->convertExtension($data, JSONConstants::getRepositoryInfoKeys()));

        return $object;
    }

    /**
     * @param array $data
     * @return RepositoryCapabilities|null
     */
    public function convertRepositoryCapabilities(array $data = null)
    {
        if (empty($data)) {
            return null;
        }

        $repositoryCapabilities = new RepositoryCapabilities();
        if (isset($data[JSONConstants::JSON_CAP_CONTENT_STREAM_UPDATABILITY])) {
            $repositoryCapabilities->setContentStreamUpdatesCapability(
                CapabilityContentStreamUpdates::cast($data[JSONConstants::JSON_CAP_CONTENT_STREAM_UPDATABILITY])
            );
        }
        if (isset($data[JSONConstants::JSON_CAP_CHANGES])) {
            $repositoryCapabilities->setChangesCapability(
                CapabilityChanges::cast($data[JSONConstants::JSON_CAP_CHANGES])
            );
        }
        if (isset($data[JSONConstants::JSON_CAP_RENDITIONS])) {
            $repositoryCapabilities->setRenditionsCapability(
                CapabilityRenditions::cast($data[JSONConstants::JSON_CAP_RENDITIONS])
            );
        }
        if (isset($data[JSONConstants::JSON_CAP_GET_DESCENDANTS])) {
            $repositoryCapabilities->setSupportsGetDescendants(
                (boolean) $data[JSONConstants::JSON_CAP_GET_DESCENDANTS]
            );
        }
        if (isset($data[JSONConstants::JSON_CAP_GET_FOLDER_TREE])) {
            $repositoryCapabilities->setSupportsGetFolderTree((boolean) $data[JSONConstants::JSON_CAP_GET_FOLDER_TREE]);
        }
        if (isset($data[JSONConstants::JSON_CAP_MULTIFILING])) {
            $repositoryCapabilities->setSupportsMultifiling((boolean) $data[JSONConstants::JSON_CAP_MULTIFILING]);
        }
        if (isset($data[JSONConstants::JSON_CAP_UNFILING])) {
            $repositoryCapabilities->setSupportsUnfiling((boolean) $data[JSONConstants::JSON_CAP_UNFILING]);
        }
        if (isset($data[JSONConstants::JSON_CAP_VERSION_SPECIFIC_FILING])) {
            $repositoryCapabilities->setSupportsVersionSpecificFiling(
                (boolean) $data[JSONConstants::JSON_CAP_VERSION_SPECIFIC_FILING]
            );
        }
        if (isset($data[JSONConstants::JSON_CAP_PWC_SEARCHABLE])) {
            $repositoryCapabilities->setSupportsPwcSearchable((boolean) $data[JSONConstants::JSON_CAP_PWC_SEARCHABLE]);
        }
        if (isset($data[JSONConstants::JSON_CAP_PWC_UPDATABLE])) {
            $repositoryCapabilities->setSupportsPwcUpdatable((boolean) $data[JSONConstants::JSON_CAP_PWC_UPDATABLE]);
        }
        if (isset($data[JSONConstants::JSON_CAP_ALL_VERSIONS_SEARCHABLE])) {
            $repositoryCapabilities->setSupportsAllVersionsSearchable(
                (boolean) $data[JSONConstants::JSON_CAP_ALL_VERSIONS_SEARCHABLE]
            );
        }
        if (isset($data[JSONConstants::JSON_CAP_ORDER_BY])) {
            $repositoryCapabilities->setOrderByCapability(
                CapabilityOrderBy::cast($data[JSONConstants::JSON_CAP_ORDER_BY])
            );
        }
        if (isset($data[JSONConstants::JSON_CAP_QUERY])) {
            $repositoryCapabilities->setQueryCapability(CapabilityQuery::cast($data[JSONConstants::JSON_CAP_QUERY]));
        }
        if (isset($data[JSONConstants::JSON_CAP_JOIN])) {
            $repositoryCapabilities->setJoinCapability(CapabilityJoin::cast($data[JSONConstants::JSON_CAP_JOIN]));
        }
        if (isset($data[JSONConstants::JSON_CAP_ACL])) {
            $repositoryCapabilities->setAclCapability(CapabilityAcl::cast($data[JSONConstants::JSON_CAP_ACL]));
        }

        if (isset($data[JSONConstants::JSON_CAP_CREATABLE_PROPERTY_TYPES])
            && is_array($data[JSONConstants::JSON_CAP_CREATABLE_PROPERTY_TYPES])
        ) {
            $creatablePropertyTypes = new CreatablePropertyTypes();
            $creatablePropertyTypesData = $data[JSONConstants::JSON_CAP_CREATABLE_PROPERTY_TYPES];

            if (isset($creatablePropertyTypesData[JSONConstants::JSON_CAP_CREATABLE_PROPERTY_TYPES_CANCREATE])
                && is_array($creatablePropertyTypesData[JSONConstants::JSON_CAP_CREATABLE_PROPERTY_TYPES_CANCREATE])
            ) {
                $canCreate = array();

                foreach ($creatablePropertyTypesData[JSONConstants::JSON_CAP_CREATABLE_PROPERTY_TYPES_CANCREATE] as $canCreateItem) {
                    try {
                        $canCreate[] = PropertyType::cast($canCreateItem);
                    } catch (InvalidEnumerationValueException $exception) {
                        // ignore invalid types
                    }
                }

                $creatablePropertyTypes->setCanCreate($canCreate);
            }

            $creatablePropertyTypes->setExtensions(
                $this->convertExtension(
                    $creatablePropertyTypesData,
                    JSONConstants::getCapabilityCreatablePropertyKeys()
                )
            );

            $repositoryCapabilities->setCreatablePropertyTypes($creatablePropertyTypes);
        }

        if (isset($data[JSONConstants::JSON_CAP_NEW_TYPE_SETTABLE_ATTRIBUTES])
            && is_array($data[JSONConstants::JSON_CAP_NEW_TYPE_SETTABLE_ATTRIBUTES])
        ) {
            $newTypeSettableAttributesData = $data[JSONConstants::JSON_CAP_NEW_TYPE_SETTABLE_ATTRIBUTES];
            $newTypeSettableAttributes = new NewTypeSettableAttributes();

            if (isset($newTypeSettableAttributesData[JSONConstants::JSON_CAP_NEW_TYPE_SETTABLE_ATTRIBUTES_ID])) {
                $newTypeSettableAttributes->setCanSetId(
                    (boolean) $newTypeSettableAttributesData[JSONConstants::JSON_CAP_NEW_TYPE_SETTABLE_ATTRIBUTES_ID]
                );
            }
            if (isset($newTypeSettableAttributesData[JSONConstants::JSON_CAP_NEW_TYPE_SETTABLE_ATTRIBUTES_LOCALNAME])) {
                $newTypeSettableAttributes->setCanSetLocalName(
                    (boolean) $newTypeSettableAttributesData[JSONConstants::JSON_CAP_NEW_TYPE_SETTABLE_ATTRIBUTES_LOCALNAME]
                );
            }
            if (isset($newTypeSettableAttributesData[JSONConstants::JSON_CAP_NEW_TYPE_SETTABLE_ATTRIBUTES_LOCALNAMESPACE])) {
                $newTypeSettableAttributes->setCanSetLocalNamespace(
                    (boolean) $newTypeSettableAttributesData[JSONConstants::JSON_CAP_NEW_TYPE_SETTABLE_ATTRIBUTES_LOCALNAMESPACE]
                );
            }
            if (isset($newTypeSettableAttributesData[JSONConstants::JSON_CAP_NEW_TYPE_SETTABLE_ATTRIBUTES_DISPLAYNAME])) {
                $newTypeSettableAttributes->setCanSetDisplayName(
                    (boolean) $newTypeSettableAttributesData[JSONConstants::JSON_CAP_NEW_TYPE_SETTABLE_ATTRIBUTES_DISPLAYNAME]
                );
            }
            if (isset($newTypeSettableAttributesData[JSONConstants::JSON_CAP_NEW_TYPE_SETTABLE_ATTRIBUTES_QUERYNAME])) {
                $newTypeSettableAttributes->setCanSetQueryName(
                    (boolean) $newTypeSettableAttributesData[JSONConstants::JSON_CAP_NEW_TYPE_SETTABLE_ATTRIBUTES_QUERYNAME]
                );
            }
            if (isset($newTypeSettableAttributesData[JSONConstants::JSON_CAP_NEW_TYPE_SETTABLE_ATTRIBUTES_DESCRIPTION])) {
                $newTypeSettableAttributes->setCanSetDescription(
                    (boolean) $newTypeSettableAttributesData[JSONConstants::JSON_CAP_NEW_TYPE_SETTABLE_ATTRIBUTES_DESCRIPTION]
                );
            }
            if (isset($newTypeSettableAttributesData[JSONConstants::JSON_CAP_NEW_TYPE_SETTABLE_ATTRIBUTES_CREATEABLE])) {
                $newTypeSettableAttributes->setCanSetCreatable(
                    (boolean) $newTypeSettableAttributesData[JSONConstants::JSON_CAP_NEW_TYPE_SETTABLE_ATTRIBUTES_CREATEABLE]
                );
            }
            if (isset($newTypeSettableAttributesData[JSONConstants::JSON_CAP_NEW_TYPE_SETTABLE_ATTRIBUTES_FILEABLE])) {
                $newTypeSettableAttributes->setCanSetFileable(
                    (boolean) $newTypeSettableAttributesData[JSONConstants::JSON_CAP_NEW_TYPE_SETTABLE_ATTRIBUTES_FILEABLE]
                );
            }
            if (isset($newTypeSettableAttributesData[JSONConstants::JSON_CAP_NEW_TYPE_SETTABLE_ATTRIBUTES_QUERYABLE])) {
                $newTypeSettableAttributes->setCanSetQueryable(
                    (boolean) $newTypeSettableAttributesData[JSONConstants::JSON_CAP_NEW_TYPE_SETTABLE_ATTRIBUTES_QUERYABLE]
                );
            }
            if (isset($newTypeSettableAttributesData[JSONConstants::JSON_CAP_NEW_TYPE_SETTABLE_ATTRIBUTES_FULLTEXTINDEXED])) {
                $newTypeSettableAttributes->setCanSetFulltextIndexed(
                    (boolean) $newTypeSettableAttributesData[JSONConstants::JSON_CAP_NEW_TYPE_SETTABLE_ATTRIBUTES_FULLTEXTINDEXED]
                );
            }
            if (isset($newTypeSettableAttributesData[JSONConstants::JSON_CAP_NEW_TYPE_SETTABLE_ATTRIBUTES_INCLUDEDINSUPERTYTPEQUERY])) {
                $newTypeSettableAttributes->setCanSetIncludedInSupertypeQuery(
                    (boolean) $newTypeSettableAttributesData[JSONConstants::JSON_CAP_NEW_TYPE_SETTABLE_ATTRIBUTES_INCLUDEDINSUPERTYTPEQUERY]
                );
            }
            if (isset($newTypeSettableAttributesData[JSONConstants::JSON_CAP_NEW_TYPE_SETTABLE_ATTRIBUTES_CONTROLABLEPOLICY])) {
                $newTypeSettableAttributes->setCanSetControllablePolicy(
                    (boolean) $newTypeSettableAttributesData[JSONConstants::JSON_CAP_NEW_TYPE_SETTABLE_ATTRIBUTES_CONTROLABLEPOLICY]
                );
            }
            if (isset($newTypeSettableAttributesData[JSONConstants::JSON_CAP_NEW_TYPE_SETTABLE_ATTRIBUTES_CONTROLABLEACL])) {
                $newTypeSettableAttributes->setCanSetControllableAcl(
                    (boolean) $newTypeSettableAttributesData[JSONConstants::JSON_CAP_NEW_TYPE_SETTABLE_ATTRIBUTES_CONTROLABLEACL]
                );
            }

            $newTypeSettableAttributes->setExtensions(
                $this->convertExtension(
                    $newTypeSettableAttributesData,
                    JSONConstants::getCapabilityNewTypeSettableAttributeKeys()
                )
            );

            $repositoryCapabilities->setNewTypeSettableAttributes($newTypeSettableAttributes);
        }

        $repositoryCapabilities->setExtensions($this->convertExtension($data, JSONConstants::getCapabilityKeys()));

        return $repositoryCapabilities;
    }

    /**
     * @param array $data
     * @param boolean $isExact
     * @return AccessControlList
     */
    public function convertAcl(array $data = null, $isExact = false)
    {
        if (empty($data)) {
            return null;
        }

        $aces = array();
        if (isset($data[JSONConstants::JSON_ACL_ACES]) && is_array($data[JSONConstants::JSON_ACL_ACES])) {
            foreach ($data[JSONConstants::JSON_ACL_ACES] as $aceData) {
                if (empty($aceData[JSONConstants::JSON_ACE_PRINCIPAL][JSONConstants::JSON_ACE_PRINCIPAL_ID])) {
                    continue;
                }

                $permissions = array();
                if (isset($aceData[JSONConstants::JSON_ACE_PERMISSIONS])
                    && is_array($aceData[JSONConstants::JSON_ACE_PERMISSIONS])
                ) {
                    foreach ($aceData[JSONConstants::JSON_ACE_PERMISSIONS] as $permissionItem) {
                        if (!empty($permissionItem)) {
                            $permissions[] = $permissionItem;
                        }
                    }
                }

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
                    $ace->setIsDirect((boolean) $aceData[JSONConstants::JSON_ACE_IS_DIRECT]);
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
     * @param array $data
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

        if (isset($data[JSONConstants::JSON_ACLCAP_PERMISSIONS])
            && is_array($data[JSONConstants::JSON_ACLCAP_PERMISSIONS])
        ) {
            $permissionsData = $data[JSONConstants::JSON_ACLCAP_PERMISSIONS];
            $permissionDefinitionList = array();

            if (is_array($permissionsData)) {
                foreach ($permissionsData as $permissionData) {
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
            }

            $aclCapabilities->setPermissions($permissionDefinitionList);
        }

        if (isset($data[JSONConstants::JSON_ACLCAP_PERMISSION_MAPPING])
            && is_array($data[JSONConstants::JSON_ACLCAP_PERMISSION_MAPPING])
        ) {
            $permissionMappingData = $data[JSONConstants::JSON_ACLCAP_PERMISSION_MAPPING];
            $permissionMappingList = array();

            foreach ($permissionMappingData as $permissionMapping) {
                $mapping = new PermissionMapping();
                $key = (string) $permissionMapping[JSONConstants::JSON_ACLCAP_MAPPING_KEY];

                $mapping->setKey($key);

                $permissionList = array();
                if (isset($permissionMapping[JSONConstants::JSON_ACLCAP_MAPPING_PERMISSION])
                    && is_array($permissionMapping[JSONConstants::JSON_ACLCAP_MAPPING_PERMISSION])
                ) {
                    foreach ($permissionMapping[JSONConstants::JSON_ACLCAP_MAPPING_PERMISSION] as $permission) {
                        if (!empty($permission)) {
                            $permissionList[] = (string) $permission;
                        }
                    }
                }
                $mapping->setPermissions($permissionList);

                $mapping->setExtensions(
                    $this->convertExtension($permissionMapping, JSONConstants::getAclCapabilityMappingKeys())
                );

                $permissionMappingList[$key] = $mapping;
            }

            $aclCapabilities->setPermissionMapping($permissionMappingList);
        }

        // handle extensions
        $aclCapabilities->setExtensions($this->convertExtension($data, JSONConstants::getAclCapabilityKeys()));

        return $aclCapabilities;
    }

    /**
     * Convert an array to a type definition object
     *
     * @param array $data
     * @return TypeDefinitionInterface|null
     */
    public function convertTypeDefinition(array $data = null)
    {
        if (empty($data)) {
            return null;
        }

        $id = null;
        if (!empty($data[JSONConstants::JSON_TYPE_ID])) {
            $id = $data[JSONConstants::JSON_TYPE_ID];
        }
        $baseType = BaseTypeId::cast($data[JSONConstants::JSON_TYPE_BASE_ID]);

        if ($baseType->equals(BaseTypeId::cast(BaseTypeId::CMIS_FOLDER))) {
            $result = new FolderTypeDefinition();
        } elseif ($baseType->equals(BaseTypeId::cast(BaseTypeId::CMIS_DOCUMENT))) {
            $result = new DocumentTypeDefinition();
            $result->setContentStreamAllowed(
                ContentStreamAllowed::cast($data[JSONConstants::JSON_TYPE_CONTENTSTREAM_ALLOWED])
            );
            $result->setIsVersionable((boolean) $data[JSONConstants::JSON_TYPE_VERSIONABLE]);
        } elseif ($baseType->equals(BaseTypeId::cast(BaseTypeId::CMIS_RELATIONSHIP))) {
            $result = new RelationshipTypeDefinition();
            if (isset($data[JSONConstants::JSON_TYPE_ALLOWED_SOURCE_TYPES])
                && is_array($data[JSONConstants::JSON_TYPE_ALLOWED_SOURCE_TYPES])
            ) {
                $allowedSourceTypeIds = array();
                foreach ($data[JSONConstants::JSON_TYPE_ALLOWED_SOURCE_TYPES] as $allowedSourceTypeId) {
                    $allowedSourceTypeId = (string) $allowedSourceTypeId;
                    if (!empty($allowedSourceTypeId)) {
                        $allowedSourceTypeIds[] = $allowedSourceTypeId;
                    }
                }
                $result->setAllowedSourceTypeIds($allowedSourceTypeIds);
            }
            if (isset($data[JSONConstants::JSON_TYPE_ALLOWED_TARGET_TYPES])
                && is_array($data[JSONConstants::JSON_TYPE_ALLOWED_TARGET_TYPES])
            ) {
                $allowedTargetTypeIds = array();
                foreach ($data[JSONConstants::JSON_TYPE_ALLOWED_TARGET_TYPES] as $allowedTargetTypeId) {
                    $allowedTargetTypeId = (string) $allowedTargetTypeId;
                    if (!empty($allowedTargetTypeId)) {
                        $allowedTargetTypeIds[] = $allowedTargetTypeId;
                    }
                }
                $result->setAllowedTargetTypeIds($allowedTargetTypeIds);
            }
        } elseif ($baseType->equals(BaseTypeId::cast(BaseTypeId::CMIS_POLICY))) {
            $result = new PolicyTypeDefinition();
        } elseif ($baseType->equals(BaseTypeId::cast(BaseTypeId::CMIS_ITEM))) {
            $result = new ItemTypeDefinition();
        } elseif ($baseType->equals(BaseTypeId::cast(BaseTypeId::CMIS_SECONDARY))) {
            $result = new SecondaryTypeDefinition();
        } else {
            // this could only happen if a new baseType is added to the enumeration and not implemented here.
            throw new CmisInvalidArgumentException(
                sprintf('The given type definition "%s" could not be converted.', $baseType)
            );
        }

        $result->setBaseTypeId($baseType);
        $result->setId($id);
        if (isset($data[JSONConstants::JSON_TYPE_DESCRIPTION])) {
            $result->setDescription((string) $data[JSONConstants::JSON_TYPE_DESCRIPTION]);
        }
        if (isset($data[JSONConstants::JSON_TYPE_DISPLAYNAME])) {
            $result->setDisplayName((string) $data[JSONConstants::JSON_TYPE_DISPLAYNAME]);
        }
        if (isset($data[JSONConstants::JSON_TYPE_CONTROLABLE_ACL])) {
            $result->setIsControllableACL((boolean) $data[JSONConstants::JSON_TYPE_CONTROLABLE_ACL]);
        }
        if (isset($data[JSONConstants::JSON_TYPE_CONTROLABLE_POLICY])) {
            $result->setIsControllablePolicy((boolean) $data[JSONConstants::JSON_TYPE_CONTROLABLE_POLICY]);
        }
        if (isset($data[JSONConstants::JSON_TYPE_CREATABLE])) {
            $result->setIsCreatable((boolean) $data[JSONConstants::JSON_TYPE_CREATABLE]);
        }
        if (isset($data[JSONConstants::JSON_TYPE_FILEABLE])) {
            $result->setIsFileable((boolean) $data[JSONConstants::JSON_TYPE_FILEABLE]);
        }
        if (isset($data[JSONConstants::JSON_TYPE_FULLTEXT_INDEXED])) {
            $result->setIsFulltextIndexed((boolean) $data[JSONConstants::JSON_TYPE_FULLTEXT_INDEXED]);
        }
        if (isset($data[JSONConstants::JSON_TYPE_INCLUDE_IN_SUPERTYPE_QUERY])) {
            $result->setIsIncludedInSupertypeQuery(
                (boolean) $data[JSONConstants::JSON_TYPE_INCLUDE_IN_SUPERTYPE_QUERY]
            );
        }
        if (isset($data[JSONConstants::JSON_TYPE_QUERYABLE])) {
            $result->setIsQueryable((boolean) $data[JSONConstants::JSON_TYPE_QUERYABLE]);
        }
        if (isset($data[JSONConstants::JSON_TYPE_LOCALNAME])) {
            $result->setLocalName((string) $data[JSONConstants::JSON_TYPE_LOCALNAME]);
        }
        if (isset($data[JSONConstants::JSON_TYPE_LOCALNAMESPACE])) {
            $result->setLocalNamespace((string) $data[JSONConstants::JSON_TYPE_LOCALNAMESPACE]);
        }
        if (isset($data[JSONConstants::JSON_TYPE_PARENT_ID])) {
            $result->setParentTypeId((string) $data[JSONConstants::JSON_TYPE_PARENT_ID]);
        }
        if (isset($data[JSONConstants::JSON_TYPE_QUERYNAME])) {
            $result->setQueryName((string) $data[JSONConstants::JSON_TYPE_QUERYNAME]);
        }

        if (isset($data[JSONConstants::JSON_TYPE_TYPE_MUTABILITY])
            && is_array($data[JSONConstants::JSON_TYPE_TYPE_MUTABILITY])
        ) {
            $typeMutabilityData = $data[JSONConstants::JSON_TYPE_TYPE_MUTABILITY];
            $typeMutability = new TypeMutability();
            if (isset($typeMutabilityData[JSONConstants::JSON_TYPE_TYPE_MUTABILITY_CREATE])) {
                $typeMutability->setCanCreate(
                    (boolean) $typeMutabilityData[JSONConstants::JSON_TYPE_TYPE_MUTABILITY_CREATE]
                );
            }
            if (isset($typeMutabilityData[JSONConstants::JSON_TYPE_TYPE_MUTABILITY_UPDATE])) {
                $typeMutability->setCanUpdate(
                    (boolean) $typeMutabilityData[JSONConstants::JSON_TYPE_TYPE_MUTABILITY_UPDATE]
                );
            }
            if (isset($typeMutabilityData[JSONConstants::JSON_TYPE_TYPE_MUTABILITY_DELETE])) {
                $typeMutability->setCanDelete(
                    (boolean) $typeMutabilityData[JSONConstants::JSON_TYPE_TYPE_MUTABILITY_DELETE]
                );
            }

            $typeMutability->setExtensions(
                $this->convertExtension($typeMutabilityData, JSONConstants::getTypeTypeMutabilityKeys())
            );
        }

        if (isset($data[JSONConstants::JSON_TYPE_PROPERTY_DEFINITIONS])
            && is_array($data[JSONConstants::JSON_TYPE_PROPERTY_DEFINITIONS])
        ) {
            foreach ($data[JSONConstants::JSON_TYPE_PROPERTY_DEFINITIONS] as $propertyDefinitionData) {
                if (is_array($propertyDefinitionData)) {
                    $propertyDefinition = $this->convertPropertyDefinition($propertyDefinitionData);
                    if ($propertyDefinition !== null) {
                        $result->addPropertyDefinition($propertyDefinition);
                    }
                }
            }
        }

        $result->setExtensions($this->convertExtension($data, JSONConstants::getTypeKeys()));

        return $result;
    }

    /**
     * @param array $data
     * @return PropertyDefinitionInterface
     */
    public function convertPropertyDefinition(array $data = null)
    {
        if (empty($data)) {
            return null;
        }
        $id = null;
        if (!empty($data[JSONConstants::JSON_PROPERTY_TYPE_ID])) {
            $id = $data[JSONConstants::JSON_PROPERTY_TYPE_ID];
        }

        $propertyType = PropertyType::cast($data[JSONConstants::JSON_PROPERTY_TYPE_PROPERTY_TYPE]);
        $cardinality = Cardinality::cast($data[JSONConstants::JSON_PROPERTY_TYPE_CARDINALITY]);

        if ($propertyType->equals(PropertyType::STRING)) {
            $propertyDefinition = new PropertyStringDefinition($id);
            if (isset($data[JSONConstants::JSON_PROPERTY_TYPE_MAX_LENGTH])) {
                $propertyDefinition->setMaxLength((integer) $data[JSONConstants::JSON_PROPERTY_TYPE_MAX_LENGTH]);
            }
        } elseif ($propertyType->equals(PropertyType::ID)) {
            $propertyDefinition = new PropertyIdDefinition($id);
        } elseif ($propertyType->equals(PropertyType::BOOLEAN)) {
            $propertyDefinition = new PropertyBooleanDefinition($id);
        } elseif ($propertyType->equals(PropertyType::INTEGER)) {
            $propertyDefinition = new PropertyIntegerDefinition($id);
            if (isset($data[JSONConstants::JSON_PROPERTY_TYPE_MIN_VALUE])) {
                $propertyDefinition->setMinValue((integer) $data[JSONConstants::JSON_PROPERTY_TYPE_MIN_VALUE]);
            }
            if (isset($data[JSONConstants::JSON_PROPERTY_TYPE_MAX_VALUE])) {
                $propertyDefinition->setMaxValue((integer) $data[JSONConstants::JSON_PROPERTY_TYPE_MAX_VALUE]);
            }
        } elseif ($propertyType->equals(PropertyType::DATETIME)) {
            $propertyDefinition = new PropertyDateTimeDefinition($id);
            if (isset($data[JSONConstants::JSON_PROPERTY_TYPE_RESOLUTION])) {
                $propertyDefinition->setDateTimeResolution(
                    DateTimeResolution::cast($data[JSONConstants::JSON_PROPERTY_TYPE_RESOLUTION])
                );
            }
        } elseif ($propertyType->equals(PropertyType::DECIMAL)) {
            $propertyDefinition = new PropertyDecimalDefinition($id);
            if (isset($data[JSONConstants::JSON_PROPERTY_TYPE_MIN_VALUE])) {
                $propertyDefinition->setMinValue((integer) $data[JSONConstants::JSON_PROPERTY_TYPE_MIN_VALUE]);
            }
            if (isset($data[JSONConstants::JSON_PROPERTY_TYPE_MAX_VALUE])) {
                $propertyDefinition->setMaxValue((integer) $data[JSONConstants::JSON_PROPERTY_TYPE_MAX_VALUE]);
            }
            if (isset($data[JSONConstants::JSON_PROPERTY_TYPE_PRECISION])) {
                $propertyDefinition->setPrecision(
                    DecimalPrecision::cast($data[JSONConstants::JSON_PROPERTY_TYPE_PRECISION])
                );
            }
        } elseif ($propertyType->equals(PropertyType::HTML)) {
            $propertyDefinition = new PropertyHtmlDefinition($id);
        } elseif ($propertyType->equals(PropertyType::URI)) {
            $propertyDefinition = new PropertyUriDefinition($id);
        } else {
            // this could only happen if a new property type is added to the enumeration and not implemented here.
            throw new CmisInvalidArgumentException(
                sprintf('The given property definition "%s" could not be converted.', $propertyType)
            );
        }

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

        $propertyDefinition->setPropertyType($propertyType);
        $propertyDefinition->setCardinality($cardinality);
        if (isset($data[JSONConstants::JSON_PROPERTY_TYPE_LOCALNAME])) {
            $propertyDefinition->setLocalName((string) $data[JSONConstants::JSON_PROPERTY_TYPE_LOCALNAME]);
        }
        if (isset($data[JSONConstants::JSON_PROPERTY_TYPE_LOCALNAMESPACE])) {
            $propertyDefinition->setLocalNamespace((string) $data[JSONConstants::JSON_PROPERTY_TYPE_LOCALNAMESPACE]);
        }
        if (isset($data[JSONConstants::JSON_PROPERTY_TYPE_QUERYNAME])) {
            $propertyDefinition->setQueryName((string) $data[JSONConstants::JSON_PROPERTY_TYPE_QUERYNAME]);
        }
        if (isset($data[JSONConstants::JSON_PROPERTY_TYPE_DESCRIPTION])) {
            $propertyDefinition->setDescription((string) $data[JSONConstants::JSON_PROPERTY_TYPE_DESCRIPTION]);
        }
        if (isset($data[JSONConstants::JSON_PROPERTY_TYPE_DISPLAYNAME])) {
            $propertyDefinition->setDisplayName((string) $data[JSONConstants::JSON_PROPERTY_TYPE_DISPLAYNAME]);
        }
        if (isset($data[JSONConstants::JSON_PROPERTY_TYPE_INHERITED])) {
            $propertyDefinition->setIsInherited((boolean) $data[JSONConstants::JSON_PROPERTY_TYPE_INHERITED]);
        }
        if (isset($data[JSONConstants::JSON_PROPERTY_TYPE_OPENCHOICE])) {
            $propertyDefinition->setIsOpenChoice((boolean) $data[JSONConstants::JSON_PROPERTY_TYPE_OPENCHOICE]);
        }
        if (isset($data[JSONConstants::JSON_PROPERTY_TYPE_ORDERABLE])) {
            $propertyDefinition->setIsOrderable((boolean) $data[JSONConstants::JSON_PROPERTY_TYPE_ORDERABLE]);
        }
        if (isset($data[JSONConstants::JSON_PROPERTY_TYPE_QUERYABLE])) {
            $propertyDefinition->setIsQueryable((boolean) $data[JSONConstants::JSON_PROPERTY_TYPE_QUERYABLE]);
        }
        if (isset($data[JSONConstants::JSON_PROPERTY_TYPE_REQUIRED])) {
            $propertyDefinition->setIsRequired((boolean) $data[JSONConstants::JSON_PROPERTY_TYPE_REQUIRED]);
        }
        if (isset($data[JSONConstants::JSON_PROPERTY_TYPE_UPDATABILITY])) {
            $propertyDefinition->setUpdatability(
                Updatability::cast($data[JSONConstants::JSON_PROPERTY_TYPE_UPDATABILITY])
            );
        }

        $propertyDefinition->setExtensions($this->convertExtension($data, JSONConstants::getPropertyTypeKeys()));

        return $propertyDefinition;
    }

    /**
     * Converts an object.
     *
     * @param array $data
     * @return null|ObjectData
     */
    public function convertObject(array $data = null)
    {
        if (empty($data)) {
            return null;
        }

        $object = new ObjectData();
        if (isset($data[JSONConstants::JSON_OBJECT_ACL]) && is_array($data[JSONConstants::JSON_OBJECT_ACL])
            && isset($data[JSONConstants::JSON_OBJECT_EXACT_ACL])
        ) {
            $acl = $this->convertAcl(
                $data[JSONConstants::JSON_OBJECT_ACL],
                (boolean) $data[JSONConstants::JSON_OBJECT_EXACT_ACL]
            );
            if ($acl !== null) {
                $object->setAcl($acl);
            }
        }

        if (isset($data[JSONConstants::JSON_OBJECT_ALLOWABLE_ACTIONS])) {
            $allowableActions = $this->convertAllowableActions($data[JSONConstants::JSON_OBJECT_ALLOWABLE_ACTIONS]);
            if ($allowableActions !== null) {
                $object->setAllowableActions($allowableActions);
            }
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

        if (isset($data[JSONConstants::JSON_OBJECT_EXACT_ACL])) {
            $object->setIsExactAcl((boolean) $data[JSONConstants::JSON_OBJECT_EXACT_ACL]);
        }
        if (isset($data[JSONConstants::JSON_OBJECT_POLICY_IDS])) {
            $object->setPolicyIds($this->convertPolicyIdList($data[JSONConstants::JSON_OBJECT_POLICY_IDS]));
        }

        /**
         * A client MAY add the query parameter succinct (HTTP GET) or the control succinct (HTTP POST) with the
         * value "true" to a request. If this is set, the repository MUST return properties in a succinct format.
         * That is, whenever the repository renders an object or a query result, it MUST populate the
         * succinctProperties value and MUST NOT populate the properties value.
         *
         * @see http://docs.oasis-open.org/cmis/CMIS/v1.1/os/CMIS-v1.1-os.html#x1-552027r554
         */
        if (isset($data[JSONConstants::JSON_OBJECT_SUCCINCT_PROPERTIES])
            && is_array($data[JSONConstants::JSON_OBJECT_SUCCINCT_PROPERTIES])
        ) {
            $properties = $data[JSONConstants::JSON_OBJECT_SUCCINCT_PROPERTIES];
            $propertiesExtension = null;
            if (isset($data[JSONConstants::JSON_OBJECT_PROPERTIES_EXTENSION])) {
                $propertiesExtension = $data[JSONConstants::JSON_OBJECT_PROPERTIES_EXTENSION];
            }
            $object->setProperties($this->convertSuccinctProperties($properties, $propertiesExtension));
        } elseif (isset($data[JSONConstants::JSON_OBJECT_PROPERTIES])
            && is_array($data[JSONConstants::JSON_OBJECT_PROPERTIES])
        ) {
            $propertiesExtension = array();
            if (isset($data[JSONConstants::JSON_OBJECT_PROPERTIES_EXTENSION])) {
                $propertiesExtension = (array) $data[JSONConstants::JSON_OBJECT_PROPERTIES_EXTENSION];
            }
            $properties = $this->convertProperties($data[JSONConstants::JSON_OBJECT_PROPERTIES], $propertiesExtension);
            if ($properties !== null) {
                $object->setProperties($properties);
            }
        }

        if (isset($data[JSONConstants::JSON_OBJECT_RELATIONSHIPS])
            && is_array($data[JSONConstants::JSON_OBJECT_RELATIONSHIPS])
        ) {
            $relationships = $this->convertObjects($data[JSONConstants::JSON_OBJECT_RELATIONSHIPS]);
            if ($relationships !== null) {
                $object->setRelationships($relationships);
            }
        }

        if (isset($data[JSONConstants::JSON_OBJECT_RENDITIONS])
            && is_array($data[JSONConstants::JSON_OBJECT_RENDITIONS])
        ) {
            $object->setRenditions($this->convertRenditions($data[JSONConstants::JSON_OBJECT_RENDITIONS]));
        }

        $object->setExtensions($this->convertExtension($data, JSONConstants::getObjectKeys()));

        return $object;
    }

    /**
     * @param array $data
     * @return array
     */
    public function convertObjects(array $data = null)
    {
        $objects = array();

        if (empty($data)) {
            return $objects;
        }

        foreach ($data as $itemData) {
            if (!is_array($itemData)) {
                continue;
            }
            $object = $this->convertObject($itemData);

            // @TODO once a logger is available we should log an INFO message if the object could not be converted
            if ($object !== null) {
                $objects[] = $object;
            }
        }

        return $objects;
    }

    /**
     * @param array $data
     * @param array $extensions
     * @return null|Properties
     * @throws CmisRuntimeException
     */
    public function convertProperties(array $data = null, $extensions = array())
    {
        if (empty($data)) {
            return null;
        }
        $properties = new Properties();

        foreach ($data as $propertyKey => $propertyData) {
            $id = (!empty($propertyData[JSONConstants::JSON_PROPERTY_ID])) ?
                $propertyData[JSONConstants::JSON_PROPERTY_ID] : null;
            $queryName = (!empty($propertyData[JSONConstants::JSON_PROPERTY_QUERYNAME])) ?
                $propertyData[JSONConstants::JSON_PROPERTY_QUERYNAME] : null;

            if ($id === null && $queryName === null) {
                throw new CmisRuntimeException('Invalid property!');
            }

            try {
                $propertyType = PropertyType::cast($propertyData[JSONConstants::JSON_PROPERTY_DATATYPE]);
            } catch (InvalidEnumerationValueException $exception) {
                throw new CmisRuntimeException(
                    sprintf('Unknown property type "%s"!', $propertyData[JSONConstants::JSON_PROPERTY_DATATYPE])
                );
            }

            if (is_array($propertyData[JSONConstants::JSON_PROPERTY_VALUE])) {
                $propertyValues = $propertyData[JSONConstants::JSON_PROPERTY_VALUE];
            } else {
                $propertyValues = array($propertyData[JSONConstants::JSON_PROPERTY_VALUE]);
            }

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

            $property->setQueryName($queryName);
            if (isset($propertyData[JSONConstants::JSON_PROPERTY_DISPLAYNAME])) {
                $property->setDisplayName((string) $propertyData[JSONConstants::JSON_PROPERTY_DISPLAYNAME]);
            }
            if (isset($propertyData[JSONConstants::JSON_PROPERTY_LOCALNAME])) {
                $property->setLocalName((string) $propertyData[JSONConstants::JSON_PROPERTY_LOCALNAME]);
            }

            $property->setExtensions($this->convertExtension($propertyData, JSONConstants::getPropertyKeys()));

            $properties->addProperty($property);
        }

        if (!empty($extensions)) {
            $properties->setExtensions($this->convertExtension($extensions));
        }

        return $properties;
    }

    /**
     * TODO Add description
     *
     * @param array $data
     * @param array $extensions
     * @return PropertiesInterface[]
     * @throws \Exception
     */
    public function convertSuccinctProperties(array $data = null, $extensions = array())
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
     * @param array $data
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
     * @param array $data
     * @return RenditionData[]
     */
    public function convertRenditions(array $data = null)
    {
        if (empty($data)) {
            return array();
        }
        $renditions = array();

        foreach ($data as $renditionData) {
            $rendition = null;
            if (is_array($renditionData)) {
                $rendition = $this->convertRendition($renditionData);
            }

            // @TODO once a logger is available we should log an INFO message if the rendition could not be converted
            if ($rendition !== null) {
                $renditions[] = $rendition;
            }
        }

        return $renditions;
    }

    /**
     * Convert given input data to an Extension object
     *
     * @param array $data
     * @param string[] $cmisKeys
     * @return CmisExtensionElement[]
     */
    public function convertExtension(array $data = null, array $cmisKeys = array())
    {
        if (empty($data)) {
            return array();
        }

        $extensions = array();

        foreach ($data as $key => $value) {
            if (in_array($key, $cmisKeys)) {
                continue;
            }

            if (is_array($value)) {
                $extension = $this->convertExtension($value, $cmisKeys);

                if (!empty($extension)) {
                    $extensions[] = new CmisExtensionElement(
                        null,
                        $key,
                        array(),
                        null,
                        $extension
                    );
                }
            } else {
                $value = (empty($value)) ? null : (string) $value;
                $extensions[] = new CmisExtensionElement(null, $key, array(), $value);
            }
        }

        return $extensions;
    }

    /**
     * Convert given input data to an ExtensionFeature object
     *
     * @param array $data
     * @return ExtensionFeature[]
     */
    public function convertExtensionFeatures(array $data = null)
    {
        $features = array();

        if (empty($data)) {
            return $features;
        }

        foreach ($data as $extendedFeature) {
            if (!is_array($extendedFeature) || empty($extendedFeature)) {
                continue;
            }

            $feature = new ExtensionFeature();
            $feature->setId((string) $extendedFeature[JSONConstants::JSON_FEATURE_ID]);
            $feature->setUrl((string) $extendedFeature[JSONConstants::JSON_FEATURE_URL]);
            $feature->setCommonName((string) $extendedFeature[JSONConstants::JSON_FEATURE_COMMON_NAME]);
            $feature->setVersionLabel((string) $extendedFeature[JSONConstants::JSON_FEATURE_VERSION_LABEL]);
            $feature->setDescription((string) $extendedFeature[JSONConstants::JSON_FEATURE_DESCRIPTION]);

            if (isset($extendedFeature[JSONConstants::JSON_FEATURE_DATA])
                && is_array($extendedFeature[JSONConstants::JSON_FEATURE_DATA])
            ) {
                $data = array();
                foreach ($extendedFeature[JSONConstants::JSON_FEATURE_DATA] as $key => $value) {
                    $data[$key] = $value;
                }

                $feature->setFeatureData($data);
            }

            $feature->setExtensions($this->convertExtension($extendedFeature, JSONConstants::getFeatureKeys()));

            $features[] = $feature;
        }

        return $features;
    }

    /**
     * Converts a list of policy ids.
     *
     * @param array $data
     * @return PolicyIdList List of policy ids
     */
    public function convertPolicyIdList(array $data = null)
    {
        $policyIdsList = new PolicyIdList();
        $list = array();

        if (isset($data[JSONConstants::JSON_OBJECT_POLICY_IDS_IDS])) {
            foreach ((array) $data[JSONConstants::JSON_OBJECT_POLICY_IDS_IDS] as $id) {
                if (!empty($id) && is_string($id)) {
                    $list[] = $id;
                }
            }
        }

        $policyIdsList->setPolicyIds($list);
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
     * @return mixed
     */
    public function convertFromTypeDefinition(TypeDefinitionInterface $typeDefinition)
    {
        // TODO: Implement convertFromTypeDefinition() method.
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
     * @param array $data
     * @return TypeDefinitionListInterface
     */
    public function convertTypeChildren(array $data = null)
    {
        // TODO: Implement convertTypeChildren() method.
    }

    /**
     * Convert given input data to a TypeDescendants object
     *
     * @param array $data
     * @return TypeDefinitionContainerInterface
     */
    public function convertTypeDescendants(array $data = null)
    {
        // TODO: Implement convertTypeDescendants() method.
    }

    /**
     * Convert given input data to a ObjectInFolderList object
     *
     * @param array $data
     * @return null|ObjectInFolderList
     */
    public function convertObjectInFolderList(array $data = null)
    {
        if (empty($data)) {
            return null;
        }

        $objectInFolderList = new ObjectInFolderList();
        $objects = array();

        if (isset($data[JSONConstants::JSON_OBJECTINFOLDERLIST_OBJECTS])) {
            foreach ((array) $data[JSONConstants::JSON_OBJECTINFOLDERLIST_OBJECTS] as $objectInFolderData) {
                if (!empty($objectInFolderData)) {
                    $object = $this->convertObjectInFolder($objectInFolderData);

                    if ($object !== null) {
                        $objects[] = $object;
                    }
                }
            }
        }

        $objectInFolderList->setObjects($objects);

        if (isset($data[JSONConstants::JSON_OBJECTINFOLDERLIST_HAS_MORE_ITEMS])) {
            $objectInFolderList->setHasMoreItems(
                (boolean) $data[JSONConstants::JSON_OBJECTINFOLDERLIST_HAS_MORE_ITEMS]
            );
        }

        if (isset($data[JSONConstants::JSON_OBJECTINFOLDERLIST_NUM_ITEMS])) {
            $objectInFolderList->setNumItems((integer) $data[JSONConstants::JSON_OBJECTINFOLDERLIST_NUM_ITEMS]);
        }

        $objectInFolderList->setExtensions($this->convertExtension($data, JSONConstants::getObjectInFolderListKeys()));

        return $objectInFolderList;
    }

    /**
     * Convert given input data to a ObjectInFolderData object
     *
     * @param array $data
     * @return ObjectInFolderData|null
     */
    public function convertObjectInFolder(array $data = null)
    {
        if (empty($data)) {
            return null;
        }

        $objectInFolderData = new ObjectInFolderData();

        if (isset($data[JSONConstants::JSON_OBJECTINFOLDER_OBJECT])) {
            $object = $this->convertObject($data[JSONConstants::JSON_OBJECTINFOLDER_OBJECT]);

            if ($object !== null) {
                $objectInFolderData->setObject($object);
            }
        }

        if (isset($data[JSONConstants::JSON_OBJECTINFOLDER_PATH_SEGMENT])) {
            $objectInFolderData->setPathSegment((string) $data[JSONConstants::JSON_OBJECTINFOLDER_PATH_SEGMENT]);
        }

        $objectInFolderData->setExtensions($this->convertExtension($data, JSONConstants::getObjectInFolderKeys()));

        return $objectInFolderData;
    }

    /**
     * Convert given input data to a list of ObjectParentData objects
     *
     * @param array $data
     * @return ObjectParentData[]
     */
    public function convertObjectParents(array $data = null)
    {
        if (empty($data)) {
            return array();
        }
        $parents = array();

        foreach ($data as $parentData) {
            $parent = $this->convertObjectParentData($parentData);

            // @TODO once a logger is available we should log an INFO message if the parent data could not be converted
            if ($parent !== null) {
                $parents[] = $parent;
            }
        }

        return $parents;
    }

    /**
     * Convert given input data to a ObjectParentData object
     *
     * @param array $data
     * @return null|ObjectParentData
     */
    public function convertObjectParentData(array $data = null)
    {
        if (empty($data)) {
            return null;
        }
        $parent = new ObjectParentData();

        if (isset($data[JSONConstants::JSON_OBJECTPARENTS_OBJECT])) {
            $object = $this->convertObject($data[JSONConstants::JSON_OBJECTPARENTS_OBJECT]);
            if ($object !== null) {
                $parent->setObject($object);
            }
        }

        if (isset($data[JSONConstants::JSON_OBJECTPARENTS_RELATIVE_PATH_SEGMENT])) {
            $parent->setRelativePathSegment((string) $data[JSONConstants::JSON_OBJECTPARENTS_RELATIVE_PATH_SEGMENT]);
        }

        $parent->setExtensions($this->convertExtension($data, JSONConstants::getObjectParentsKeys()));

        return $parent;
    }

    /**
     * Convert given input data array to a ObjectList object
     *
     * @param array $data
     * @return null|ObjectList
     */
    public function convertObjectList(array $data = null)
    {
        if (empty($data)) {
            return null;
        }

        $objectList = new ObjectList();
        $objects = array();

        if (isset($data[JSONConstants::JSON_OBJECTLIST_OBJECTS])) {
            foreach ((array) $data[JSONConstants::JSON_OBJECTLIST_OBJECTS] as $objectData) {
                $object = $this->convertObject($objectData);

                if ($object !== null) {
                    $objects[] = $object;
                }
            }
        }

        $objectList->setObjects($objects);

        if (isset($data[JSONConstants::JSON_OBJECTLIST_HAS_MORE_ITEMS])) {
            $objectList->setHasMoreItems(
                (boolean) $data[JSONConstants::JSON_OBJECTLIST_HAS_MORE_ITEMS]
            );
        }

        if (isset($data[JSONConstants::JSON_OBJECTLIST_NUM_ITEMS])) {
            $objectList->setNumItems((integer) $data[JSONConstants::JSON_OBJECTLIST_NUM_ITEMS]);
        }

        $objectList->setExtensions($this->convertExtension($data, JSONConstants::getObjectListKeys()));

        return $objectList;
    }

    /**
     * Convert given input data array from query result to a ObjectList object
     *
     * @param array $data
     * @return null|ObjectList
     */
    public function convertQueryResultList(array $data = null)
    {
        if (empty($data)) {
            return null;
        }

        $objectList = new ObjectList();
        $objects = array();

        if (isset($data[JSONConstants::JSON_QUERYRESULTLIST_RESULTS])) {
            foreach ((array) $data[JSONConstants::JSON_QUERYRESULTLIST_RESULTS] as $objectData) {
                $object = $this->convertObject($objectData);

                if ($object !== null) {
                    $objects[] = $object;
                }
            }
        }

        $objectList->setObjects($objects);

        if (isset($data[JSONConstants::JSON_QUERYRESULTLIST_HAS_MORE_ITEMS])) {
            $objectList->setHasMoreItems(
                (boolean) $data[JSONConstants::JSON_QUERYRESULTLIST_HAS_MORE_ITEMS]
            );
        }

        if (isset($data[JSONConstants::JSON_QUERYRESULTLIST_NUM_ITEMS])) {
            $objectList->setNumItems((integer) $data[JSONConstants::JSON_QUERYRESULTLIST_NUM_ITEMS]);
        }

        $objectList->setExtensions($this->convertExtension($data, JSONConstants::getQueryResultListKeys()));

        return $objectList;
    }

    /**
     * Convert given input data array to a ObjectList object
     *
     * @param array $data
     * @return ObjectInFolderContainer[]
     */
    public function convertDescendants(array $data = null)
    {
        if (empty($data)) {
            return array();
        }

        $descendants = array();

        foreach ($data as $descendantData) {
            $descendant = $this->convertDescendant($descendantData);
            if ($descendant !== null) {
                $descendants[] = $descendant;
            }
        }

        return $descendants;
    }

    /**
     * Convert given input data array to a ObjectInFolderContainer object
     *
     * @param array $data
     * @return null|ObjectInFolderContainer
     * @throws CmisRuntimeException
     */
    public function convertDescendant(array $data = null)
    {
        if (empty($data)) {
            return null;
        }

        $object = null;
        if (isset($data[JSONConstants::JSON_OBJECTINFOLDERCONTAINER_OBJECT])) {
            $object = $this->convertObjectInFolder($data[JSONConstants::JSON_OBJECTINFOLDERCONTAINER_OBJECT]);
        }

        if ($object === null) {
            throw new CmisRuntimeException('Given data could not be converted to ObjectInFolder!');
        }

        $objectInFolderContainer = new ObjectInFolderContainer($object);
        $children = array();

        if (isset($data[JSONConstants::JSON_OBJECTINFOLDERCONTAINER_CHILDREN])) {
            foreach ((array) $data[JSONConstants::JSON_OBJECTINFOLDERCONTAINER_CHILDREN] as $childData) {
                $child = $this->convertDescendant($childData);

                if ($child !== null) {
                    $children[] = $child;
                }
            }
        }

        $objectInFolderContainer->setChildren($children);

        $objectInFolderContainer->setExtensions(
            $this->convertExtension($data, JSONConstants::getObjectInFolderContainerKeys())
        );

        return $objectInFolderContainer;
    }
}
