<?php
namespace Dkd\PhpCmis\Data;

/**
 * Holder for bulkUpdateObject data.
 */
interface BulkUpdateObjectIdAndChangeTokenInterface extends ExtensionsDataInterface
{
    /**
     * Returns the change token of the object.
     *
     * @return string the change token or null if the repository does not support change tokens
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
     * @return string the new object ID or null if no new object has been created
     */
    public function getNewId();
}
