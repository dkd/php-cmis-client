<?php
namespace Dkd\PhpCmis\DataObjects;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Definitions\PropertyDefinitionInterface;
use Dkd\PhpCmis\Definitions\TypeDefinitionInterface;
use Dkd\PhpCmis\Definitions\TypeMutabilityInterface;
use Dkd\PhpCmis\Enum\BaseTypeId;

/**
 * Abstract type definition data implementation.
 */
abstract class AbstractTypeDefinition extends AbstractExtensionData implements TypeDefinitionInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $localName;

    /**
     * @var string
     */
    protected $localNamespace;

    /**
     * @var string
     */
    protected $queryName;

    /**
     * @var string
     */
    protected $displayName;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var BaseTypeId
     */
    protected $baseTypeId;

    /**
     * @var string
     */
    protected $parentTypeId;

    /**
     * @var boolean|null
     */
    protected $isCreatable;

    /**
     * @var boolean|null
     */
    protected $isFileable;

    /**
     * @var boolean|null
     */
    protected $isQueryable;

    /**
     * @var boolean|null
     */
    protected $isIncludedInSupertypeQuery;

    /**
     * @var boolean|null
     */
    protected $isFulltextIndexed;

    /**
     * @var boolean|null
     */
    protected $isControllableAcl;

    /**
     * @var boolean|null
     */
    protected $isControllablePolicy;

    /**
     * @var PropertyDefinitionInterface[]|null
     */
    protected $propertyDefinitions;

    /**
     * @var TypeMutabilityInterface
     */
    protected $typeMutability;

    /**
     * @param TypeDefinitionInterface $typeDefinition
     */
    public function initialize(TypeDefinitionInterface $typeDefinition)
    {
        if ($typeDefinition->getId() !== null) {
            $this->setId($typeDefinition->getId());
        }
        if ($typeDefinition->getLocalName() !== null) {
            $this->setLocalName($typeDefinition->getLocalName());
        }
        if ($typeDefinition->getLocalNamespace() !== null) {
            $this->setLocalNamespace($typeDefinition->getLocalNamespace());
        }
        if ($typeDefinition->getQueryName() !== null) {
            $this->setQueryName($typeDefinition->getQueryName());
        }
        if ($typeDefinition->getDisplayName() !== null) {
            $this->setDisplayName($typeDefinition->getDisplayName());
        }
        if ($typeDefinition->getDescription() !== null) {
            $this->setDescription($typeDefinition->getDescription());
        }
        if ($typeDefinition->getBaseTypeId() !== null) {
            $this->setBaseTypeId($typeDefinition->getBaseTypeId());
        }
        if ($typeDefinition->getParentTypeId() !== null) {
            $this->setParentTypeId($typeDefinition->getParentTypeId());
        }
        if ($typeDefinition->isCreatable() !== null) {
            $this->setIsCreatable($typeDefinition->isCreatable());
        }
        if ($typeDefinition->isFileable() !== null) {
            $this->setIsFileable($typeDefinition->isFileable());
        }
        if ($typeDefinition->isQueryable() !== null) {
            $this->setIsQueryable($typeDefinition->isQueryable());
        }
        if ($typeDefinition->isIncludedInSupertypeQuery() !== null) {
            $this->setIsIncludedInSupertypeQuery($typeDefinition->isIncludedInSupertypeQuery());
        }
        if ($typeDefinition->isFulltextIndexed() !== null) {
            $this->setIsFulltextIndexed($typeDefinition->isFulltextIndexed());
        }
        if ($typeDefinition->isControllableAcl() !== null) {
            $this->setIsControllableAcl($typeDefinition->isControllableAcl());
        }
        if ($typeDefinition->isControllablePolicy() !== null) {
            $this->setIsControllablePolicy($typeDefinition->isControllablePolicy());
        }
        if ($typeDefinition->getPropertyDefinitions() !== null) {
            $this->setPropertyDefinitions($typeDefinition->getPropertyDefinitions());
        }
        if ($typeDefinition->getTypeMutability() !== null) {
            $this->setTypeMutability($typeDefinition->getTypeMutability());
        }
        if ($typeDefinition->getTypeMutability() !== null) {
            $this->setExtensions($typeDefinition->getExtensions());
        }
    }

    /**
     * Returns the type ID.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $this->castValueToSimpleType('string', $id);
    }

    /**
     * Returns the local name.
     */
    public function getLocalName()
    {
        return $this->localName;
    }

    /**
     * @param string $localName
     */
    public function setLocalName($localName)
    {
        $this->localName = $this->castValueToSimpleType('string', $localName);
    }

    /**
     * Returns the local namespace.
     */
    public function getLocalNamespace()
    {
        return $this->localNamespace;
    }

    /**
     * @param string $localNamespace
     */
    public function setLocalNamespace($localNamespace)
    {
        $this->localNamespace = $this->castValueToSimpleType('string', $localNamespace);
    }

    /**
     * Returns the query name
     */
    public function getQueryName()
    {
        return $this->queryName;
    }

    /**
     * @param string $queryName
     */
    public function setQueryName($queryName)
    {
        $this->queryName = $this->castValueToSimpleType('string', $queryName);
    }

    /**
     * Returns the display name.
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param string $displayName
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $this->castValueToSimpleType('string', $displayName);
    }

    /**
     * Returns the property description.
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $this->castValueToSimpleType('string', $description);
    }

    /**
     * Returns the base object type ID.
     */
    public function getBaseTypeId()
    {
        return $this->baseTypeId;
    }

    /**
     * @param BaseTypeId $baseTypeId
     */
    public function setBaseTypeId(BaseTypeId $baseTypeId)
    {
        $this->baseTypeId = $baseTypeId;
    }

    /**
     * Returns the parent type ID.
     */
    public function getParentTypeId()
    {
        return $this->parentTypeId;
    }

    /**
     * @param string $parentTypeId
     */
    public function setParentTypeId($parentTypeId)
    {
        $this->parentTypeId = $this->castValueToSimpleType('string', $parentTypeId, true);
    }

    /**
     * Returns if an object of this type can be created.
     */
    public function isCreatable()
    {
        return $this->isCreatable;
    }

    /**
     * @param boolean $isCreatable
     */
    public function setIsCreatable($isCreatable)
    {
        $this->isCreatable = $this->castValueToSimpleType('boolean', $isCreatable, true);
    }

    /**
     * Returns if an object of this type can be filed.
     */
    public function isFileable()
    {
        return $this->isFileable;
    }

    /**
     * @param boolean $isFileable
     */
    public function setIsFileable($isFileable)
    {
        $this->isFileable = $this->castValueToSimpleType('boolean', $isFileable, true);
    }

    /**
     * Returns if this type is queryable.
     */
    public function isQueryable()
    {
        return $this->isQueryable;
    }

    /**
     * @param boolean $isQueryable
     */
    public function setIsQueryable($isQueryable)
    {
        $this->isQueryable = $this->castValueToSimpleType('boolean', $isQueryable, true);
    }

    /**
     * Returns if this type is included in queries that query the super type.
     */
    public function isIncludedInSupertypeQuery()
    {
        return $this->isIncludedInSupertypeQuery;
    }

    /**
     * @param boolean $isIncludedInSupertypeQuery
     */
    public function setIsIncludedInSupertypeQuery($isIncludedInSupertypeQuery)
    {
        $this->isIncludedInSupertypeQuery = $this->castValueToSimpleType('boolean', $isIncludedInSupertypeQuery, true);
    }

    /**
     * Returns if this type is full text indexed.
     */
    public function isFulltextIndexed()
    {
        return $this->isFulltextIndexed;
    }

    /**
     * @param boolean $isFulltextIndexed
     */
    public function setIsFulltextIndexed($isFulltextIndexed)
    {
        $this->isFulltextIndexed = $this->castValueToSimpleType('boolean', $isFulltextIndexed, true);
    }

    /**
     * @return boolean
     */
    public function isControllableAcl()
    {
        return $this->isControllableAcl;
    }

    /**
     * @param boolean $isControllableAcl
     */
    public function setIsControllableAcl($isControllableAcl)
    {
        $this->isControllableAcl = $this->castValueToSimpleType('boolean', $isControllableAcl, true);
    }

    /**
     * Returns if objects of this type are controllable by policies.
     */
    public function isControllablePolicy()
    {
        return $this->isControllablePolicy;
    }

    /**
     * @param boolean $isControllablePolicy
     */
    public function setIsControllablePolicy($isControllablePolicy)
    {
        $this->isControllablePolicy = $this->castValueToSimpleType('boolean', $isControllablePolicy, true);
    }

    /**
     * Returns the property definitions for the given id of this type.
     *
     * @param string $id id of the property
     * @return PropertyDefinitionInterface|null the property definition
     */
    public function getPropertyDefinition($id)
    {
        return (isset($this->propertyDefinitions[$id]) ? $this->propertyDefinitions[$id] : null);
    }

    /**
     * Returns the property definitions of this type.
     */
    public function getPropertyDefinitions()
    {
        return $this->propertyDefinitions;
    }

    /**
     * @param PropertyDefinitionInterface[] $propertyDefinitions
     */
    public function setPropertyDefinitions(array $propertyDefinitions)
    {
        $this->propertyDefinitions = array();
        foreach ($propertyDefinitions as $propertyDefinition) {
            $this->addPropertyDefinition($propertyDefinition);
        }
    }

    /**
     * @param PropertyDefinitionInterface $propertyDefinition
     */
    public function addPropertyDefinition(PropertyDefinitionInterface $propertyDefinition)
    {
        $this->propertyDefinitions[$propertyDefinition->getId()] = $propertyDefinition;
    }

    /**
     * Returns type mutability flags.
     */
    public function getTypeMutability()
    {
        return $this->typeMutability;
    }

    /**
     * @param TypeMutabilityInterface $typeMutability
     */
    public function setTypeMutability(TypeMutabilityInterface $typeMutability)
    {
        $this->typeMutability = $typeMutability;
    }
}
