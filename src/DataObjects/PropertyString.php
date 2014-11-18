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

use Dkd\PhpCmis\Data\PropertyStringInterface;

/**
 * String property data implementation.
 */
class PropertyString extends AbstractPropertyData implements PropertyStringInterface
{
    /**
     * {@inheritdoc}
     *
     * @param string[] $values
     */
    public function setValues(array $values)
    {
        foreach ($values as $value) {
            $this->checkType('string', $value, true);
        }
        parent::setValues($values);
    }
}
