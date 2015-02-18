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
use Dkd\PhpCmis\Data\ObjectInFolderDataInterface;

/**
 * Object in folder data implementation.
 */
class ObjectInFolderData extends AbstractExtensionData implements ObjectInFolderDataInterface
{
    /**
     * @var ObjectDataInterface
     */
    protected $object;

    /**
     * @var string|null
     */
    protected $pathSegment;

    /**
     * Returns the object at this level.
     *
     * @return ObjectDataInterface the object, not <code>null</code>
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Sets the object
     *
     * @param ObjectDataInterface $object
     */
    public function setObject(ObjectDataInterface $object)
    {
        $this->object = $object;
    }

    /**
     * Returns the path segment of the object in the folder.
     *
     * @return string|null the path segment or <code>null</code> if the path segment has not been requested
     */
    public function getPathSegment()
    {
        return $this->pathSegment;
    }

    /**
     * Sets the path segment
     *
     * @param string|null
     */
    public function setPathSegment($pathSegment)
    {
        $this->pathSegment = $this->castValueToSimpleType('string', $pathSegment, true);
    }
}
