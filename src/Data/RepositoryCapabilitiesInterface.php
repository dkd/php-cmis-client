<?php
namespace Dkd\PhpCmis\Data;

use Dkd\PhpCmis\Enum\CapabilityAcl;
use Dkd\PhpCmis\Enum\CapabilityChanges;
use Dkd\PhpCmis\Enum\CapabilityContentStreamUpdates;
use Dkd\PhpCmis\Enum\CapabilityJoin;
use Dkd\PhpCmis\Enum\CapabilityOrderBy;
use Dkd\PhpCmis\Enum\CapabilityQuery;
use Dkd\PhpCmis\Enum\CapabilityRenditions;

/**
 * Repository Capabilities.
 */
interface RepositoryCapabilitiesInterface extends ExtensionsDataInterface
{
    /**
     * Returns the ACL capabilities.
     *
     * @return CapabilityAcl
     */
    public function getAclCapabilities();

    /**
     * @return CapabilityChanges
     */
    public function getChangesCapability();

    /**
     * @return CapabilityContentStreamUpdates
     */
    public function getContentStreamUpdatesCapability();

    /**
     * @return CreatablePropertyTypes
     */
    public function getCreatablePropertyTypes();

    /**
     * @return CapabilityJoin
     */
    public function getJoinCapability();

    /**
     * @return NewTypeSettableAttributesInterface
     */
    public function getNewTypeSettableAttributes();

    /**
     * @return CapabilityOrderBy
     */
    public function getOrderByCapability();

    /**
     * @return CapabilityQuery
     */
    public function getQueryCapability();

    /**
     * @return CapabilityRenditions
     */
    public function getRenditionsCapability();

    /**
     * @return boolean
     */
    public function isAllVersionsSearchableSupported();

    /**
     * @return boolean
     */
    public function isGetDescendantsSupported();

    /**
     * @return boolean
     */
    public function isGetFolderTreeSupported();

    /**
     * @return boolean
     */
    public function isMultifilingSupported();

    /**
     * @return boolean
     */
    public function isPwcSearchableSupported();

    /**
     * @return boolean
     */
    public function isPwcUpdatableSupported();

    /**
     * @return boolean
     */
    public function isUnfilingSupported();

    /**
     * @return boolean
     */
    public function isVersionSpecificFilingSupported();
}
