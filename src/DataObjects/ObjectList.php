<?php
namespace Dkd\PhpCmis\DataObjects;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Dimitri Ebert <dimitri.ebert@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Data\ObjectDataInterface;
use Dkd\PhpCmis\Data\ObjectListInterface;

/**
 * Object list implementation.
 */
class ObjectList extends AbstractExtensionData implements ObjectListInterface
{
    /**
     * @var ObjectDataInterface[]
     */
    protected $objects = array();

    /**
     * @var boolean
     */
    protected $hasMoreItems = false;

    /**
     * @var integer|null
     */
    protected $numItems = null;

    /**
     * Returns the total number of the objects in the list.
     *
     * @return integer|null the total number of the objects or <code>null</code> if the repository didn't provide
     *      the number
     */
    public function getNumItems()
    {
        return $this->numItems;
    }

    /**
     * sets total number of internal counter
     *
     * @param integer $numItems
     */
    public function setNumItems($numItems)
    {
        $this->numItems = $this->castValueToSimpleType('integer', $numItems);
    }

    /**
     * Returns the objects
     *
     * @return ObjectDataInterface[] the objects in the list, not <code>null</code>
     */
    public function getObjects()
    {
        return $this->objects;
    }

    /**
     * Sets given list of objects
     *
     * @param ObjectDataInterface[] $objects
     */
    public function setObjects(array $objects)
    {
        foreach ($objects as $object) {
            $this->checkType('\\Dkd\\PhpCmis\\Data\\ObjectDataInterface', $object);
        }

        $this->objects = $objects;
    }

    /**
     * Indicates if there are more objects in the list.
     *
     * @return boolean|null <code>true</code> if there are more objects,
     *      <code>false</code> if there are not more objects, or <code>null</code> if the repository didn't provide
     *      this flag
     */
    public function hasMoreItems()
    {
        return $this->hasMoreItems;
    }

    /**
     * Set if the repository has more items
     *
     * @param boolean
     */
    public function setHasMoreItems($hasMoreItems)
    {
        $this->hasMoreItems = $this->castValueToSimpleType('boolean', $hasMoreItems);
    }
}
