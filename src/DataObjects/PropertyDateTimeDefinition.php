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

use Dkd\PhpCmis\Definitions\MutablePropertyDateTimeDefinitionInterface;
use Dkd\PhpCmis\Enum\DateTimeResolution;

/**
 * DateTime property definition data implementation.
 */
class PropertyDateTimeDefinition extends AbstractPropertyDefinition implements
    MutablePropertyDateTimeDefinitionInterface
{
    /**
     * @var DateTimeResolution
     */
    protected $dateTimeResolution;

    /**
     * Sets which datetime resolution is supported by this property.
     *
     * @param DateTimeResolution $dateTimeResolution
     */
    public function setDateTimeResolution(DateTimeResolution $dateTimeResolution)
    {
        $this->dateTimeResolution = $dateTimeResolution;
    }

    /**
     * Returns which datetime resolution is supported by this property.
     *
     * @return DateTimeResolution
     */
    public function getDateTimeResolution()
    {
        return $this->dateTimeResolution;
    }
}
