<?php
namespace Dkd\PhpCmis\Data;

use Dkd\PhpCmis\Enum\PropertyType;

/**
 * Holds the set of property data types for type creation and update.
 */
interface CreatablePropertyTypes extends ExtensionsDataInterface
{
    /**
     * Returns the set of property data types that can used to create or update a type definition.
     *
     * @return PropertyType[] the available set of property data types
     */
    public function canCreate();
}
