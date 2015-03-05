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

use Dkd\PhpCmis\Data\ExtensionDataInterface;
use Dkd\PhpCmis\Data\ObjectListInterface;
use Dkd\PhpCmis\Enum\IncludeRelationships;

/**
 * Discovery Service interface.
 *
 * See the CMIS 1.0 and CMIS 1.1 specifications for details on the operations,
 * parameters, exceptions and the domain model.
 */
interface DiscoveryServiceInterface
{
    /**
     * Gets a list of content changes.
     *
     * @param string $repositoryId The identifier for the repository.
     * @param string|null $changeLogToken If specified, then the repository MUST return the change event corresponding
     *      to the value of the specified change log token as the first result in the output.
     *      If not specified, then the repository MUST return the first change event recorded in the change log.
     * @param boolean $includeProperties If <code>true</code>, then the repository MUST include the updated property
     *      values for "updated" change events if the repository supports returning property values as specified
     *      by capbilityChanges.
     *      If <code>false</code>, then the repository MUST NOT include the updated property values for
     *      "updated" change events. The single exception to this is that the property cmis:objectId MUST always
     *      be included.
     * @param boolean $includePolicyIds If <code>true</code>, then the repository MUST include the ids of the policies
     *      applied to the object referenced in each change event, if the change event modified the set of policies
     *      applied to the object.
     *      If <code>false</code>, then the repository MUST not include policy information.
     * @param boolean $includeAcl If <code>true</code>, then the repository MUST return the ACLs for each object in the
     *      result set. Defaults to <code>false</code>.
     * @param integer|null $maxItems the maximum number of items to return in a response
     *      (default is repository specific)
     * @param ExtensionDataInterface|null $extension
     * @return ObjectListInterface
     */
    public function getContentChanges(
        $repositoryId,
        &$changeLogToken = null,
        $includeProperties = false,
        $includePolicyIds = false,
        $includeAcl = false,
        $maxItems = null,
        ExtensionDataInterface $extension = null
    );

    /**
     * Executes a CMIS query statement against the contents of the repository.
     *
     * @param string $repositoryId The identifier for the repository.
     * @param string $statement CMIS query to be executed
     * @param boolean $searchAllVersions If <code>true</code>, then the repository MUST include latest and non-latest
     *      versions of document objects in the query search scope.
     *      If <code>false</code>, then the repository MUST only include latest versions of documents in
     *      the query search scope.
     *      If the repository does not support the optional capabilityAllVersionsSearchable capability, then this
     *      parameter value MUST be set to <code>false</code>.
     * @param IncludeRelationships|null $includeRelationships For query statements where the SELECT clause contains
     *      properties from only one virtual table reference (i.e. referenced object-type), any value for this enum may
     *      be used. If the SELECT clause contains properties from more than one table, then the value of this
     *      parameter MUST be none. Defaults to none.
     * @param string $renditionFilter The Repository MUST return the set of renditions whose kind matches this
     *      filter. See section below for the filter grammar. Defaults to "cmis:none".
     * @param boolean $includeAllowableActions if true, then the repository must return the available actions for
     *      each object in the result set (default is <code>false</code>)
     * @param integer|null $maxItems the maximum number of items to return in a response
     *      (default is repository specific)
     * @param integer $skipCount number of potential results that the repository MUST skip/page over before
     *      returning any results (default is 0)
     * @param ExtensionDataInterface|null $extension
     * @return ObjectListInterface|null Returns object of type <code>ObjectListInterface</code>
     *     or <code>null</code> if the repository response was empty
     */
    public function query(
        $repositoryId,
        $statement,
        $searchAllVersions = false,
        IncludeRelationships $includeRelationships = null,
        $renditionFilter = Constants::RENDITION_NONE,
        $includeAllowableActions = false,
        $maxItems = null,
        $skipCount = 0,
        ExtensionDataInterface $extension = null
    );
}
