<?php
namespace Dkd\PhpCmis;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Data\ExtensionsDataInterface;

/**
 * Access Control List (ACL)
 */
interface AclInterface extends ExtensionsDataInterface
{
    /**
     * Returns the list of Access Control Entries (ACEs).
     *
     * @return AceInterface[]
     */
    public function getAces();

    /**
     * Indicates whether this ACL expresses all permissions of the object.
     *
     * @return boolean
     */
    public function isExact();
}
