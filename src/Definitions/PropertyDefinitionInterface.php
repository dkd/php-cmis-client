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
 * Base property definition interface.
 */
interface PropertyDefinitionInterface
{
    /**
     * Returns the cardinality.
     *
     * @return Cardinality the cardinality
     */
    public function getCardinality();

    /**
     * Returns the choices for this property.
     *
     * @return ChoiceInterface[]
     */
    public function getChoices();

    /**
     * Returns the default value.
     *
     * @return mixed
     */
    public function getDefaultValue();

    /**
     * Returns the property description.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Returns the display name.
     *
     * @return string
     */
    public function getDisplayName();

    /**
     * Returns the property definition ID.
     *
     * @return string
     */
    public function getId();

    /**
     * Returns the local name.
     *
     * @return string
     */
    public function getLocalName();

    /**
     * Returns the local namespace.
     *
     * @return string
     */
    public function getLocalNamespace();

    /**
     * Returns the property type.
     *
     * @return PropertyType
     */
    public function getPropertyType();

    /**
     * Returns the query name
     *
     * @return string
     */
    public function getQueryName();

    /**
     * Returns the updatability.
     *
     * @return Updatability
     */
    public function getUpdatability();

    /**
     * Returns if the property is inherited by a parent type.
     *
     * @return boolean
     */
    public function isInherited();

    /**
     * Returns if the property supports open choice.
     *
     * @return boolean
     */
    public function isOpenChoice();

    /**
     * Returns if the property is Orderable.
     *
     * @return boolean
     */
    public function isOrderable();

    /**
     * Returns if the property is queryable.
     *
     * @return boolean
     */
    public function isQueryable();

    /**
     * Returns if the property is required.
     *
     * @return boolean
     */
    public function isRequired();
}
