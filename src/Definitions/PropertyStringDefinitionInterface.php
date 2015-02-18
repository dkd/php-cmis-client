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
 * Property definition of an string property.
 */
interface PropertyStringDefinitionInterface extends PropertyDefinitionInterface
{
    /**
     * Returns the maximum length of the string.
     *
     * @return integer|null the maximum string length in characters or <code>null</code> if the the length is
     *      not limited
     */
    public function getMaxLength();
}
