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

use Dkd\PhpCmis\Enum\Cardinality;
use Dkd\PhpCmis\Enum\PropertyType;
use Dkd\PhpCmis\Enum\Updatability;

/**
 * Mutable base property definition interface.
 */
interface MutablePropertyDefinitionInterface extends PropertyDefinitionInterface
{
    /**
     * Sets the cardinality.
     *
     * @param Cardinality $cardinality the cardinality
     */
    public function setCardinality(Cardinality $cardinality);

    /**
     * Sets the choices for this property.
     *
     * @param ChoiceInterface[] $choices
     */
    public function setChoices(array $choices);

    /**
     * Sets the default value.
     *
     * @param array $defaultValue
     */
    public function setDefaultValue(array $defaultValue);

    /**
     * Sets the property description.
     *
     * @param string $description
     */
    public function setDescription($description);

    /**
     * Sets the display name.
     *
     * @param string $displayName
     */
    public function setDisplayName($displayName);

    /**
     * Sets the property definition ID.
     *
     * @param string $id
     */
    public function setId($id);

    /**
     * Sets the local name.
     *
     * @param string $localName
     */
    public function setLocalName($localName);

    /**
     * Sets the local namespace.
     *
     * @param string $localNamespace
     */
    public function setLocalNamespace($localNamespace);

    /**
     * Sets the property type.
     *
     * @param PropertyType $propertyType
     */
    public function setPropertyType(PropertyType $propertyType);

    /**
     * Sets the query name
     *
     * @param string $queryName
     */
    public function setQueryName($queryName);

    /**
     * Sets the updatability.
     *
     * @param Updatability $updatability
     */
    public function setUpdatability(Updatability $updatability);

    /**
     * Sets if the property is inherited by a parent type.
     *
     * @param boolean $isInherited
     */
    public function setIsInherited($isInherited);

    /**
     * Sets if the property supports open choice.
     *
     * @param boolean $isOpenChoice
     */
    public function setIsOpenChoice($isOpenChoice);

    /**
     * Sets if the property is Orderable.
     *
     * @param boolean $isOrderable
     */
    public function setIsOrderable($isOrderable);

    /**
     * Sets if the property is queryable.
     *
     * @param boolean $isQueryable
     */
    public function setIsQueryable($isQueryable);

    /**
     * Sets if the property is required.
     *
     * @param boolean $isRequired
     */
    public function setIsRequired($isRequired);
}
