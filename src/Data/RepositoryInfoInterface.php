<?php
namespace Dkd\PhpCmis\Data;

use Dkd\PhpCmis\Enum\BaseTypeId;
use Dkd\PhpCmis\Enum\CmisVersion;

/**
 * Repository Info.
 */
interface RepositoryInfoInterface extends ExtensionsDataInterface
{
    /**
     * Returns the ACL capabilities.
     *
     * @return AclCapabilitiesInterface
     */
    public function getAclCapabilities();

    /**
     * Returns the repository capabilities.
     *
     * @return RepositoryCapabilitiesInterface
     */
    public function getCapabilities();

    /**
     * Indicates whether the entries in the change log are incomplete or complete.
     *
     * @return boolean|null true if the changes are incomplete, false if the changes are complete,
     * or null if the repository didn't provide this flag
     */
    public function getChangesIncomplete();

    /**
     * Returns which types of objects are considered in the change log.
     *
     * @return BaseTypeId[]
     */
    public function getChangesOnType();

    /**
     * Returns the CMIS version supported by this repository as a CmisVersion enum.
     *
     * @return CmisVersion the supported CMIS version, not null
     */
    public function getCmisVersion();

    /**
     * Returns the CMIS version supported by this repository as a string.
     *
     * @return string the supported CMIS version, not null
     */
    public function getCmisVersionSupported();

    /**
     * Returns the repository description.
     *
     * @return string|null the repository description, may be null
     */
    public function getDescription();

    /**
     * Returns the list of CMIS extension features supported by this repository.
     *
     * @return ExtensionFeatureInterface[] the list of features, may be null
     */
    public function getExtensionFeatures();

    /**
     * Returns the repository ID.
     *
     * @return string the repository ID, not null
     */
    public function getId();

    /**
     * Returns the latest change log token.
     *
     * @return string|null the latest change log token, may be null
     */
    public function getLatestChangeLogToken();

    /**
     * Returns the repository name.
     *
     * @return string|null the repository name, may be null
     */
    public function getName();

    /**
     * Returns principal ID for an anonymous user (any authenticated user).
     * This principal ID is supposed to be used in an Ace.
     *
     * @return string|null the principal ID for an anonymous user or null
     * if the repository does not support anonymous users
     */
    public function getPrincipalIdAnonymous();

    /**
     * Returns principal ID for unauthenticated user (guest user).
     * This principal ID is supposed to be used in an Ace.
     *
     * @return string|null the principal ID for unauthenticated user or null
     * if the repository does not support unauthenticated users
     */
    public function getPrincipalIdAnyone();

    /**
     * Returns the repository product name.
     *
     * @return string|null the repository product name, may be null
     */
    public function getProductName();

    /**
     * Returns the repository product version.
     *
     * @return string|null the repository product version, may be null
     */
    public function getProductVersion();

    /**
     * Returns the object ID of the root folder.
     *
     * @return string the root folder ID, not null
     */
    public function getRootFolderId();

    /**
     * Returns the URL of a web interface for this repository, if available.
     *
     * @return string the thin client URL, not null
     */
    public function getThinClientUri();

    /**
     * Returns the repository vendor name.
     *
     * @return string|null the repository vendor name, may be null
     */
    public function getVendorName();
}
