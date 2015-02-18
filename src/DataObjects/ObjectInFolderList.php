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

use Dkd\PhpCmis\Data\ObjectInFolderDataInterface;
use Dkd\PhpCmis\Data\ObjectInFolderListInterface;

/**
 * Object in folder list implementation.
 */
class ObjectInFolderList extends AbstractExtensionData implements ObjectInFolderListInterface
{
    /**
     * @var ObjectInFolderDataInterface[]
     */
    protected $objects = array();

    /**
     * @var boolean
     */
    protected $hasMoreItems = false;

    /**
     * @var integer
     */
    protected $numItems = null;

    /**
     * Returns the total number of the objects in the folder from repository.
     * It is not a number of objects in response and could be greater as amount of objects properties.
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
     * Returns the objects in the folder.
     *
     * @return ObjectInFolderDataInterface[] the objects in the folder, not <code>null</code>
     */
    public function getObjects()
    {
        return $this->objects;
    }

    /**
     * checks input array for ObjectInFolderDataInterface and sets objects
     *
     * @param ObjectInFolderDataInterface[] $objects
     */
    public function setObjects(array $objects)
    {
        foreach ($objects as $object) {
            $this->checkType('\\Dkd\\PhpCmis\\Data\\ObjectInFolderDataInterface', $object);
        }

        $this->objects = $objects;
    }

    /**
     * Indicates if there are more objects in the folder.
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
