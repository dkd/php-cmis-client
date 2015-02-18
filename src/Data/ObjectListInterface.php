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
 * Represents a list of objects.
 */
interface ObjectListInterface extends ExtensionDataInterface
{
    /**
     * Returns the total number of the objects.
     *
     * @return integer|null the total number of the objects or <code>null</code> if the repository didn't
     *      provide the number
     */
    public function getNumItems();

    /**
     * Returns the list of objects.
     *
     * @return array the list of objects, not <code>null</code>
     */
    public function getObjects();

    /**
     * Indicates if there are more objects.
     *
     * @return boolean|null <code>true</code> if there are more objects, <code>false</code> if there are not more
     *      objects, or <code>null</code> if the repository didn't provide this flag
     */
    public function hasMoreItems();
}
