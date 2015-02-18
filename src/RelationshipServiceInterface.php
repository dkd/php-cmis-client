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

use Dkd\PhpCmis\Data\ExtensionDataInterface;
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
     * @param string $repositoryId The identifier for the repository.
     * @param string $objectId The identifier of the object.
     * @param boolean $includeSubRelationshipTypes If <code>true</code>, then the repository MUST return all
     *      relationships whose object-types are descendant-types of the object-type specified by the typeId parameter
     *      value as well as relationships of the specified type.
     *      If <code>false</code>, then the repository MUST only return relationships whose object-types is
     *      equivalent to the object-type specified by the typeId parameter value.
     *      If the typeId input is not specified, then this input MUST be ignored.
     * @param RelationshipDirection|null $relationshipDirection Specifying whether the repository MUST return
     *      relationships where the specified object is the source of the relationship, the target of the relationship,
     *      or both. (default is source)
     * @param string|null $typeId If specified, then the repository MUST return only relationships whose object-type is
     *      of the type specified. See also parameter includeSubRelationshipTypes.
     *      If not specified, then the repository MUST return relationship objects of all types.
     * @param string|null $filter a comma-separated list of query names that defines which properties
     *      must be returned by the repository (default is repository specific)
     * @param boolean $includeAllowableActions
     * @param integer|null $maxItems the maximum number of items to return in a response
     *      (default is repository specific)
     * @param integer $skipCount number of potential results that the repository MUST skip/page over before
     *      returning any results (default is 0)
     * @param ExtensionDataInterface|null $extension
     * @return ObjectListInterface
     */
    public function getObjectRelationships(
        $repositoryId,
        $objectId,
        $includeSubRelationshipTypes = false,
        RelationshipDirection $relationshipDirection = null,
        $typeId = null,
        $filter = null,
        $includeAllowableActions = false,
        $maxItems = null,
        $skipCount = 0,
        ExtensionDataInterface $extension = null
    );
}
