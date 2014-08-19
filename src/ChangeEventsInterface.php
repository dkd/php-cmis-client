<?php
namespace Dkd\PhpCmis;

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
