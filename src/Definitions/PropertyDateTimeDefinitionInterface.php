<?php
namespace Dkd\PhpCmis\Definitions;

use Dkd\PhpCmis\Enum\DateTimeResolution;

/**
 * Property definition of a datetime property.
 */
interface PropertyDateTimeDefinitionInterface extends PropertyDefinitionInterface
{
    /**
     * Returns which datetime resolution is supported by this property.
     *
     * @return DateTimeResolution
     */
    public function getDateTimeResolution();
}
