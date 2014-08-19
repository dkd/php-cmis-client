<?php
namespace Dkd\PhpCmis;

use Dkd\PhpCmis\Data\ContentStreamInterface;
use Dkd\PhpCmis\Data\ExtensionsDataInterface;
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
     * @param ExtensionsDataInterface $extension
     * @return void
     */
    public function cancelCheckOut($repositoryId, $objectId, ExtensionsDataInterface $extension = null);

    /**
     * Checks-in the private working copy (PWC) document.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId input: the identifier for the PWC,
     * output: the identifier for the newly created version document
     * @param boolean $major indicator if the new version should become a major (true) or minor (false) version
     * @param PropertiesInterface $properties the property values that must be applied to the
     * newly created document object
     * @param ContentStreamInterface $contentStream the content stream that must be stored
     * for the newly created document object
     * @param String $checkinComment a version comment
     * @param string[] $policies a list of policy IDs that must be applied to the newly created document object
     * @param AclInterface $addAces a list of ACEs that must be added to the newly created document object
     * @param AclInterface $removeAces a list of ACEs that must be removed from the newly created document object
     * @param ExtensionsDataInterface $extension
     * @return void
     */
    public function checkIn(
        $repositoryId,
        $objectId,
        $major,
        PropertiesInterface $properties = null,
        ContentStreamInterface $contentStream = null,
        $checkinComment = null,
        array $policies = array(),
        AclInterface $addAces = null,
        AclInterface $removeAces = null,
        ExtensionsDataInterface $extension = null
    );

    /**
     * Create a private working copy of the document.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId input: the identifier for the document that should be checked out,
     * output: the identifier for the newly created PWC
     * @param ExtensionsDataInterface $extension
     * @param boolean $contentCopied output: indicator if the content of the original
     * document has been copied to the PWC
     * @return void
     */
    public function checkOut($repositoryId, $objectId, ExtensionsDataInterface $extension, $contentCopied);

    /**
     * Returns the list of all document objects in the specified version series,
     * sorted by the property "cmis:creationDate" descending.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId the identifier for the object
     * @param string $versionSeriesId the identifier for the object
     * @param string $filter a comma-separated list of query names that defines which properties must be
     * returned by the repository (default is repository specific)
     * @param boolean $includeAllowableActions if true, then the repository must return the allowable
     * actions for the objects (default is false)
     * @param ExtensionsDataInterface $extension
     * @return ObjectDataInterface[] the complete version history of the version series
     */
    public function getAllVersions(
        $repositoryId,
        $objectId,
        $versionSeriesId,
        $filter,
        $includeAllowableActions,
        ExtensionsDataInterface $extension
    );

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
     * @param ExtensionsDataInterface $extension
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
        ExtensionsDataInterface $extension = null
    );

    /**
     * Get a subset of the properties for the latest document object in the version series.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId
     * @param string $versionSeriesId
     * @param boolean $major
     * @param string $filter
     * @param ExtensionsDataInterface $extension
     * @return PropertiesInterface
     */
    public function getPropertiesOfLatestVersion(
        $repositoryId,
        $objectId,
        $versionSeriesId,
        $major,
        $filter = null,
        ExtensionsDataInterface $extension = null
    );
}
