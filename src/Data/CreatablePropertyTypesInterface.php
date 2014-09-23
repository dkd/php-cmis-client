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

use Dkd\PhpCmis\Enum\PropertyType;

/**
 * Holds the set of property data types for type creation and update.
 */
interface CreatablePropertyTypesInterface extends ExtensionDataInterface
{
    /**
     * Returns the set of property data types that can used to create or update a type definition.
     *
     * @return PropertyType[] the available set of property data types
     */
    public function canCreate();
}
