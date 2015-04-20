<?php
namespace Dkd\PhpCmis\Definitions;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Enum\DecimalPrecision;

/**
 * Mutable Property definition of a decimal property.
 */
interface MutablePropertyDecimalDefinitionInterface extends
    MutablePropertyDefinitionInterface,
    PropertyDecimalDefinitionInterface
{
    /**
     * Sets the maximum value of this decimal.
     *
     * @param float $maxValue the maximum value or <code>null</code> if no limit is specified
     */
    public function setMaxValue($maxValue);

    /**
     * Sets the minimum value of this decimal.
     *
     * @param float $minValue the minimum value or <code>null</code> if no limit is specified
     */
    public function setMinValue($minValue);

    /**
     * Sets the precision of this decimal.
     *
     * @param DecimalPrecision|null $decimalPrecision the precision or <code>null</code> if the decimal supports any
     *      value
     */
    public function setPrecision(DecimalPrecision $decimalPrecision = null);
}
