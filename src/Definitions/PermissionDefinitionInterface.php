<?php
namespace Dkd\PhpCmis\Definitions;

use Dkd\PhpCmis\Data\ExtensionsDataInterface;

/**
 * Permission definition.
 */
interface PermissionDefinitionInterface extends ExtensionsDataInterface
{
    /**
     * Returns a human readable description of the permission.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Returns the permission ID.
     *
     * @return string
     */
    public function getId();
}
