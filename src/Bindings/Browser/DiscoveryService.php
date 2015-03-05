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

use Dkd\PhpCmis\Constants;
use Dkd\PhpCmis\Data;
use Dkd\PhpCmis\Data\ExtensionDataInterface;
use Dkd\PhpCmis\Data\ObjectListInterface;
use Dkd\PhpCmis\DiscoveryServiceInterface;
use Dkd\PhpCmis\Enum;
use Dkd\PhpCmis\Enum\IncludeRelationships;

/**
 * Discovery Service Browser Binding client.
 */
class DiscoveryService extends AbstractBrowserBindingService implements DiscoveryServiceInterface
{
    /**
     * Gets a list of content changes.
     *
     * @param string $repositoryId The identifier for the repository.
     * @param string|null $changeLogToken If specified, then the repository MUST return the change event corresponding
     *      to the value of the specified change log token as the first result in the output.
     * If not specified, then the repository MUST return the first change event recorded in the change log.
     * @param boolean $includeProperties If <code>true</code>, then the repository MUST include the updated property
     *      values for "updated" change events if the repository supports returning property values as specified by
     *      capbilityChanges.
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
    ) {
        $url = $this->getRepositoryUrl($repositoryId, Constants::SELECTOR_CONTENT_CHANGES);

        $url->getQuery()->modify(
            array(
                Constants::PARAM_PROPERTIES => $includeProperties ? 'true' : 'false',
                Constants::PARAM_POLICY_IDS => $includePolicyIds ? 'true' : 'false',
                Constants::PARAM_ACL => $includeAcl ? 'true' : 'false',
                Constants::PARAM_SUCCINCT => $this->getSuccinct() ? 'true' : 'false',
            )
        );

        if ($changeLogToken !== null) {
            $url->getQuery()->modify(array(Constants::PARAM_CHANGE_LOG_TOKEN => (string) $changeLogToken));
        }

        if ($maxItems > 0) {
            $url->getQuery()->modify(array(Constants::PARAM_MAX_ITEMS => (string) $maxItems));
        }

        $responseData = $this->read($url)->json();

        // $changeLogToken was passed by reference. The value is changed here
        if ($changeLogToken!==null && isset($responseData[JSONConstants::JSON_OBJECTLIST_CHANGE_LOG_TOKEN])) {
            $changeLogToken = (string) $responseData[JSONConstants::JSON_OBJECTLIST_CHANGE_LOG_TOKEN];
        }

        // TODO Implement Cache
        return $this->getJsonConverter()->convertObjectList($responseData);
    }

    /**
     * Executes a CMIS query statement against the contents of the repository.
     *
     * @param string $repositoryId The identifier for the repository.
     * @param string $statement CMIS query to be executed
     * @param boolean $searchAllVersions If <code>true</code>, then the repository MUST include latest and non-latest
     *      versions of document objects in the query search scope.
     *      If <code>false</code>, then the repository MUST only include latest versions of documents in the
     *      query search scope.
     *      If the repository does not support the optional capabilityAllVersionsSearchable capability, then this
     *      parameter value MUST be set to <code>false</code>.
     * @param IncludeRelationships|null $includeRelationships indicates what relationships in which the objects
     *      participate must be returned (default is <code>IncludeRelationships::NONE</code>)
     * @param string $renditionFilter The Repository MUST return the set of renditions whose kind matches this
     *      filter. See section below for the filter grammar. Defaults to "cmis:none".
     * @param boolean $includeAllowableActions if <code>true</code>, then the repository must return the available
     *      actions for each object in the result set (default is <code>false</code>)
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
    ) {
        $url = $this->getRepositoryUrl($repositoryId);

        $url->getQuery()->modify(
            array(
                Constants::CONTROL_CMISACTION => Constants::CMISACTION_QUERY,
                Constants::PARAM_STATEMENT => (string) $statement,
                Constants::PARAM_SEARCH_ALL_VERSIONS => $searchAllVersions ? 'true' : 'false',
                Constants::PARAM_ALLOWABLE_ACTIONS => $includeAllowableActions ? 'true' : 'false',
                Constants::PARAM_RENDITION_FILTER => $renditionFilter,
                Constants::PARAM_SKIP_COUNT => (string) $skipCount,
                Constants::PARAM_DATETIME_FORMAT => (string) $this->getDateTimeFormat()
            )
        );

        if ($includeRelationships !== null) {
            $url->getQuery()->modify(array(Constants::PARAM_RELATIONSHIPS => (string) $includeRelationships));
        }

        if ($maxItems > 0) {
            $url->getQuery()->modify(array(Constants::PARAM_MAX_ITEMS => (string) $maxItems));
        }

        $responseData = $this->post($url)->json();

        return $this->getJsonConverter()->convertQueryResultList($responseData);
    }
}
