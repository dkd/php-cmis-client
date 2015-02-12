<?php
namespace Dkd\PhpCmis\Data;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Definitions\PropertyDefinitionInterface;
use Dkd\PhpCmis\Enum\PropertyType;

/**
 * CMIS Property.
 */
interface PropertyInterface extends PropertyDataInterface
{
    /**
     * Initialize the property with its definition and values
     *
     * @param PropertyDefinitionInterface $propertyDefinition
     * @param mixed[] $values
     */
    public function __construct(PropertyDefinitionInterface $propertyDefinition, array $values);

    /**
     * Returns if the property is a multi-value property.
     *
     * @return boolean <code>true</code> if the property is multi-value property, <code>false</code> if the property is
     *     single-value property,
     */
    public function isMultiValued();

    /**
     * Returns the property data type.
     *
     * @return PropertyType the data type of the property
     */
    public function getType();

    /**
     * Returns the property definition.
     *
     * @return PropertyDefinitionInterface the property definition
     */
    public function getDefinition();
}
