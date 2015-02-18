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

/**
 * Change event in the change log.
 */
interface ChangeEventInterface extends Data\ChangeEventInfoInterface
{
    /**
     * Returns the ACL.
     *
     * @return AclInterface the ACL
     */
    public function getAcl();

    /**
     * Gets the ID of the object.
     *
     * @return string the object ID, not <code>null</code>
     */
    public function getObjectId();

    /**
     * Returns the policy IDs.
     *
     * @return string[] the policy IDs
     */
    public function getPolicyIds();

    /**
     * Returns the properties.
     *
     * @return array
     */
    public function getProperties();
}
