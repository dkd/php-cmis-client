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
 * Represents the objects in a folder.
 */
interface ObjectInFolderListInterface extends ExtensionDataInterface
{
    /**
     * Returns the total number of the objects in the folder.
     *
     * @return integer|null the total number of the objects or <code>null</code> if the repository didn't provide
     *      the number
     */
    public function getNumItems();

    /**
     * Returns the objects in the folder.
     *
     * @return ObjectInFolderDataInterface[] the objects in the folder, not <code>null</code>
     */
    public function getObjects();

    /**
     * Indicates if there are more objects in the folder.
     *
     * @return boolean|null <code>true</code> if there are more objects,
     *      <code>false</code> if there are not more objects, or <code>null</code> if the repository didn't provide
     *      this flag
     */
    public function hasMoreItems();
}
