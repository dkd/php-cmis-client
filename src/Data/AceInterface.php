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
 * Access Control Entry (ACE)
 */
interface AceInterface extends ExtensionDataInterface
{
    /**
     * Returns the permissions granted to the principal.
     *
     * @return string[]
     */
    public function getPermissions();

    /**
     * Returns the ACE principal.
     *
     * @return PrincipalInterface
     */
    public function getPrincipal();

    /**
     * Returns the ACE principal id.
     *
     * @return string
     */
    public function getPrincipalId();

    /**
     * Indicates if the ACE was directly applied to the object or has been
     * inherited from another object (for example from the folder it resides in).
     *
     * @return boolean
     */
    public function isDirect();
}
