<?php
namespace Dkd\PhpCmis\Definitions;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Data\ExtensionDataInterface;
use Dkd\PhpCmis\Enum\BaseTypeId;

/**
 * Base type definition interface.
 */
interface TypeDefinitionInterface extends ExtensionDataInterface
{
    /**
     * Returns the base object type ID.
     *
     * @return BaseTypeId|null the base object type ID
     */
    public function getBaseTypeId();

    /**
     * Returns the property description.
     *
     * @return string the description
     */
    public function getDescription();

    /**
     * Returns the display name.
     *
     * @return string the display name
     */
    public function getDisplayName();

    /**
     * Returns the type ID.
     *
     * @return string the type ID, not <code>null</code>
     */
    public function getId();

    /**
     * Returns the local name.
     *
     * @return string the local name
     */
    public function getLocalName();

    /**
     * Returns the local namespace.
     *
     * @return string the local namespace
     */
    public function getLocalNamespace();

    /**
     * Returns the parent type ID.
     *
     * @return string|null the parent type ID or <code>null</code> if the type is a base type
     */
    public function getParentTypeId();

    /**
     * Returns the property definitions for the given id of this type.
     *
     * @param string $id id of the property
     * @return PropertyDefinitionInterface|null the property definition
     */
    public function getPropertyDefinition($id);

    /**
     * Returns the property definitions of this type.
     *
     * @return PropertyDefinitionInterface[] the property definitions
     */
    public function getPropertyDefinitions();

    /**
     * Returns the query name
     *
     * @return string the query name
     */
    public function getQueryName();

    /**
     * Returns type mutability flags.
     *
     * @return TypeMutabilityInterface type mutability flags
     */
    public function getTypeMutability();

    /**
     * Returns if objects of this type are controllable by ACLs.
     *
     * @return boolean|null <code>true</code> if objects are controllable by ACLs;
     *      <code>false</code> if objects are not controllable by ACLs;
     *      <code>null</code> - unknown (noncompliant repository)
     */
    public function isControllableAcl();

    /**
     * Returns if objects of this type are controllable by policies.
     *
     * @return boolean|null <code>true</code> if objects are controllable by policies;
     *      <code>false</code> if objects are not controllable by policies;
     *      <code>null</code> - unknown (noncompliant repository)
     */
    public function isControllablePolicy();

    /**
     * Returns if an object of this type can be created.
     *
     * @return boolean|null <code>true</code> if an object of this type can be created;
     *      <code>false</code> if creation of objects of this type is not possible;
     *      <code>null</code> - unknown (noncompliant repository)
     */
    public function isCreatable();

    /**
     * Returns if an object of this type can be filed.
     *
     * @return boolean|null <code>true</code> if an object of this type can be filed;
     *      <code>false</code> if an object of this type cannot be filed;
     *      <code>null</code> - unknown (noncompliant repository)
     */
    public function isFileable();

    /**
     * Returns if this type is full text indexed.
     *
     * @return boolean|null <code>true</code> if this type is full text indexed;
     *      <code>false</code> if this type is not full text indexed;
     *      <code>null</code> - unknown (noncompliant repository)
     */
    public function isFulltextIndexed();

    /**
     * Returns if this type is included in queries that query the super type.
     *
     * @return boolean|null <code>true</code> if this type is included;
     *      <code>false</code> if this type is not included;
     *      <code>null</code> - unknown (noncompliant repository)
     */
    public function isIncludedInSupertypeQuery();

    /**
     * Returns if this type is queryable.
     *
     * @return boolean|null <code>true</code> if this type is queryable;
     *      <code>false</code> if this type is not queryable;
     *      <code>null</code> - unknown (noncompliant repository)
     */
    public function isQueryable();
}
