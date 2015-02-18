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

/**
 * Mutable Property definition of an integer property.
 */
interface MutablePropertyIntegerDefinitionInterface extends
    MutablePropertyDefinitionInterface,
    PropertyIntegerDefinitionInterface
{
    /**
     * Sets the maximum value of this integer.
     *
     * @param integer $maxValue the maximum value or <code>null</code> if no limit is specified
     */
    public function setMaxValue($maxValue);

    /**
     * Sets the minimum value of this integer.
     *
     * @param integer $minValue the minimum value or <code>null</code> if no limit is specified
     */
    public function setMinValue($minValue);
}
