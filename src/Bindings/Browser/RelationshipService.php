<?php
namespace Dkd\PhpCmis\Bindings\Browser;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Data\ExtensionDataInterface;
use Dkd\PhpCmis\Data\ObjectListInterface;
use Dkd\PhpCmis\Enum\RelationshipDirection;
use Dkd\PhpCmis\RelationshipServiceInterface;

/**
 * Relationship Service Browser Binding client.
 */
class RelationshipService extends AbstractBrowserBindingService implements RelationshipServiceInterface
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
     * @param integer $maxItems
     * @param integer $skipCount
     * @param ExtensionDataInterface $extension
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
        ExtensionDataInterface $extension
    ) {
        // TODO: Implement getObjectRelationships() method.
    }
}
