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
 * Holder for bulkUpdateObject data.
 */
interface BulkUpdateObjectIdAndChangeTokenInterface extends ExtensionDataInterface
{
    /**
     * Returns the change token of the object.
     *
     * @return string the change token or <code>null</code> if the repository does not support change tokens
     */
    public function getChangeToken();

    /**
     * Returns the object ID.
     *
     * @return string the object ID
     */
    public function getId();

    /**
     * Returns the new object ID if the repository created a new object during the update.
     *
     * @return string the new object ID or <code>null</code> if no new object has been created
     */
    public function getNewId();
}
