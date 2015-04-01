<?php
namespace Dkd\PhpCmis;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Dimitri Ebert <dimitri.ebert@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Data\AllowableActionsInterface;
use Dkd\PhpCmis\Data\ObjectDataInterface;
use Dkd\PhpCmis\Data\PropertyDataInterface;
use Dkd\PhpCmis\Data\RelationshipInterface;
use Dkd\PhpCmis\Data\RenditionInterface;

/**
 * Query Result.
 */
class QueryResult implements QueryResultInterface
{
    /**
     * @var PropertyDataInterface[]
     */
    protected $propertiesById = array();

    /**
     * @var PropertyDataInterface[]
     */
    protected $propertiesByQueryName = array();

    /**
     * @var AllowableActionsInterface|null
     */
    protected $allowableActions = null;

    /**
     * @var RelationshipInterface[]
     */
    protected $relationships = array();

    /**
     * @var RenditionInterface[]
     */
    protected $renditions = array();

    /**
     * @param SessionInterface $session
     * @param ObjectDataInterface $objectData
     */
    public function __construct(SessionInterface $session, ObjectDataInterface $objectData)
    {
        $objectFactory = $session->getObjectFactory();
        $properties = $objectData->getProperties();

        // handle properties
        if (!empty($properties)) {
            $queryProperties = $objectFactory->convertQueryProperties($properties);
            foreach ($queryProperties as $queryProperty) {
                if ($queryProperty->getId() !== null) {
                    $this->propertiesById[$queryProperty->getId()] = $queryProperty;
                }

                if ($queryProperty->getQueryName() !== null) {
                    $this->propertiesByQueryName[$queryProperty->getQueryName()] = $queryProperty;
                }
            }
        }

        // handle allowable actions
        $allowableActions = $objectData->getAllowableActions();
        if ($allowableActions !== null) {
            $this->allowableActions = $allowableActions;
        }

        // handle relationships
        $relationshipsObjectData = $objectData->getRelationships();
        foreach ($relationshipsObjectData as $relationshipObjectData) {
            $relationship = $objectFactory->convertObject($relationshipObjectData, $session->getDefaultContext());
            if ($relationship instanceof RelationshipInterface) {
                $this->relationships[] = $relationship;
            }
        }

        // handle renditions
        $renditionsData = $objectData->getRenditions();
        foreach ($renditionsData as $renditionData) {
            $rendition = $objectFactory->convertRendition($objectData->getId(), $renditionData);
            if ($rendition instanceof RenditionInterface) {
                $this->renditions[] = $rendition;
            }
        }
    }

    /**
     * Returns the allowable actions if they have been requested.
     *
     * @return AllowableActionsInterface|null the allowable actions if they have been requested, <code>null</code>
     *      otherwise
     */
    public function getAllowableActions()
    {
        return $this->allowableActions;
    }

    /**
     * Returns the list of all properties in this query result.
     *
     * @return PropertyDataInterface[] all properties, not <code>null</code>
     */
    public function getProperties()
    {
        return array_values($this->propertiesById);
    }

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
    public function getPropertyById($id)
    {
        return isset($this->propertiesById[$id]) ? $this->propertiesById[$id] : null;
    }

    /**
     * Returns a property by query name or alias.
     *
     * @param string $queryName the property query name or alias
     * @return PropertyDataInterface|null the property or <code>null</code> if the property doesn't exist
     * or hasn't been requested
     */
    public function getPropertyByQueryName($queryName)
    {
        return isset($this->propertiesByQueryName[$queryName]) ? $this->propertiesByQueryName[$queryName] : null;
    }

    /**
     * Returns a property multi-value by ID.
     *
     * @param string $id the property ID
     * @return array|null the property value or <code>null</code> if the property doesn't exist, hasn't been requested,
     * or the property value isn't set
     */
    public function getPropertyMultivalueById($id)
    {
        $property = $this->getPropertyById($id);

        return $property !== null ? $property->getValues() : null;
    }

    /**
     * Returns a property multi-value by query name or alias.
     *
     * @param string $queryName the property query name or alias
     * @return array|null the property value or <code>null</code> if the property doesn't exist, hasn't been requested,
     * or the property value isn't set
     */
    public function getPropertyMultivalueByQueryName($queryName)
    {
        $property = $this->getPropertyByQueryName($queryName);

        return $property !== null ? $property->getValues() : null;
    }

    /**
     * Returns a property (single) value by ID.
     *
     * @param string $id the property ID
     * @return mixed
     */
    public function getPropertyValueById($id)
    {
        $property = $this->getPropertyById($id);

        return $property !== null ? $property->getFirstValue() : null;
    }

    /**
     * Returns a property (single) value by query name or alias.
     *
     * @param string $queryName the property query name or alias
     * @return mixed the property value or <code>null</code> if the property doesn't exist, hasn't been requested,
     * or the property value isn't set
     */
    public function getPropertyValueByQueryName($queryName)
    {
        $property = $this->getPropertyByQueryName($queryName);

        return $property !== null ? $property->getFirstValue() : null;
    }

    /**
     * Returns the relationships if they have been requested.
     *
     * @return RelationshipInterface[]
     */
    public function getRelationships()
    {
        return $this->relationships;
    }

    /**
     * Returns the renditions if they have been requested.
     *
     * @return RenditionInterface[]
     */
    public function getRenditions()
    {
        return $this->renditions;
    }
}
