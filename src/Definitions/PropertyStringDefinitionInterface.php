<?php
namespace Dkd\PhpCmis\Definitions;

/**
 * Property definition of an string property.
 */
interface PropertyStringDefinitionInterface extends PropertyDefinitionInterface
{
    /**
     * Returns the max length of the string.
     *
     * @return int|null the max string length in characters or null if the the length is not limited
     */
    public function getMaxLength();
}
