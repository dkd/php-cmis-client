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

/**
 * Mutable Property definition of an string property.
 */
interface MutablePropertyStringDefinitionInterface extends
    MutablePropertyDefinitionInterface,
    PropertyStringDefinitionInterface
{
    /**
     * Sets the max length of the string.
     *
     * @param integer $maxLength the max string length in characters or <code>null</code> if the the length is not
     *      limited
     */
    public function setMaxLength($maxLength);
}
