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

use Dkd\PhpCmis\Definitions\MutablePropertyIntegerDefinitionInterface;

/**
 * Integer property definition data implementation.
 */
class PropertyIntegerDefinition extends AbstractPropertyDefinition implements MutablePropertyIntegerDefinitionInterface
{
    /**
     * @var integer|null
     */
    protected $minValue;

    /**
     * @var integer|null
     */
    protected $maxValue;

    /**
     * @return integer|null the minimum value or <code>null</code> if no limit is specified
     */
    public function getMinValue()
    {
        return $this->minValue;
    }

    /**
     * @param integer $minValue the minimum value or <code>null</code> if no limit is specified
     */
    public function setMinValue($minValue)
    {
        $this->minValue = $this->castValueToSimpleType('integer', $minValue);
    }

    /**
     * @return integer|null the maximum value or <code>null</code> if no limit is specified
     */
    public function getMaxValue()
    {
        return $this->maxValue;
    }

    /**
     * @param integer $maxValue the maximum value or <code>null</code> if no limit is specified
     */
    public function setMaxValue($maxValue)
    {
        $this->maxValue = $this->castValueToSimpleType('integer', $maxValue);
    }
}
