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

use Dkd\PhpCmis\Enum\DateTimeResolution;

/**
 * Mutable Property definition of a datetime property.
 */
interface MutablePropertyDateTimeDefinitionInterface extends
    MutablePropertyDefinitionInterface,
    PropertyDateTimeDefinitionInterface
{
    /**
     * Sets which datetime resolution is supported by this property.
     *
     * @param DateTimeResolution $dateTimeResolution
     */
    public function setDateTimeResolution(DateTimeResolution $dateTimeResolution);
}
