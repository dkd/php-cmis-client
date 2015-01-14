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

use Dkd\PhpCmis\Data\CreatablePropertyTypesInterface;
use Dkd\PhpCmis\Data\NewTypeSettableAttributesInterface;
use Dkd\PhpCmis\Data\RepositoryCapabilitiesInterface;
use Dkd\PhpCmis\Enum\CapabilityAcl;
use Dkd\PhpCmis\Enum\CapabilityChanges;
use Dkd\PhpCmis\Enum\CapabilityContentStreamUpdates;
use Dkd\PhpCmis\Enum\CapabilityJoin;
use Dkd\PhpCmis\Enum\CapabilityOrderBy;
use Dkd\PhpCmis\Enum\CapabilityQuery;
use Dkd\PhpCmis\Enum\CapabilityRenditions;

/**
 * Repository info data implementation including browser binding specific data.
 */
class RepositoryCapabilities extends AbstractExtensionData implements RepositoryCapabilitiesInterface
{
    /**
     * @var boolean
     */
    protected $supportsAllVersionsSearchable = false;

    /**
     * @var CapabilityAcl
     */
    protected $aclCapability;

    /**
     * @var CapabilityChanges
     */
    protected $changesCapability;

    /**
     * @var CapabilityContentStreamUpdates
     */
    protected $contentStreamUpdatesCapability;

    /**
     * @var CapabilityJoin
     */
    protected $joinCapability;

    /**
     * @var CapabilityQuery
     */
    protected $queryCapability;

    /**
     * @var CapabilityRenditions
     */
    protected $renditionsCapability;

    /**
     * @var boolean
     */
    protected $isPwcSearchable = false;

    /**
     * @var boolean
     */
    protected $isPwcUpdatable = false;

    /**
     * @var boolean
     */
    protected $supportsGetDescendants = false;

    /**
     * @var boolean
     */
    protected $supportsGetFolderTree = false;

    /**
     * @var CapabilityOrderBy
     */
    protected $orderByCapability;

    /**
     * @var boolean
     */
    protected $supportsMultifiling = false;

    /**
     * @var boolean
     */
    protected $supportsUnfiling = false;

    /**
     * @var boolean
     */
    protected $supportsVersionSpecificFiling = false;

    /**
     * @var CreatablePropertyTypesInterface|null
     */
    protected $creatablePropertyTypes;

    /**
     * @var NewTypeSettableAttributesInterface|null
     */
    protected $newTypeSettableAttributes;

    /**
     * Constructor that sets some defaults
     */
    public function __construct()
    {
        $this->setAclCapability(CapabilityAcl::cast(CapabilityAcl::__DEFAULT));
        $this->setChangesCapability(CapabilityChanges::cast(CapabilityChanges::__DEFAULT));
        $this->setJoinCapability(CapabilityJoin::cast(CapabilityJoin::__DEFAULT));
        $this->setContentStreamUpdatesCapability(
            CapabilityContentStreamUpdates::cast(CapabilityContentStreamUpdates::__DEFAULT)
        );
        $this->setQueryCapability(CapabilityQuery::cast(CapabilityQuery::__DEFAULT));
        $this->setRenditionsCapability(CapabilityRenditions::cast(CapabilityRenditions::__DEFAULT));
        $this->setOrderByCapability(CapabilityOrderBy::cast(CapabilityOrderBy::__DEFAULT));
    }

    /**
     * @return CapabilityAcl
     */
    public function getAclCapability()
    {
        return CapabilityAcl::cast($this->aclCapability);
    }

    /**
     * @param CapabilityAcl $aclCapability
     */
    public function setAclCapability(CapabilityAcl $aclCapability)
    {
        $this->aclCapability = $aclCapability;
    }

    /**
     * @return boolean
     */
    public function isAllVersionsSearchableSupported()
    {
        return $this->supportsAllVersionsSearchable;
    }

    /**
     * @param boolean $supportsAllVersionsSearchable
     */
    public function setSupportsAllVersionsSearchable($supportsAllVersionsSearchable)
    {
        $this->supportsAllVersionsSearchable = (boolean) $supportsAllVersionsSearchable;
    }

    /**
     * @return CapabilityChanges
     */
    public function getChangesCapability()
    {
        return CapabilityChanges::cast($this->changesCapability);
    }

    /**
     * @param CapabilityChanges $changesCapability
     */
    public function setChangesCapability(CapabilityChanges $changesCapability)
    {
        $this->changesCapability = $changesCapability;
    }

    /**
     * @return CapabilityContentStreamUpdates
     */
    public function getContentStreamUpdatesCapability()
    {
        return CapabilityContentStreamUpdates::cast($this->contentStreamUpdatesCapability);
    }

    /**
     * @param CapabilityContentStreamUpdates $contentStreamUpdatesCapability
     */
    public function setContentStreamUpdatesCapability(CapabilityContentStreamUpdates $contentStreamUpdatesCapability)
    {
        $this->contentStreamUpdatesCapability = $contentStreamUpdatesCapability;
    }

