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

use Dkd\PhpCmis\Data\AclCapabilitiesInterface;
use Dkd\PhpCmis\Data\PermissionMappingInterface;
use Dkd\PhpCmis\Definitions\PermissionDefinitionInterface;
use Dkd\PhpCmis\Enum\AclPropagation;
use Dkd\PhpCmis\Enum\SupportedPermissions;

/**
 * ACL Capabilities.
 */
class AclCapabilities extends AbstractExtensionData implements AclCapabilitiesInterface
{
    /**
     * @var SupportedPermissions
     */
    protected $supportedPermissions;

    /**
     * @var AclPropagation
     */
    protected $aclPropagation;

    /**
     * @var PermissionMappingInterface[]
     */
    protected $permissionMapping;

    /**
     * @var PermissionDefinitionInterface[]
     */
    protected $permissions;

    /**
     * @return AclPropagation
     */
    public function getAclPropagation()
    {
        return $this->aclPropagation;
    }

    /**
     * @param AclPropagation $aclPropagation
     */
    public function setAclPropagation(AclPropagation $aclPropagation)
    {
        $this->aclPropagation = $aclPropagation;
    }

    /**
     * @param PermissionDefinitionInterface[] $permissionDefinitionList
     */
    public function setPermissions(array $permissionDefinitionList)
    {
        foreach ($permissionDefinitionList as $permissionDefinition) {
            $this->checkType('\\Dkd\\PhpCmis\\Definitions\\PermissionDefinitionInterface', $permissionDefinition);
        }
        $this->permissions = $permissionDefinitionList;
    }

    /**
     * @return PermissionDefinitionInterface[]
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @return PermissionMappingInterface[]
     */
    public function getPermissionMapping()
    {
        return $this->permissionMapping;
    }

    /**
     * @param PermissionMappingInterface[] $permissionMapping
     */
    public function setPermissionMapping(array $permissionMapping)
    {
        foreach ($permissionMapping as $permissionMappingItem) {
            $this->checkType('\\Dkd\\PhpCmis\\Data\\PermissionMappingInterface', $permissionMappingItem);
        }
        $this->permissionMapping = $permissionMapping;
    }

    /**
     * @return SupportedPermissions
     */
    public function getSupportedPermissions()
    {
        return $this->supportedPermissions;
    }

    /**
     * @param SupportedPermissions $supportedPermissions
     */
    public function setSupportedPermissions(SupportedPermissions $supportedPermissions)
    {
        $this->supportedPermissions = $supportedPermissions;
    }
}
