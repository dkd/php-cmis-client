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

use Dkd\PhpCmis\PrincipalInterface;

/**
 * Mutable Access Control Entry (ACE)
 */
interface MutableAceInterface extends AceInterface
{
    /**
     * @param PrincipalInterface $principal
     * @param string[] $permissions
     */
    public function __construct(PrincipalInterface $principal, array $permissions);

    /**
     * Set the permissions granted to the principal.
     *
     * @param string[] $permissions
     */
    public function setPermissions(array $permissions);

    /**
     * Set the ACE principal.
     *
     * @param PrincipalInterface $principal
     */
    public function setPrincipal(PrincipalInterface $principal);

    /**
     * Set if the ACE was directly applied to the object or has been
     * inherited from another object (for example from the folder it resides in).
     *
     * @param boolean $isDirect
     */
    public function setIsDirect($isDirect);
}
