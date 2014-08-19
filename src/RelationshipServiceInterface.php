<?php
namespace Dkd\PhpCmis;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Data\ExtensionsDataInterface;
use Dkd\PhpCmis\Data\ObjectListInterface;
use Dkd\PhpCmis\Enum\RelationshipDirection;

/**
 * Relationship Service interface.
 *
 * See the CMIS 1.0 and CMIS 1.1 specifications for details on the operations,
 * parameters, exceptions and the domain model.
 */
interface RelationshipServiceInterface
{
    /**
     * Gets all or a subset of relationships associated with an independent object.
     *
     * @param string $repositoryId
     * @param string $objectId
     * @param boolean $includeSubRelationshipTypes
     * @param RelationshipDirection $relationshipDirection
     * @param string $typeId
     * @param string $filter
     * @param boolean $includeAllowableActions
     * @param int $maxItems
     * @param int $skipCount
     * @param ExtensionsDataInterface $extension
     * @internal param $String $
     * @return ObjectListInterface
     */
    public function getObjectRelationships(
        $repositoryId,
        $objectId,
        $includeSubRelationshipTypes,
        RelationshipDirection $relationshipDirection,
        $typeId,
        $filter,
        $includeAllowableActions,
        $maxItems,
        $skipCount,
        ExtensionsDataInterface $extension
    );
}
