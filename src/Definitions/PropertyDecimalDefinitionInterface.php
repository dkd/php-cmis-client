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
 * Property definition of a decimal property.
 */
interface PropertyDecimalDefinitionInterface extends PropertyDefinitionInterface
{
    /**
     * Returns the maximum value of this decimal.
     *
     * @return float|null the maximum value or <code>null</code> if no limit is specified
     */
    public function getMaxValue();

    /**
     * Returns the minimum value of this decimal.
     *
     * @return float|null the minimum value or <code>null</code> if no limit is specified
     */
    public function getMinValue();

    /**
     * Returns the precision this decimal.
     *
     * @return DecimalPrecision|null the precision or <code>null</code> if the decimal supports any value
     */
    public function getPrecision();
}
