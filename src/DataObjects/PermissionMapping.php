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

use Dkd\PhpCmis\Data\PermissionMappingInterface;

/**
 * Permission Mapping.
 */
class PermissionMapping extends AbstractExtensionData implements PermissionMappingInterface
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var string[]
     */
    protected $permissions = array();

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = (string) $key;
    }

    /**
     * @return string[]
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    /**
     * @param string[] $permissions
     */
    public function setPermissions(array $permissions)
    {
        // ensure that all array values are of type string
        $permissions = array_map('strval', $permissions);
        $this->permissions = $permissions;
    }
}
