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
use Dkd\PhpCmis\Definitions\PropertyDefinitionInterface;
use Dkd\PhpCmis\Definitions\TypeMutabilityInterface;
use Dkd\PhpCmis\Enum\BaseTypeId;

/**
 * Mutable Base type definition interface.
 */
interface MutableTypeDefinitionInterface extends ExtensionDataInterface
{
    /**
     * Sets the base object type ID.
     *
     * @param BaseTypeId $baseTypeId the base object type ID
     */
    public function setBaseTypeId(BaseTypeId $baseTypeId);

    /**
     * Sets the property description.
     *
     * @param string $description the description
     */
    public function setDescription($description);

    /**
     * Sets the display name.
     *
     * @param string $displayName the display name
     */
    public function setDisplayName($displayName);

    /**
     * Sets the type ID.
     *
     * @param string $id the type ID, not null
     */
    public function setId($id);

    /**
     * Sets the local name.
     *
     * @param string $localName the local name
     */
    public function setLocalName($localName);

    /**
     * Sets the local namespace.
     *
     * @param string $localNamespace the local namespace
     */
    public function setLocalNamespace($localNamespace);

    /**
     * Sets the parent type ID.
     *
     * @param string $parentTypeId the parent type ID or <code>null<code>if the type is a base type
     */
    public function setParentTypeId($parentTypeId);

    /**
     * Sets the property definitions for the given id of this type.
     *
     * @param PropertyDefinitionInterface $propertyDefinition the property definition
     */
    public function addPropertyDefinition(PropertyDefinitionInterface $propertyDefinition);

    /**
     * Sets the property definitions of this type.
     *
     * @param PropertyDefinitionInterface[] $propertyDefinitions the property definitions
     */
    public function setPropertyDefinitions(array $propertyDefinitions);

    /**
     * Sets the query name
     *
     * @param string $queryName the query name
     */
    public function setQueryName($queryName);

    /**
     * Sets type mutability flags.
     *
     * @param TypeMutabilityInterface $typeMutability type mutability flags
     */
    public function setTypeMutability(TypeMutabilityInterface $typeMutability);

    /**
     * Sets if objects of this type are controllable by ACLs.
     *
     * @param boolean $isControllableAcl <code>true</code> if objects are controllable by ACLs;
     * <code>false</code> if objects are not controllable by ACLs
     * default value is <code>null<code>- unknown (noncompliant repository)
     */
    public function setIsControllableAcl($isControllableAcl);

    /**
     * Sets if objects of this type are controllable by policies.
     *
     * @param boolean $isControllablePolicy <code>true</code> if objects are controllable by policies;
     * <code>false</code> if objects are not controllable by policies
     * default value is <code>null<code>- unknown (noncompliant repository)
     */
    public function setIsControllablePolicy($isControllablePolicy);

    /**
     * Sets if an object of this type can be created.
     *
     * @param boolean $isCreatable <code>true</code> if an object of this type can be created;
     * <code>false</code> if creation of objects of this type is not possible
     * default value is <code>null<code>- unknown (noncompliant repository)
     */
    public function setIsCreatable($isCreatable);

    /**
     * Sets if an object of this type can be filed.
     *
     * @param boolean $isFileable <code>true</code> if an object of this type can be filed;
     * <code>false</code> if an object of this type cannot be filed
     * default value is <code>null<code>- unknown (noncompliant repository)
     */
    public function setIsFileable($isFileable);

    /**
     * Sets if this type is full text indexed.
     *
     * @param boolean $isFulltextIndexed <code>true</code> if this type is full text indexed;
     * <code>false</code> if this type is not full text indexed
     * default value is <code>null<code>- unknown (noncompliant repository)
     */
    public function setIsFulltextIndexed($isFulltextIndexed);

    /**
     * Sets if this type is included in queries that query the super type.
     *
     * @param boolean $isIncludedInSupertypeQuery <code>true</code> if this type is included;
     * <code>false</code> if this type is not included
     * default value is <code>null<code>- unknown (noncompliant repository)
     */
    public function setIsIncludedInSupertypeQuery($isIncludedInSupertypeQuery);

    /**
     * Sets if this type is queryable.
     *
     * @param boolean $isQueryable <code>true</code> if this type is queryable;
     * <code>false</code> if this type is not queryable
     * default value is <code>null<code>- unknown (noncompliant repository)
     */
    public function setIsQueryable($isQueryable);
}
