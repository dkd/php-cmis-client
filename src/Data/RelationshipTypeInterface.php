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
 * Relationship Object Type.
 */
interface RelationshipTypeInterface extends ObjectTypeInterface
{
    /**
     * Get the list of object types, allowed as source for relationships of this
     * type.
     *
     * @return ObjectTypeInterface[] the allowed source types for this relationship type
     */
    public function getAllowedSourceTypes();

    /**
     * Get the list of object types, allowed as target for relationships of this
     * type.
     *
     * @return ObjectTypeInterface[] the allowed target types for this relationship type
     */
    public function getAllowedTargetTypes();
}
