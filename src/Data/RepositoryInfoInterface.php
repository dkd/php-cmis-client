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

use Dkd\PhpCmis\Enum\BaseTypeId;
use Dkd\PhpCmis\Enum\CmisVersion;

/**
 * Repository Info.
 */
interface RepositoryInfoInterface extends ExtensionDataInterface
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
     * @return boolean|null <code>true</code> if the changes are incomplete, <code>false</code> if the changes are
     *      complete, or <code>null</code> if the repository didn't provide this flag
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
     * @return CmisVersion the supported CMIS version, not <code>null</code>
     */
    public function getCmisVersion();

    /**
     * Returns the repository description.
     *
     * @return string|null the repository description, may be <code>null</code>
     */
    public function getDescription();

    /**
     * Returns the list of CMIS extension features supported by this repository.
     *
     * @return ExtensionFeatureInterface[] the list of features, may be <code>null</code>
     */
    public function getExtensionFeatures();

    /**
     * Returns the repository ID.
     *
     * @return string the repository ID, not <code>null</code>
     */
    public function getId();

    /**
     * Returns the latest change log token.
     *
     * @return string|null the latest change log token, may be <code>null</code>
     */
    public function getLatestChangeLogToken();

    /**
     * Returns the repository name.
     *
     * @return string|null the repository name, may be <code>null</code>
     */
    public function getName();

    /**
     * Returns principal ID for an anonymous user (any authenticated user).
     * This principal ID is supposed to be used in an Ace.
     *
     * @return string|null the principal ID for an anonymous user or <code>null</code>
     * if the repository does not support anonymous users
     */
    public function getPrincipalIdAnonymous();

    /**
     * Returns principal ID for unauthenticated user (guest user).
     * This principal ID is supposed to be used in an Ace.
     *
     * @return string|null the principal ID for unauthenticated user or <code>null</code>
     * if the repository does not support unauthenticated users
     */
    public function getPrincipalIdAnyone();

    /**
     * Returns the repository product name.
     *
     * @return string|null the repository product name, may be <code>null</code>
     */
    public function getProductName();

    /**
     * Returns the repository product version.
     *
     * @return string|null the repository product version, may be <code>null</code>
     */
    public function getProductVersion();

    /**
     * Returns the object ID of the root folder.
     *
     * @return string the root folder ID, not <code>null</code>
     */
    public function getRootFolderId();

    /**
     * Returns the URL of a web interface for this repository, if available.
     *
     * @return string the thin client URL, not <code>null</code>
     */
    public function getThinClientUri();

    /**
     * Returns the repository vendor name.
     *
     * @return string|null the repository vendor name, may be <code>null</code>
     */
    public function getVendorName();
}
