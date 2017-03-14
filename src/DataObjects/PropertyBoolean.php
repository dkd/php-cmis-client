<?php
namespace Dkd\PhpCmis\DataObjects;

/*
 * This file is part of php-cmis-client.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Data\MutablePropertyBooleanInterface;

/**
 * Boolean property data implementation.
 */
class PropertyBoolean extends AbstractPropertyData implements MutablePropertyBooleanInterface
{
    /**
     * {@inheritdoc}
     *
     * @param boolean[] $values
     */
    public function setValues(array $values)
    {
        foreach ($values as $value) {
            $this->checkType('boolean', $value, true);
        }
        parent::setValues($values);
    }
}
