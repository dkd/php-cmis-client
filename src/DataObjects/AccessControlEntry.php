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

use Dkd\PhpCmis\Data\MutableAceInterface;
use Dkd\PhpCmis\PrincipalInterface;

/**
 * Access control entry data implementation.
 */
class AccessControlEntry extends AbstractExtensionData implements MutableAceInterface
{
    /**
     * @var string[]
     */
    protected $permissions = array();

    /**
     * @var PrincipalInterface
     */
    protected $principal;

    /**
     * @var boolean
     */
    protected $isDirect;

    /**
     * @param PrincipalInterface $principal
     * @param string[] $permissions
     */
    public function __construct(PrincipalInterface $principal, array $permissions)
    {
        $this->setPrincipal($principal);
        $this->setPermissions($permissions);
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
        foreach ($permissions as $permission) {
            $this->checkType('string', $permission);
        }
        $this->permissions = $permissions;
    }

    /**
     * @return PrincipalInterface
     */
    public function getPrincipal()
    {
        return $this->principal;
    }

    /**
     * @param PrincipalInterface $principal
     */
    public function setPrincipal(PrincipalInterface $principal)
    {
        $this->principal = $principal;
    }

    /**
     * Returns the ACE principal id.
     *
     * @return string
     */
    public function getPrincipalId()
    {
        return $this->getPrincipal()->getId();
    }

    /**
     * @return boolean
     */
    public function isDirect()
    {
        return $this->isDirect;
    }

    /**
     * @param boolean $isDirect
     */
    public function setIsDirect($isDirect)
    {
        $this->isDirect = $this->castValueToSimpleType('boolean', $isDirect);
    }
}
