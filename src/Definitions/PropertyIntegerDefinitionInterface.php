<?php
namespace Dkd\PhpCmis\Definitions;

/**
 * Property definition of an integer property.
 */
interface PropertyIntegerDefinitionInterface extends PropertyDefinitionInterface
{
    /**
     * Returns the max value of this integer.
     *
     * @return int|null the max value or null if no limit is specified
     */
    public function getMaxValue();

    /**
     * Returns the min value of this integer.
     *
     * @return int|null the min value or null if no limit is specified
     */
    public function getMinValue();
}
