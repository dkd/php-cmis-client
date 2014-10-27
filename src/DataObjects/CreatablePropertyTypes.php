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

use Dkd\PhpCmis\Data\CreatablePropertyTypesInterface;
use Dkd\PhpCmis\Enum\PropertyType;

/**
 * Repository info data implementation.
 */
class CreatablePropertyTypes extends AbstractExtensionData implements CreatablePropertyTypesInterface
{
    /**
     * @var PropertyType[]
     */
    protected $propertyTypeSet = array();

    /**
     * Returns the set of property data types that can used to create or update a type definition.
     *
     * @return PropertyType[] the available set of property data types
     */
    public function canCreate()
    {
        return $this->propertyTypeSet;
    }

    /**
     * @param PropertyType[] $propertyTypeSet
     */
    public function setCanCreate(array $propertyTypeSet)
    {
        foreach ($propertyTypeSet as $propertyType) {
            $this->checkType('\\Dkd\\PhpCmis\\Enum\\PropertyType', $propertyType);
        }

        $this->propertyTypeSet = $propertyTypeSet;
    }
}
