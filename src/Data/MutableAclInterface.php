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

/**
 * Mutable Access Control List (ACL)
 */
interface MutableAclInterface extends AclInterface
{
    /**
     * @param AceInterface[] $aces
     */
    public function __construct(array $aces);

    /**
     * Set the list of Access Control Entries (ACEs).
     *
     * @param AceInterface[] $aces the list of ACEs
     */
    public function setAces(array $aces);

    /**
     * Set whether this ACL expresses all permissions of the object.
     *
     * @param boolean $isExact {@code true} if the ACL expresses the exact permission set,
     *         {@code false} if there are other permission rules that cannot be
     *         expressed through ACEs, and {@code null} if this in unknown (the
     *         repository did not provide this information)
     */
    public function setIsExact($isExact);
}
