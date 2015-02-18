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

use Dkd\PhpCmis\Data\ObjectInFolderContainerInterface;
use Dkd\PhpCmis\Data\ObjectInFolderDataInterface;

/**
 * Object in folder list implementation.
 */
class ObjectInFolderContainer extends AbstractExtensionData implements ObjectInFolderContainerInterface
{
    /**
     * @var ObjectInFolderDataInterface
     */
    protected $object;

    /**
     * @var ObjectInFolderContainerInterface[]
     */
    protected $children = array();

    /**
     * Creates new ObjectInFolderContainer
     *
     * @param ObjectInFolderDataInterface $object
     */
    public function __construct(ObjectInFolderDataInterface $object)
    {
        $this->setObject($object);
    }

    /**
     * Returns the object containers of the next level.
     *
     * @return ObjectInFolderContainerInterface[] the child object, not <code>null</code>
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * checks input array for ObjectInFolderContainerInterface and sets objects
     *
     * @param ObjectInFolderContainerInterface[] $children
     */
    public function setChildren(array $children)
    {
        foreach ($children as $child) {
            $this->checkType('\\Dkd\\PhpCmis\\Data\\ObjectInFolderContainerInterface', $child);
        }

        $this->children = $children;
    }
    /**
     * Returns the object at this level.
     *
     * @return ObjectInFolderDataInterface the object, not <code>null</code>
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Sets the object
     *
     * @param ObjectInFolderDataInterface $object
     */
    public function setObject(ObjectInFolderDataInterface $object)
    {
        $this->object = $object;
    }
}
