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

use Dkd\PhpCmis\Data\MutablePropertyIdInterface;

/**
 * Id property data implementation.
 */
class PropertyId extends PropertyString implements MutablePropertyIdInterface
{
    /**
     * {@inheritdoc}
     *
     * @param string[] $values
     */
    public function setValues(array $values)
    {
        // currently no special behavior here. It's just a string value.
        parent::setValues($values);
    }
}
