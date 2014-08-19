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
