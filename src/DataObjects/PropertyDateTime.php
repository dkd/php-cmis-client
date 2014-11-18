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

use Dkd\PhpCmis\Data\MutablePropertyDateTimeInterface;

/**
 * DateTime property data implementation.
 */
class PropertyDateTime extends AbstractPropertyData implements MutablePropertyDateTimeInterface
{
    /**
     * {@inheritdoc}
     *
     * @param \DateTime[] $values
     */
    public function setValues(array $values)
    {
        foreach ($values as $value) {
            $this->checkType('\\DateTime', $value, true);
        }
        parent::setValues($values);
    }
}
