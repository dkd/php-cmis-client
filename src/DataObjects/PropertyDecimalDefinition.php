<?php
namespace Dkd\PhpCmis\DataObjects;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Definitions\MutablePropertyDecimalDefinitionInterface;
use Dkd\PhpCmis\Enum\DecimalPrecision;

/**
 * Decimal property definition data implementation.
 */
class PropertyDecimalDefinition extends AbstractPropertyDefinition implements MutablePropertyDecimalDefinitionInterface
{
    /**
     * @var integer|null
     */
    protected $maxValue;

    /**
     * @var integer|null
     */
    protected $minValue;

    /**
     * @var DecimalPrecision|null
     */
    protected $precision;

    /**
     * Returns the precision this decimal.
     */
    public function getPrecision()
    {
        return $this->precision;
    }

    /**
     * Sets the precision this decimal.
     *
     * @param DecimalPrecision|null $precision the precision or <code>null</code> if the decimal supports any value
     */
    public function setPrecision(DecimalPrecision $precision = null)
    {
        $this->precision = $precision;
    }

    /**
     * Returns the minimum value of this decimal.
     */
    public function getMinValue()
    {
        return $this->minValue;
    }

    /**
     * Set the minimum value of this decimal.
     *
     * @param float $minValue the minimum value or <code>null</code> if no limit is specified
     */
    public function setMinValue($minValue)
    {
        $this->minValue = $this->castValueToSimpleType('integer', $minValue);
    }

    /**
     * Returns the maximum value of this decimal.
     */
    public function getMaxValue()
    {
        return $this->maxValue;
    }

    /**
     * Sets the maximum value of this decimal.
     *
     * @param float $maxValue the maximum value or <code>null</code> if no limit is specified
     */
    public function setMaxValue($maxValue)
    {
        $this->maxValue = $this->castValueToSimpleType('integer', $maxValue);
    }
}
