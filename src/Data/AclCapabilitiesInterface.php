<?php
namespace Dkd\PhpCmis\Data;

use Dkd\PhpCmis\Definitions\PermissionDefinitionInterface;
use Dkd\PhpCmis\Enum\AclPropagation;
use Dkd\PhpCmis\Enum\SupportedPermissions;

/**
 * Acl Capabilities.
 */
interface AclCapabilitiesInterface extends ExtensionsDataInterface
{
    /**
     * @return AclPropagation
     */
    public function getAclPropagation();

    /**
     * @return PermissionMappingInterface[]
     */
    public function getPermissionMapping();

    /**
     * @return PermissionDefinitionInterface[]
     */
    public function getPermissions();

    /**
     * @return SupportedPermissions
     */
    public function getSupportedPermissions();
}
