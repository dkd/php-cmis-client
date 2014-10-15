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

use Dkd\PhpCmis\Data\NewTypeSettableAttributesInterface;

/**
 * Repository info data implementation including browser binding specific data.
 */
class NewTypeSettableAttributes extends AbstractExtensionData implements NewTypeSettableAttributesInterface
{
    /**
     * @var boolean
     */
    protected $id = false;

    /**
     * @var boolean
     */
    protected $localName = false;

    /**
     * @var boolean
     */
    protected $localNamespace = false;

    /**
     * @var boolean
     */
    protected $displayName = false;

    /**
     * @var boolean
     */
    protected $queryName = false;

    /**
     * @var boolean
     */
    protected $description = false;

    /**
     * @var boolean
     */
    protected $creatable = false;

    /**
     * @var boolean
     */
    protected $fileable = false;

    /**
     * @var boolean
     */
    protected $queryable = false;

    /**
     * @var boolean
     */
    protected $fulltextIndexed = false;

    /**
     * @var boolean
     */
    protected $includedInSupertypeQuery = false;

    /**
     * @var boolean
     */
    protected $controllablePolicy = false;

    /**
     * @var boolean
     */
    protected $controllableACL = false;

    /**
     * @return boolean
     */
    public function canSetControllableACL()
    {
        return $this->controllableACL;
    }

    /**
     * @param boolean $controllableACL
     */
    public function setControllableACL($controllableACL)
    {
        $this->controllableACL = (boolean) $controllableACL;
    }

    /**
     * @return boolean
     */
    public function canSetControllablePolicy()
    {
        return $this->controllablePolicy;
    }

    /**
     * @param boolean $controllablePolicy
     */
    public function setControllablePolicy($controllablePolicy)
    {
        $this->controllablePolicy = (boolean) $controllablePolicy;
    }

    /**
     * @return boolean
     */
    public function canSetCreatable()
    {
        return $this->creatable;
    }

    /**
     * @param boolean $creatable
     */
    public function setCreatable($creatable)
    {
        $this->creatable = (boolean) $creatable;
    }

    /**
     * @return boolean
     */
    public function canSetDescription()
    {
        return $this->description;
    }

    /**
     * @param boolean $description
     */
    public function setDescription($description)
    {
        $this->description = (boolean) $description;
    }

    /**
     * @return boolean
     */
    public function canSetDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param boolean $displayName
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = (boolean) $displayName;
    }

    /**
     * @return boolean
     */
    public function canSetFileable()
    {
        return $this->fileable;
    }

    /**
     * @param boolean $fileable
     */
    public function setFileable($fileable)
    {
        $this->fileable = (boolean) $fileable;
    }

    /**
     * @return boolean
     */
    public function canSetFulltextIndexed()
    {
        return $this->fulltextIndexed;
    }

    /**
     * @param boolean $fulltextIndexed
     */
    public function setFulltextIndexed($fulltextIndexed)
    {
        $this->fulltextIndexed = (boolean) $fulltextIndexed;
    }

    /**
     * @return boolean
     */
    public function canSetId()
    {
        return $this->id;
    }

    /**
     * @param boolean $id
     */
    public function setId($id)
    {
        $this->id = (boolean) $id;
    }

    /**
     * @return boolean
     */
    public function canSetIncludedInSupertypeQuery()
    {
        return $this->includedInSupertypeQuery;
    }

    /**
     * @param boolean $includedInSupertypeQuery
     */
    public function setIncludedInSupertypeQuery($includedInSupertypeQuery)
    {
        $this->includedInSupertypeQuery = (boolean) $includedInSupertypeQuery;
    }

    /**
     * @return boolean
     */
    public function canSetLocalName()
    {
        return $this->localName;
    }

    /**
     * @param boolean $localName
     */
    public function setLocalName($localName)
    {
        $this->localName = (boolean) $localName;
    }

    /**
     * @return boolean
     */
    public function canSetLocalNamespace()
    {
        return $this->localNamespace;
    }

    /**
     * @param boolean $localNamespace
     */
    public function setLocalNamespace($localNamespace)
    {
        $this->localNamespace = (boolean) $localNamespace;
    }

    /**
     * @return boolean
     */
    public function canSetQueryName()
    {
        return $this->queryName;
    }

    /**
     * @param boolean $queryName
     */
    public function setQueryName($queryName)
    {
        $this->queryName = (boolean) $queryName;
    }

    /**
     * @return boolean
     */
    public function canSetQueryable()
    {
        return $this->queryable;
    }

    /**
     * @param boolean $queryable
     */
    public function setQueryable($queryable)
    {
        $this->queryable = (boolean) $queryable;
    }
}
