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

use Dkd\PhpCmis\Definitions\MutablePropertyStringDefinitionInterface;

/**
 * String property definition data implementation.
 */
class PropertyStringDefinition extends AbstractPropertyDefinition implements MutablePropertyStringDefinitionInterface
{
    /**
     * @var integer
     */
    protected $maxLength;

    /**
     * Returns the maximum length of the string.
     *
     * @return integer|null the maximum string length in characters or <code>null</code>
     * if the the length is not limited
     */
    public function getMaxLength()
    {
        return $this->maxLength;
    }

    /**
     * Sets the maximum length of the string.
     *
     * @param integer $maxLength the maximum string length in characters or <code>null</code>
     * if the the length is not limited
     */
    public function setMaxLength($maxLength)
    {
        $this->maxLength = $this->castValueToSimpleType('integer', $maxLength);
    }
}
