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

/**
 * Mutable PropertyData.
 */
interface MutablePropertyDataInterface extends PropertyDataInterface
{
    /**
     * Sets the property ID.
     *
     * @param string $id the property ID, should not be <code>null</code>
     */
    public function setId($id);

    /**
     * Set the display name.
     *
     * @param string $displayName the display name
     */
    public function setDisplayName($displayName);

    /**
     * Set the local name.
     *
     * @param string $localName the local name
     */
    public function setLocalName($localName);

    /**
     * Set the query name.
     *
     * @param string $queryName the query name
     */
    public function setQueryName($queryName);

    /**
     * Sets the property value.
     *
     * If this property is a single value property, this list must either be
     * empty or <code>null</code> (= unset) or must only contain one entry.
     *
     * @param array $values the property values
     */
    public function setValues(array $values);

    /**
     * Sets a property value.
     *
     * If this property is a multi value property, this value becomes the only
     * value in the list of values.
     *
     * @param mixed $value the property value or <code>null</code> to unset the property
     */
    public function setValue($value);
}
