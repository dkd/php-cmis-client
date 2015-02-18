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
 * Property definition of an integer property.
 */
interface PropertyIntegerDefinitionInterface extends PropertyDefinitionInterface
{
    /**
     * Returns the maximum value of this integer.
     *
     * @return integer|null the maximum value or <code>null</code> if no limit is specified
     */
    public function getMaxValue();

    /**
     * Returns the minimum value of this integer.
     *
     * @return integer|null the minimum value or <code>null</code> if no limit is specified
     */
    public function getMinValue();
}