    /**
     * @return CreatablePropertyTypesInterface|null
     */
    public function getCreatablePropertyTypes()
    {
        return $this->creatablePropertyTypes;
    }

    /**
     * @param CreatablePropertyTypesInterface $creatablePropertyTypes
     */
    public function setCreatablePropertyTypes(CreatablePropertyTypesInterface $creatablePropertyTypes)
    {
        $this->creatablePropertyTypes = $creatablePropertyTypes;
    }

    /**
     * @return boolean
     */
    public function isPwcSearchableSupported()
    {
        return $this->isPwcSearchable;
    }

    /**
     * @param boolean $isPwcSearchable
     */
    public function setSupportsPwcSearchable($isPwcSearchable)
    {
        $this->isPwcSearchable = (boolean) $isPwcSearchable;
    }

    /**
     * @return boolean
     */
    public function isPwcUpdatableSupported()
    {
        return $this->isPwcUpdatable;
    }

    /**
     * @param boolean $isPwcUpdatable
     */
    public function setSupportsPwcUpdatable($isPwcUpdatable)
    {
        $this->isPwcUpdatable = (boolean) $isPwcUpdatable;
    }

    /**
     * @return CapabilityJoin
     */
    public function getJoinCapability()
    {
        return CapabilityJoin::cast($this->joinCapability);
    }

    /**
     * @param CapabilityJoin $joinCapability
     */
    public function setJoinCapability(CapabilityJoin $joinCapability)
    {
        $this->joinCapability = $joinCapability;
    }

    /**
     * @return NewTypeSettableAttributesInterface|null
     */
    public function getNewTypeSettableAttributes()
    {
        return $this->newTypeSettableAttributes;
    }

    /**
     * @param NewTypeSettableAttributesInterface $newTypeSettableAttributes
     */
    public function setNewTypeSettableAttributes(NewTypeSettableAttributesInterface $newTypeSettableAttributes)
    {
        $this->newTypeSettableAttributes = $newTypeSettableAttributes;
    }

    /**
     * @return CapabilityOrderBy
     */
    public function getOrderByCapability()
    {
        return CapabilityOrderBy::cast($this->orderByCapability);
    }

    /**
     * @param CapabilityOrderBy $orderByCapability
     */
    public function setOrderByCapability(CapabilityOrderBy $orderByCapability)
    {
        $this->orderByCapability = $orderByCapability;
    }

    /**
     * @return CapabilityQuery
     */
    public function getQueryCapability()
    {
        return CapabilityQuery::cast($this->queryCapability);
    }

    /**
     * @param CapabilityQuery $queryCapability
     */
    public function setQueryCapability(CapabilityQuery $queryCapability)
    {
        $this->queryCapability = $queryCapability;
    }

    /**
     * @return CapabilityRenditions
     */
    public function getRenditionsCapability()
    {
        return CapabilityRenditions::cast($this->renditionsCapability);
    }

    /**
     * @param CapabilityRenditions $renditionsCapability
     */
    public function setRenditionsCapability(CapabilityRenditions $renditionsCapability)
    {
        $this->renditionsCapability = $renditionsCapability;
    }

    /**
     * @return boolean
     */
    public function isGetDescendantsSupported()
    {
        return $this->supportsGetDescendants;
    }

    /**
     * @param boolean $supportsGetDescendants
     */
    public function setSupportsGetDescendants($supportsGetDescendants)
    {
        $this->supportsGetDescendants = (boolean) $supportsGetDescendants;
    }

    /**
     * @return boolean
     */
    public function isGetFolderTreeSupported()
    {
        return $this->supportsGetFolderTree;
    }

    /**
     * @param boolean $supportsGetFolderTree
     */
    public function setSupportsGetFolderTree($supportsGetFolderTree)
    {
        $this->supportsGetFolderTree = (boolean) $supportsGetFolderTree;
    }

    /**
     * @return boolean
     */
    public function isMultifilingSupported()
    {
        return $this->supportsMultifiling;
    }

    /**
     * @param boolean $supportsMultifiling
     */
    public function setSupportsMultifiling($supportsMultifiling)
    {
        $this->supportsMultifiling = (boolean) $supportsMultifiling;
    }

    /**
     * @return boolean
     */
    public function isUnfilingSupported()
    {
        return $this->supportsUnfiling;
    }

    /**
     * @param boolean $supportsUnfiling
     */
    public function setSupportsUnfiling($supportsUnfiling)
    {
        $this->supportsUnfiling = (boolean) $supportsUnfiling;
    }

    /**
     * @return boolean
     */
    public function isVersionSpecificFilingSupported()
    {
        return $this->supportsVersionSpecificFiling;
    }

    /**
     * @param boolean $supportsVersionSpecificFiling
     */
    public function setSupportsVersionSpecificFiling($supportsVersionSpecificFiling)
    {
        $this->supportsVersionSpecificFiling = (boolean) $supportsVersionSpecificFiling;
    }
}
