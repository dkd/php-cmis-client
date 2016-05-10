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

use Dkd\PhpCmis\Data\MutablePropertyIntegerInterface;

/**
 * Integer property data implementation.
 */
class PropertyInteger extends AbstractPropertyData implements MutablePropertyIntegerInterface
{
    /**
     * {@inheritdoc}
     *
     * @param integer[] $values
     */
    public function setValues(array $values)
    {

        foreach ($values as & $value) {
            if (PHP_INT_SIZE == 4 && is_double($value)) {
                //TODO: 32bit - handle this specially?
                $value = $this->castValueToSimpleType('integer', $value);
            }

            $this->checkType('integer', $value, true);
        }

        parent::setValues($values);
    }
}
