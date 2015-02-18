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

use Dkd\PhpCmis\Data\PropertiesInterface;
use Dkd\PhpCmis\Data\PropertyDataInterface;

/**
 * Properties data implementation.
 */
class Properties extends AbstractExtensionData implements PropertiesInterface
{
    /**
     * @var PropertyDataInterface[]
     */
    protected $properties = array();

    /**
     * Returns a map of properties (property ID => property).
     *
     * This method should not be used with queries because some repositories don't set property IDs,
     * and because when dealing with queries the proper key is usually the query name
     * (when using JOINs, several properties with the same ID may be returned).
     *
     * @return PropertyDataInterface[] the map of properties, not <code>null</code>
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Adds a property with propertyId as index. Existing property with same id will be replaced.
     *
     * @param PropertyDataInterface $property the property
     */
    public function addProperty(PropertyDataInterface $property)
    {
        $this->properties[$property->getId()] = $property;
    }

    /**
     * Adds a list of properties with propertyId as index. Existing property with same id will be replaced.
     *
     * @param PropertyDataInterface[] $properties
     */
    public function addProperties(array $properties)
    {
        foreach ($properties as $property) {
            $this->addProperty($property);
        }
    }

    /**
     * Removes a property.
     *
     * @param string $id the property ID
     */
    public function removeProperty($id)
    {
        if (isset($this->properties[$id])) {
            unset($this->properties[$id]);
        }
    }
}
