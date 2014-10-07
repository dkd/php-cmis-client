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

use Dkd\PhpCmis\Data\ExtensionDataInterface;
use Dkd\PhpCmis\Definitions\PropertyDefinitionInterface;
use Dkd\PhpCmis\Definitions\TypeMutabilityInterface;
use Dkd\PhpCmis\Enum\BaseTypeId;

/**
 * Base type definition interface.
 */
interface TypeDefinitionInterface extends ExtensionDataInterface, \Serializable
{
    /**
     * Returns the base object type ID.
     *
     * @return BaseTypeId the base object type ID
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
     * @return string the type ID, not null
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
     * @return string|null the parent type ID or null if the type is a base type
     */
    public function getParentTypeId();

    /**
     * Returns the property definitions of this type.
     *
     * @return PropertyDefinitionInterface[]|null the property definitions or
     * null if the property definitions were not requested
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
     * @return boolean|null true if objects are controllable by ACLs;
     * false if objects are not controllable by ACLs; null - unknown (noncompliant repository)
     */
    public function isControllableAcl();

    /**
     * Returns if objects of this type are controllable by policies.
     *
     * @return boolean|null true if objects are controllable by policies;
     * false if objects are not controllable by policies; null - unknown (noncompliant repository)
     */
    public function isControllablePolicy();

    /**
     * Returns if an object of this type can be created.
     *
     * @return boolean|null true if an object of this type can be created;
     * false if creation of objects of this type is not possible; null - unknown (noncompliant repository)
     */
    public function isCreatable();

    /**
     * Returns if an object of this type can be filed.
     *
     * @return boolean|null true if an object of this type can be filed;
     * false if an object of this type cannot be filed; null - unknown (noncompliant repository)
     */
    public function isFileable();

    /**
     * Returns if this type is full text indexed.
     *
     * @return boolean|null true if this type is full text indexed;
     * false if this type is not full text indexed; null - unknown (noncompliant repository)
     */
    public function isFulltextIndexed();

    /**
     * Returns if this type is included in queries that query the super type.
     *
     * @return boolean|null true if this type is included;
     * false if this type is not included; null - unknown (noncompliant repository)
     */
    public function isIncludedInSupertypeQuery();

    /**
     * Returns if this type is queryable.
     *
     * @return boolean|null true if this type is queryable;
     * false if this type is not queryable; null - unknown (noncompliant repository)
     */
    public function isQueryable();
}
