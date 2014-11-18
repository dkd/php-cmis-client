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
 * A collection of flags that indicate which type attributes can be set at type creation.
 */
class NewTypeSettableAttributes extends AbstractExtensionData implements NewTypeSettableAttributesInterface
{
    /**
     * @var boolean
     */
    protected $canSetControllableAcl = false;

    /**
     * @var boolean
     */
    protected $canSetControllablePolicy = false;

    /**
     * @var boolean
     */
    protected $canSetCreatable = false;

    /**
     * @var boolean
     */
    protected $canSetDescription = false;

    /**
     * @var boolean
     */
    protected $canSetDisplayName = false;

    /**
     * @var boolean
     */
    protected $canSetFileable = false;

    /**
     * @var boolean
     */
    protected $canSetFulltextIndexed = false;

    /**
     * @var boolean
     */
    protected $canSetId = false;

    /**
     * @var boolean
     */
    protected $canSetIncludedInSupertypeQuery = false;

    /**
     * @var boolean
     */
    protected $canSetLocalName = false;

    /**
     * @var boolean
     */
    protected $canSetLocalNamespace = false;

    /**
     * @var boolean
     */
    protected $canSetQueryable = false;

    /**
     * @var boolean
     */
    protected $canSetQueryName = false;

    /**
     * Indicates if the "controllableACL" attribute can be set.
     *
     * @return boolean
     */
    public function canSetControllableAcl()
    {
        return $this->canSetControllableAcl;
    }

    /**
     * @param boolean $canSetControllableAcl
     */
    public function setCanSetControllableAcl($canSetControllableAcl)
    {
        $this->canSetControllableAcl = $this->castValueToSimpleType('boolean', $canSetControllableAcl);
    }

    /**
     * Indicates if the "controllablePolicy" attribute can be set.
     *
     * @return boolean
     */
    public function canSetControllablePolicy()
    {
        return $this->canSetControllablePolicy;
    }

    /**
     * @param boolean $canSetControllablePolicy
     */
    public function setCanSetControllablePolicy($canSetControllablePolicy)
    {
        $this->canSetControllablePolicy = $this->castValueToSimpleType('boolean', $canSetControllablePolicy);
    }

    /**
     * Indicates if the "creatable" attribute can be set.
     *
     * @return boolean
     */
    public function canSetCreatable()
    {
        return $this->canSetCreatable;
    }

    /**
     * @param boolean $canSetCreatable
     */
    public function setCanSetCreatable($canSetCreatable)
    {
        $this->canSetCreatable = $this->castValueToSimpleType('boolean', $canSetCreatable);
    }

    /**
     * Indicates if the "description" attribute can be set.
     *
     * @return boolean
     */
    public function canSetDescription()
    {
        return $this->canSetDescription;
    }

    /**
     * @param boolean $canSetDescription
     */
    public function setCanSetDescription($canSetDescription)
    {
        $this->canSetDescription = $this->castValueToSimpleType('boolean', $canSetDescription);
    }

    /**
     * Indicates if the "displayName" attribute can be set.
     *
     * @return boolean
     */
    public function canSetDisplayName()
    {
        return $this->canSetDisplayName;
    }

    /**
     * @param boolean $canSetDisplayName
     */
    public function setCanSetDisplayName($canSetDisplayName)
    {
        $this->canSetDisplayName = $this->castValueToSimpleType('boolean', $canSetDisplayName);
    }

    /**
     * Indicates if the "fileable" attribute can be set.
     *
     * @return boolean
     */
    public function canSetFileable()
    {
        return $this->canSetFileable;
    }

    /**
     * @param boolean $canSetFileable
     */
    public function setCanSetFileable($canSetFileable)
    {
        $this->canSetFileable = $this->castValueToSimpleType('boolean', $canSetFileable);
    }

    /**
     * Indicates if the "fulltextIndexed" attribute can be set.
     *
     * @return boolean
     */
    public function canSetFulltextIndexed()
    {
        return $this->canSetFulltextIndexed;
    }

    /**
     * @param boolean $canSetFulltextIndexed
     */
    public function setCanSetFulltextIndexed($canSetFulltextIndexed)
    {
        $this->canSetFulltextIndexed = $this->castValueToSimpleType('boolean', $canSetFulltextIndexed);
    }

    /**
     * Indicates if the "id" attribute can be set.
     *
     * @return boolean
     */
    public function canSetId()
    {
        return $this->canSetId;
    }

    /**
     * @param boolean $canSetId
     */
    public function setCanSetId($canSetId)
    {
        $this->canSetId = $this->castValueToSimpleType('boolean', $canSetId);
    }

    /**
     * Indicates if the "includedInSupertypeQuery" attribute can be set.
     *
     * @return boolean
     */
    public function canSetIncludedInSupertypeQuery()
    {
        return $this->canSetIncludedInSupertypeQuery;
    }

    /**
     * @param boolean $canSetIncludedInSupertypeQuery
     */
    public function setCanSetIncludedInSupertypeQuery($canSetIncludedInSupertypeQuery)
    {
        $this->canSetIncludedInSupertypeQuery = $this->castValueToSimpleType(
            'boolean',
            $canSetIncludedInSupertypeQuery
        );
    }

    /**
     * Indicates if the "localName" attribute can be set.
     *
     * @return boolean
     */
    public function canSetLocalName()
    {
        return $this->canSetLocalName;
    }

    /**
     * @param boolean $canSetLocalName
     */
    public function setCanSetLocalName($canSetLocalName)
    {
        $this->canSetLocalName = $this->castValueToSimpleType('boolean', $canSetLocalName);
    }

    /**
     * Indicates if the "localNamespace" attribute can be set.
     *
     * @return boolean
     */
    public function canSetLocalNamespace()
    {
        return $this->canSetLocalNamespace;
    }

    /**
     * @param boolean $canSetLocalNamespace
     */
    public function setCanSetLocalNamespace($canSetLocalNamespace)
    {
        $this->canSetLocalNamespace = $this->castValueToSimpleType('boolean', $canSetLocalNamespace);
    }

    /**
     * Indicates if the "queryable" attribute can be set.
     *
     * @return boolean
     */
    public function canSetQueryable()
    {
        return $this->canSetQueryable;
    }

    /**
     * @param boolean $canSetQueryable
     */
    public function setCanSetQueryable($canSetQueryable)
    {
        $this->canSetQueryable = $this->castValueToSimpleType('boolean', $canSetQueryable);
    }

    /**
     * Indicates if the "queryName" attribute can be set.
     *
     * @return boolean
     */
    public function canSetQueryName()
    {
        return $this->canSetQueryName;
    }

    /**
     * @param boolean $canSetQueryName
     */
    public function setCanSetQueryName($canSetQueryName)
    {
        $this->canSetQueryName = $this->castValueToSimpleType('boolean', $canSetQueryName);
    }
}
