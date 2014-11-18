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

use Dkd\PhpCmis\Data\MutablePropertyDecimalInterface;

/**
 * Decimal property data implementation.
 */
class PropertyDecimal extends AbstractPropertyData implements MutablePropertyDecimalInterface
{
    /**
     * {@inheritdoc}
     *
     * @param float[] $values
     */
    public function setValues(array $values)
    {
        foreach ($values as $key => $value) {
            if (is_integer($value)) {
                // cast integer values silenty to a double value.
                $values[$key] = $value = (double) $value;
            }
            $this->checkType('double', $value, true);
        }
        parent::setValues($values);
    }
}
