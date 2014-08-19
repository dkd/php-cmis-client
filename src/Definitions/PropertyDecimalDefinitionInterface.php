<?php
namespace Dkd\PhpCmis\Definitions;

use Dkd\PhpCmis\Enum\DecimalPrecision;

/**
 * Property definition of a decimal property.
 */
interface PropertyDecimalDefinitionInterface extends PropertyDefinitionInterface
{
    /**
     * Returns the max value of this decimal.
     *
     * @return float|null the max value or null if no limit is specified
     */
    public function getMaxValue();

    /**
     * Returns the min value of this decimal.
     *
     * @return float|null the min value or null if no limit is specified
     */
    public function getMinValue();

    /**
     * Returns the precision this decimal.
     *
     * @return DecimalPrecision|null the precision or null if the decimal supports any value
     */
    public function getPrecision();
}
