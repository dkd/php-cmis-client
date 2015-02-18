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
 * Container used for trees that represent objects in a folder hierarchy.
 */
interface ObjectInFolderContainerInterface extends ExtensionDataInterface
{
    /**
     * Creates new ObjectInFolderContainerInterface object
     *
     * @param ObjectInFolderDataInterface $object
     */
    public function __construct(ObjectInFolderDataInterface $object);

    /**
     * Returns the object containers of the next level.
     *
     * @return ObjectInFolderContainerInterface[] the child object, not <code>null</code>
     */
    public function getChildren();

    /**
     * Returns the object at this level.
     *
     * @return ObjectInFolderDataInterface the object, not <code>null</code>
     */
    public function getObject();
}
