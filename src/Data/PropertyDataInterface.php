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
 * Base property interface.
 */
interface PropertyDataInterface extends ExtensionDataInterface
{
    /**
     * Returns the display name.
     *
     * @return string|null the display name, may be <code>null</code>
     */
    public function getDisplayName();

    /**
     * Returns the first entry of the list of values.
     *
     * @return mixed|null first entry in the list of values or <code>null</code> if the list of values is empty
     */
    public function getFirstValue();

    /**
     * Returns the property ID.
     *
     * @return string|null the property ID, may be <code>null</code>
     */
    public function getId();

    /**
     * Returns the local name.
     *
     * @return string|null the local name, may be <code>null</code>
     */
    public function getLocalName();

    /**
     * Returns the query name.
     * The property query name must be set if the object is used in a query result.
     *
     * @return string|null the query name, may be <code>null</code>
     */
    public function getQueryName();

    /**
     * Returns the list of values of this property.
     *
     * @return array the list of values, not <code>null</code>
     */
    public function getValues();
}
