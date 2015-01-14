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

/**
 * Relationship type definition.
 */
interface RelationshipTypeDefinitionInterface extends TypeDefinitionInterface
{
    /**
     * Returns the list of type IDs that are allowed as source objects.
     *
     * @return string[] of type IDs or <code>array()</code> if all types are allowed
     */
    public function getAllowedSourceTypeIds();

    /**
     * Returns the list of type IDs that are allowed as target objects.
     *
     * @return string[] of type IDs or <code>array()</code> if all types are allowed
     */
    public function getAllowedTargetTypeIds();
}
