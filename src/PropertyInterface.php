<?php
namespace Dkd\PhpCmis;

use Dkd\PhpCmis\Data\PropertyDataInterface;
use Dkd\PhpCmis\Definitions\PropertyDefinitionInterface;
use Dkd\PhpCmis\Enum\PropertyType;

/**
 * CMIS Property.
 */
interface PropertyInterface extends PropertyDataInterface
{
    /**
     * Returns the property definition.
     *
     * @return PropertyDefinitionInterface the property definition, not null
     */
    public function getDefinition();

    /**
     * Returns the property data type.
     *
     * @return PropertyType the data type of the property, not null
     */
    public function getType();

    /**
     * Returns the property value (single or multiple).
     *
     * @return mixed he property value or null if the property value isn't set
     */
    public function getValue();

    /**
     * Returns a human readable representation of the property value.
     * If the property is multi-value property, only the first value will be returned.
     *
     * @return string the (first) property value as a string or null if the property value isn't set
     */
    public function getValueAsString();

    /**
     * Returns a human readable representation of the property values.
     *
     * @return string the property value as a string or null if the property value isn't set
     */
    public function getValuesAsString();

    /**
     * Returns if the property is a multi-value property.
     *
     * @return boolean true if the property is multi-value property, false if the property is single-value property
     */
    public function isMultiValued();
}
