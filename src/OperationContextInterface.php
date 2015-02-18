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

use Dkd\PhpCmis\Enum\IncludeRelationships;

/**
 * An OperationContext object defines the filtering, paging and caching of an operation.
 */
interface OperationContextInterface
{
    /**
     * Returns a key for this OperationContext object that is used for caching.
     *
     * @return string
     */
    public function getCacheKey();

    /**
     * Returns the current filter.
     *
     * @return string[] a set of query names
     */
    public function getFilter();

    /**
     * Returns the filter extended by cmis:objectId, cmis:objectTypeId and cmis:baseTypeId
     * as a string.
     *
     * @return string
     */
    public function getQueryFilterString();

    /**
     * Returns which relationships should be returned.
     *
     * @return IncludeRelationships
     */
    public function getIncludeRelationships();

    /**
     * Returns the current max number of items per batch.
     *
     * @return integer
     */
    public function getMaxItemsPerPage();

    /**
     * Returns the order by rule for operations that return lists.
     *
     * @return string a comma-separated list of query names and the ascending modifier "ASC" or
     * the descending modifier "DESC" for each query name
     */
    public function getOrderBy();

    /**
     * Returns the current rendition filter.
     *
     * @return string[] a set of rendition filter terms
     */
    public function getRenditionFilter();

    /**
     * Returns the current rendition filter. (See CMIS spec "2.2.1.2.4.1 Rendition Filter Grammar")
     *
     * @return string a comma separated list of rendition filter terms
     */
    public function getRenditionFilterString();

    /**
     * Return if caching is enabled.
     *
     * @return boolean
     */
    public function isCacheEnabled();

    /**
     * Returns if ACLs should returned.
     *
     * @return boolean
     */
    public function isIncludeAcls();

    /**
     * Returns if allowable actions should returned.
     *
     * @return boolean
     */
    public function isIncludeAllowableActions();

    /**
     * Returns if path segments should returned.
     *
     * @return boolean
     */
    public function isIncludePathSegments();

    /**
     * Returns if policies should returned.
     *
     * @return boolean
     */
    public function isIncludePolicies();

    /**
     * Returns is secondary type properties should be loaded.
     *
     * @return boolean
     */
    public function loadSecondaryTypeProperties();

    /**
     * Enables or disables the cache.
     *
     * @param boolean $cacheEnabled
     */
    public function setCacheEnabled($cacheEnabled);

    /**
     * Sets the current filter.
     *
     * @param array $propertyFilter a set of query names
     */
    public function setFilter(array $propertyFilter);

    /**
     * Sets the current filter.
     *
     * @param string $propertyFilter a comma separated string of query names or "*" for all properties or
     *      <code>null</code> to let the repository determine a set of properties
     */
    public function setFilterString($propertyFilter);

    /**
     * Sets if ACLs should returned.
     *
     * @param boolean $include
     */
    public function setIncludeAcls($include);

    /**
     * Sets if allowable actions should returned.
     *
     * @param boolean $include
     */
    public function setIncludeAllowableActions($include);

    /**
     * Sets if path segments should returned.
     *
     * @param boolean $include
     */
    public function setIncludePathSegments($include);

    /**
     * Sets if policies should returned.
     *
     * @param boolean $include
     */
    public function setIncludePolicies($include);

    /**
     * Sets which relationships should be returned.
     *
     * @param IncludeRelationships $include
     */
    public function setIncludeRelationships(IncludeRelationships $include);

    /**
     * Sets if secondary type properties should be loaded.
     *
     * @param boolean $load
     */
    public function setLoadSecondaryTypeProperties($load);

    /**
     * Set the max number of items per batch for operations that return lists.
     *
     * @param integer $maxItemsPerPage max number of items (must be >0)
     */
    public function setMaxItemsPerPage($maxItemsPerPage);

    /**
     * Sets the order by rule for operations that return lists.
     *
     * @param string $orderBy a comma-separated list of query names and the ascending modifier "ASC" or
     * the descending modifier "DESC" for each query name
     */
    public function setOrderBy($orderBy);

    /**
     * Sets the current rendition filter.
     *
     * @param string[] $renditionFilter
     */
    public function setRenditionFilter(array $renditionFilter);

    /**
     * Sets the current rendition filter.
     *
     * @param string $renditionFilter a comma separated list of rendition filter terms
     */
    public function setRenditionFilterString($renditionFilter);
}
