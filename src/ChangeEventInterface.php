<?php
namespace Dkd\PhpCmis;

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
     * @return string the object ID, not null
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
