<?php
namespace Dkd\PhpCmis;

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
