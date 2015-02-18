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
 * Represents a set of properties.
 */
interface PropertiesInterface extends ExtensionDataInterface
{
    /**
     * Returns a map of properties (property ID => property).
     *
     * This method should not be used with queries because some repositories don't set property IDs,
     * and because when dealing with queries the proper key is usually the query name
     * (when using JOINs, several properties with the same ID may be returned).
     *
     * @return PropertyDataInterface[] the map of properties, not <code>null</code>
     */
    public function getProperties();

    /**
     * Adds a property to the end of the property list.
     *
     * @param PropertyDataInterface $property the property
     */
    public function addProperty(PropertyDataInterface $property);

    /**
     * Removes a property.
     *
     * @param string $id the property ID
     */
    public function removeProperty($id);
}
