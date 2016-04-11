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

use Dkd\PhpCmis\Data\AclInterface;
use GuzzleHttp\Stream\StreamInterface;
use Dkd\PhpCmis\Data\ExtensionDataInterface;
use Dkd\PhpCmis\Data\ObjectDataInterface;
use Dkd\PhpCmis\Data\PropertiesInterface;
use Dkd\PhpCmis\Enum\IncludeRelationships;

/**
 * Versioning Service interface.
 *
 * See the CMIS 1.0 and CMIS 1.1 specifications for details on the operations,
 * parameters, exceptions and the domain model.
 */
interface VersioningServiceInterface
{
    /**
     * Reverses the effect of a check-out.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId the identifier for the PWC
     * @param ExtensionDataInterface|null $extension
     */
    public function cancelCheckOut($repositoryId, & $objectId, ExtensionDataInterface $extension = null);

    /**
     * Checks-in the private working copy (PWC) document.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId input: the identifier for the PWC,
     *      output: the identifier for the newly created version document
     * @param boolean $major indicator if the new version should become a major (<code>true</code>) or minor
     *      (<code>false</code>) version
     * @param PropertiesInterface|null $properties the property values that must be applied to the
     *      newly created document object
     * @param StreamInterface|null $contentStream the content stream that must be stored
     *      for the newly created document object
     * @param string|null $checkinComment a version comment
     * @param string[] $policies a list of policy IDs that must be applied to the newly created document object
     * @param AclInterface|null $addAces a list of ACEs that must be added to the newly created document object
     * @param AclInterface|null $removeAces a list of ACEs that must be removed from the newly created document object
     * @param ExtensionDataInterface|null $extension
	 * @return string|null Versioned object ID of original source if succesful, null otherwise
     */
    public function checkIn(
        $repositoryId,
        & $objectId,
        $major = true,
        PropertiesInterface $properties = null,
        StreamInterface $contentStream = null,
        $checkinComment = null,
        array $policies = array(),
        AclInterface $addAces = null,
        AclInterface $removeAces = null,
        ExtensionDataInterface $extension = null
    );

    /**
     * Create a private working copy of the document.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId input: the identifier for the document that should be checked out,
     *      output: the identifier for the newly created PWC
     * @param ExtensionDataInterface|null $extension
     * @param boolean|null $contentCopied output: indicator if the content of the original
     *      document has been copied to the PWC
	 * @return string|null Versioned object ID of PWC if succesful, null otherwise
     */
    public function checkOut(
        $repositoryId,
        & $objectId,
        ExtensionDataInterface $extension = null,
        $contentCopied = null
    );

    /**
     * Returns the list of all document objects in the specified version series,
     * sorted by the property "cmis:creationDate" descending.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId The identifier for the object
     * @param string $versionSeriesId the identifier for the object
     * @param string|null $filter a comma-separated list of query names that defines which properties must be
     *      returned by the repository (default is repository specific)
     * @param boolean $includeAllowableActions if <code>true</code>, then the repository must return the allowable
     *      actions for the objects (default is <code>false</code>)
     * @param ExtensionDataInterface|null $extension
     * @return ObjectDataInterface[] the complete version history of the version series
     */
    public function getAllVersions(
        $repositoryId,
        $objectId,
        $versionSeriesId,
        $filter = null,
        $includeAllowableActions = false,
        ExtensionDataInterface $extension = null
    );

    /**
     * Get the latest document object in the version series.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId The identifier for the object
     * @param string $versionSeriesId
     * @param boolean $major If <code>true</code>, then the repository MUST return the properties for the latest major
     *      version object in the version series.
     *      If <code>false</code>, the repository MUST return the properties for the latest
     *      (major or non-major) version object in the version series.
     * @param string|null $filter a comma-separated list of query names that defines which properties must be
     *      returned by the repository (default is repository specific)
     * @param boolean $includeAllowableActions if <code>true</code>, then the repository must return the allowable
     *      actions for the objects (default is <code>false</code>)
     * @param IncludeRelationships|null $includeRelationships indicates what relationships in which the objects
     *      participate must be returned (default is <code>IncludeRelationships::NONE</code>)
     * @param string $renditionFilter indicates what set of renditions the repository must return whose kind
     *      matches this filter (default is "cmis:none")
     * @param boolean $includePolicyIds if <code>true</code>, then the repository must return the policy ids for
     *      the object (default is <code>false</code>)
     * @param boolean $includeAcl
     * @param ExtensionDataInterface|null $extension
     * @return ObjectDataInterface
     */
    public function getObjectOfLatestVersion(
        $repositoryId,
        $objectId,
        $versionSeriesId,
        $major = false,
        $filter = null,
        $includeAllowableActions = false,
        IncludeRelationships $includeRelationships = null,
        $renditionFilter = Constants::RENDITION_NONE,
        $includePolicyIds = false,
        $includeAcl = false,
        ExtensionDataInterface $extension = null
    );

    /**
     * Get a subset of the properties for the latest document object in the version series.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId The identifier for the object
     * @param string $versionSeriesId The identifier for the version series.
     * @param boolean $major If <code>true</code>, then the repository MUST return the properties for the latest major
     *      version object in the version series.
     *      If <code>false</code>, the repository MUST return the properties for the latest
     *      (major or non-major) version object in the version series.
     * @param string|null $filter a comma-separated list of query names that defines which properties must be
     *      returned by the repository (default is repository specific)
     * @param ExtensionDataInterface|null $extension
     * @return PropertiesInterface
     */
    public function getPropertiesOfLatestVersion(
        $repositoryId,
        $objectId,
        $versionSeriesId,
        $major = false,
        $filter = null,
        ExtensionDataInterface $extension = null
    );
}
