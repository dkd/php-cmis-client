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
 * Mutable Relationship type definition.
 */
interface MutableRelationshipTypeDefinitionInterface extends
    MutableTypeDefinitionInterface,
    RelationshipTypeDefinitionInterface
{
    /**
     * Sets the list of type IDs that are allowed as source objects.
     *
     * @param string[] $allowedSourceTypes of type IDs or <code>array()</code> if all types are allowed
     */
    public function setAllowedSourceTypeIds(array $allowedSourceTypes);

    /**
     * Sets the list of type IDs that are allowed as target objects.
     *
     * @param string[] $allowedTargetTypes of type IDs or <code>array()</code> if all types are allowed
     */
    public function setAllowedTargetTypeIds(array $allowedTargetTypes);
}
