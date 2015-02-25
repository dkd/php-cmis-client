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
 * Acl.
 */
interface AclInterface extends ExtensionDataInterface
{
    /**
     * Returns the list of Access Control Entries (ACEs).
     *
     * @return AceInterface[] the list of ACEs, not <code>null</code>
     */
    public function getAces();

    /**
     * Indicates whether this ACL expresses all permissions of the object.
     *
     * @return boolean <code>true</code> if the ACL expresses the exact permission set,
     *         <code>false</code> if there are other permission rules that cannot be
     *         expressed through ACEs, and <code>null</code> if this in unknown (the
     *         repository did not provide this information)
     */
    public function isExact();
}
