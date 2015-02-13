<?php
namespace Dkd\PhpCmis\Bindings\Browser;

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
use Dkd\PhpCmis\VersioningServiceInterface;
use Dkd\PhpCmis\Enum\IncludeRelationships;

/**
 * Versioning Service Browser Binding client.
 */
class VersioningService extends AbstractBrowserBindingService implements VersioningServiceInterface
{
    /**
     * Reverses the effect of a check-out.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId the identifier for the PWC
     * @param ExtensionDataInterface|null $extension
     */
    public function cancelCheckOut($repositoryId, $objectId, ExtensionDataInterface $extension = null)
    {
        // TODO: Implement cancelCheckOut() method.
    }

    /**
     * Checks-in the private working copy (PWC) document.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId input: the identifier for the PWC,
     *      output: the identifier for the newly created version document
     * @param boolean $major indicator if the new version should become a major (true) or minor (false) version
     * @param PropertiesInterface|null $properties the property values that must be applied to the
     *      newly created document object
     * @param StreamInterface|null $contentStream the content stream that must be stored
     *      for the newly created document object
     * @param string|null $checkinComment a version comment
     * @param string[] $policies a list of policy IDs that must be applied to the newly created document object
     * @param AclInterface|null $addAces a list of ACEs that must be added to the newly created document object
     * @param AclInterface|null $removeAces a list of ACEs that must be removed from the newly created document object
     * @param ExtensionDataInterface|null $extension
     */
    public function checkIn(
        $repositoryId,
        $objectId,
        $major,
        PropertiesInterface $properties = null,
        StreamInterface $contentStream = null,
        $checkinComment = null,
        array $policies = array(),
        AclInterface $addAces = null,
        AclInterface $removeAces = null,
        ExtensionDataInterface $extension = null
    ) {
        // TODO: Implement checkIn() method.
    }

    /**
     * Create a private working copy of the document.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId input: the identifier for the document that should be checked out,
     *      output: the identifier for the newly created PWC
     * @param ExtensionDataInterface|null $extension
     * @param boolean|null $contentCopied output: indicator if the content of the original
     *      document has been copied to the PWC
     */
    public function checkOut(
        $repositoryId,
        & $objectId,
        ExtensionDataInterface $extension = null,
        $contentCopied = null
    ) {
        // TODO: Implement checkOut() method.
    }

    /**
     * Returns the list of all document objects in the specified version series,
     * sorted by the property "cmis:creationDate" descending.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId the identifier for the object
     * @param string $versionSeriesId the identifier for the object
     * @param string $filter a comma-separated list of query names that defines which properties must be
     *      returned by the repository (default is repository specific)
     * @param boolean $includeAllowableActions if true, then the repository must return the allowable
     *      actions for the objects (default is false)
     * @param ExtensionDataInterface|null $extension
     * @return ObjectDataInterface[] the complete version history of the version series
     */
    public function getAllVersions(
        $repositoryId,
        $objectId,
        $versionSeriesId,
        $filter,
        $includeAllowableActions,
        ExtensionDataInterface $extension = null
    ) {
        // TODO: Implement getAllVersions() method.
    }

    /**
     * Get the latest document object in the version series.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId
     * @param string $versionSeriesId
     * @param boolean $major
     * @param string $filter
     * @param boolean $includeAllowableActions
     * @param IncludeRelationships $includeRelationships
     * @param string $renditionFilter
     * @param boolean $includePolicyIds
     * @param boolean $includeAcl
     * @param ExtensionDataInterface|null $extension
     * @return ObjectDataInterface
     */
    public function getObjectOfLatestVersion(
        $repositoryId,
        $objectId,
        $versionSeriesId,
        $major,
        $filter,
        $includeAllowableActions,
        IncludeRelationships $includeRelationships,
        $renditionFilter,
        $includePolicyIds,
        $includeAcl,
        ExtensionDataInterface $extension = null
    ) {
        // TODO: Implement getObjectOfLatestVersion() method.
    }

    /**
     * Get a subset of the properties for the latest document object in the version series.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId
     * @param string $versionSeriesId
     * @param boolean $major
     * @param string|null $filter
     * @param ExtensionDataInterface|null $extension
     * @return PropertiesInterface
     */
    public function getPropertiesOfLatestVersion(
        $repositoryId,
        $objectId,
        $versionSeriesId,
        $major,
        $filter = null,
        ExtensionDataInterface $extension = null
    ) {
        // TODO: Implement getPropertiesOfLatestVersion() method.
    }
}
