<?php
namespace Dkd\PhpCmis\Data;

/**
 * Base property interface.
 */
interface PropertyDataInterface extends ExtensionsDataInterface
{
    /**
     * Returns the display name.
     *
     * @return string|null the display name, may be null
     */
    public function getDisplayName();

    /**
     * Returns the first entry of the list of values.
     *
     * @return mixed first entry in the list of values or null if the list of values is empty
     */
    public function getFirstValue();

    /**
     * Returns the property ID.
     *
     * @return string|null the property ID, may be null
     */
    public function getId();

    /**
     * Returns the local name.
     *
     * @return string|null the local name, may be null
     */
    public function getLocalName();

    /**
     * Returns the query name.
     * The property query name must be set if the object is used in a query result.
     *
     * @return string|null the query name, may be null
     */
    public function getQueryName();

    /**
     * Returns the list of values of this property.
     *
     * @return array the list of values, not null
     */
    public function getValues();
}
