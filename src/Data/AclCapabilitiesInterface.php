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

use Dkd\PhpCmis\Definitions\PermissionDefinitionInterface;
use Dkd\PhpCmis\Enum\AclPropagation;
use Dkd\PhpCmis\Enum\SupportedPermissions;

/**
 * Acl Capabilities.
 */
interface AclCapabilitiesInterface extends ExtensionDataInterface
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
