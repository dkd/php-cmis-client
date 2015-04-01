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

use Dkd\PhpCmis\Data\AllowableActionsInterface;
use Dkd\PhpCmis\Data\PropertyDataInterface;
use Dkd\PhpCmis\Data\RelationshipInterface;
use Dkd\PhpCmis\Data\RenditionInterface;

/**
 * Query Result.
 */
interface QueryResultInterface
{
    /**
     * Returns the allowable actions if they have been requested.
     *
     * @return AllowableActionsInterface|null the allowable actions if they have been requested, <code>null</code>
     *      otherwise
     */
    public function getAllowableActions();

    /**
     * Returns the list of all properties in this query result.
     *
     * @return PropertyDataInterface[] all properties, not <code>null</code>
     */
    public function getProperties();

    /**
     * Returns a property by ID.
     *
     * Because repositories are not obligated to add property IDs to their query result properties,
     * this method might not always work as expected with some repositories. Use getPropertyByQueryName(String) instead.
     *
     * @param string $id
     * @return PropertyDataInterface|null the property or <code>null</code> if the property doesn't
     * exist or hasn't been requested
     */
    public function getPropertyById($id);

    /**
     * Returns a property by query name or alias.
     *
     * @param string $queryName the property query name or alias
     * @return PropertyDataInterface|null the property or <code>null</code> if the property doesn't exist
     * or hasn't been requested
     */
    public function getPropertyByQueryName($queryName);

    /**
     * Returns a property multi-value by ID.
     *
     * @param string $id the property ID
     * @return array|null the property value or <code>null</code> if the property doesn't exist, hasn't been requested,
     * or the property value isn't set
     */
    public function getPropertyMultivalueById($id);

    /**
     * Returns a property multi-value by query name or alias.
     *
     * @param string $queryName the property query name or alias
     * @return array|null the property value or <code>null</code> if the property doesn't exist, hasn't been requested,
     * or the property value isn't set
     */
    public function getPropertyMultivalueByQueryName($queryName);

    /**
     * Returns a property (single) value by ID.
     *
     * @param string $id the property ID
     * @return mixed
     */
    public function getPropertyValueById($id);

    /**
     * Returns a property (single) value by query name or alias.
     *
     * @param string $queryName the property query name or alias
     * @return mixed the property value or <code>null</code> if the property doesn't exist, hasn't been requested,
     * or the property value isn't set
     */
    public function getPropertyValueByQueryName($queryName);

    /**
     * Returns the relationships if they have been requested.
     *
     * @return RelationshipInterface[]
     */
    public function getRelationships();

    /**
     * Returns the renditions if they have been requested.
     *
     * @return RenditionInterface[]
     */
    public function getRenditions();
}
