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
 * A collection of flags that indicate which type attributes can be set at type creation.
 */
interface NewTypeSettableAttributesInterface extends ExtensionDataInterface
{
    /**
     * Indicates if the "controllableACL" attribute can be set.
     *
     * @return boolean
     */
    public function canSetControllableAcl();

    /**
     * Indicates if the "controllablePolicy" attribute can be set.
     *
     * @return boolean
     */
    public function canSetControllablePolicy();

    /**
     * Indicates if the "creatable" attribute can be set.
     *
     * @return boolean
     */
    public function canSetCreatable();

    /**
     * Indicates if the "description" attribute can be set.
     *
     * @return boolean
     */
    public function canSetDescription();

    /**
     * Indicates if the "displayName" attribute can be set.
     *
     * @return boolean
     */
    public function canSetDisplayName();

    /**
     * Indicates if the "fileable" attribute can be set.
     *
     * @return boolean
     */
    public function canSetFileable();

    /**
     * Indicates if the "fulltextIndexed" attribute can be set.
     *
     * @return boolean
     */
    public function canSetFulltextIndexed();

    /**
     * Indicates if the "id" attribute can be set.
     *
     * @return boolean
     */
    public function canSetId();

    /**
     * Indicates if the "includedInSupertypeQuery" attribute can be set.
     *
     * @return boolean
     */
    public function canSetIncludedInSupertypeQuery();

    /**
     * Indicates if the "localName" attribute can be set.
     *
     * @return boolean
     */
    public function canSetLocalName();

    /**
     * Indicates if the "localNamespace" attribute can be set.
     *
     * @return boolean
     */
    public function canSetLocalNamespace();

    /**
     * Indicates if the "queryable" attribute can be set.
     *
     * @return boolean
     */
    public function canSetQueryable();

    /**
     * Indicates if the "queryName" attribute can be set.
     *
     * @return boolean
     */
    public function canSetQueryName();
}
