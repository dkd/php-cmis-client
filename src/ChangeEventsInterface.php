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

/**
 * Change event list.
 */
interface ChangeEventsInterface
{
    /**
     * Returns the change event list.
     *
     * @return ChangeEventInterface[] the list of change events, not null
     */
    public function getChangeEvents();

    /**
     * Indicates whether are more change events or not.
     *
     * @return boolean true is there are more change events, false otherwise
     */
    public function getHasMoreItems();

    /**
     * Returns the change log token if available.
     *
     * @return string|null the latest change log token or null if it is not available.
     */
    public function getLatestChangeLogToken();

    /**
     * Returns the total number of change events if available.
     *
     * @return int the total number of change events or -1 if the total number is not available
     */
    public function getTotalNumItems();
}
