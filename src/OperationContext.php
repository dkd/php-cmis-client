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
 * OperationContext implementation
 */
class OperationContext implements OperationContextInterface
{
    const PROPERTIES_WILDCARD = '*';

    /**
     * @var string[]
     */
    private $filter = array();

    /**
     * @var boolean
     */
    private $loadSecondaryTypeProperties = false;

    /**
     * @var boolean
     */
    private $includeAcls = false;

    /**
     * @var boolean
     */
    private $includeAllowableActions = true;

    /**
     * @var boolean
     */
    private $includePolicies = false;

    /**
     * @var IncludeRelationships
     */
    private $includeRelationships = null;

    /**
     * @var string[]
     */
    private $renditionFilter = array();

    /**
     * @var boolean
     */
    private $includePathSegments = true;

    /**
     * @var string
     */
    private $orderBy = null;

    /**
     * @var boolean
     */
    private $cacheEnabled = false;

    /**
     * @var integer
     */
    private $maxItemsPerPage = 100;

    /**
     * Creates new Operation Context
     */
    public function __construct()
    {
        $this->setRenditionFilter(array());
        $this->includeRelationships = IncludeRelationships::cast(IncludeRelationships::NONE);
    }

    /**
     * {@inheritdoc}
     */
    public function isCacheEnabled()
    {
        return $this->cacheEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function setCacheEnabled($cacheEnabled)
    {
        $this->cacheEnabled = (boolean) $cacheEnabled;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKey()
    {
        $cacheKey = $this->isIncludeAcls() ? '1' : '0';
        $cacheKey .= $this->isIncludeAllowableActions() ? '1' : '0';
        $cacheKey .= $this->isIncludePolicies() ? '1' : '0';
        $cacheKey .= $this->isIncludePathSegments() ? '1' : '0';
        $cacheKey .= '|';
        $cacheKey .= $this->getQueryFilterString();
        $cacheKey .= '|';
        $cacheKey .= (string) $this->getIncludeRelationships();
        $cacheKey .= '|';
        $cacheKey .= $this->getRenditionFilterString();

        return $cacheKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * {@inheritdoc}
     */
    public function setFilter(array $propertyFilters)
    {
        $filters = array();
        foreach ($propertyFilters as $filter) {
            $filter = trim((string) $filter);
            if ($filter === '') {
                continue;
            }

            if (self::PROPERTIES_WILDCARD === $filter) {
                $filters[] = self::PROPERTIES_WILDCARD;
                break;
            }

            if (stripos($filter, ',') !== false) {
                throw new \InvalidArgumentException('Filter must not contain a comma!');
            }

            $filters[] = $filter;
        }

        $this->filter = $filters;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isIncludeAcls()
    {
        return $this->includeAcls;
    }

    /**
     * {@inheritdoc}
     */
    public function setIncludeAcls($includeAcls)
    {
        $this->includeAcls = $includeAcls;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isIncludeAllowableActions()
    {
        return $this->includeAllowableActions;
    }

    /**
     * {@inheritdoc}
     */
    public function setIncludeAllowableActions($includeAllowableActions)
    {
        $this->includeAllowableActions = $includeAllowableActions;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isIncludePathSegments()
    {
        return $this->includePathSegments;
    }

    /**
     * {@inheritdoc}
     */
    public function setIncludePathSegments($includePathSegments)
    {
        $this->includePathSegments = $includePathSegments;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isIncludePolicies()
    {
        return $this->includePolicies;
    }

    /**
     * {@inheritdoc}
     */
    public function setIncludePolicies($includePolicies)
    {
        $this->includePolicies = $includePolicies;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIncludeRelationships()
    {
        return $this->includeRelationships;
    }

    /**
     * {@inheritdoc}
     */
    public function setIncludeRelationships(IncludeRelationships $includeRelationships)
    {
        $this->includeRelationships = $includeRelationships;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function loadSecondaryTypeProperties()
    {
        return $this->loadSecondaryTypeProperties;
    }

    /**
     * {@inheritdoc}
     */
    public function setLoadSecondaryTypeProperties($loadSecondaryTypeProperties)
    {
        $this->loadSecondaryTypeProperties = (boolean) $loadSecondaryTypeProperties;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxItemsPerPage()
    {
        return $this->maxItemsPerPage;
    }

    /**
     * {@inheritdoc}
     */
    public function setMaxItemsPerPage($maxItemsPerPage)
    {
        if ((int) $maxItemsPerPage < 1) {
            throw new \InvalidArgumentException('itemsPerPage must be > 0!');
        }
        $this->maxItemsPerPage = (int) $maxItemsPerPage;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderBy($orderBy)
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRenditionFilter()
    {
        return $this->renditionFilter;
    }

    /**
     * {@inheritdoc}
     */
    public function setRenditionFilter(array $renditionFilter)
    {
        $filters = array();
        foreach ($renditionFilter as $filter) {
            $filter = trim((string) $filter);
            if ($filter === '') {
                continue;
            }

            if (stripos($filter, ',') !== false) {
                throw new \InvalidArgumentException('Rendition must not contain a comma!');
            }

            $filters[] = $filter;
        }

        if (count($filters) === 0) {
            $filters[] = Constants::RENDITION_NONE;
        }

        $this->renditionFilter = $filters;

        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function getQueryFilterString()
    {
        if (count($this->filter) === 0) {
            return null;
        }

        if (array_search(self::PROPERTIES_WILDCARD, $this->filter)) {
            return self::PROPERTIES_WILDCARD;
        }

        $filters = $this->filter;
        $filters[] = PropertyIds::OBJECT_ID;
        $filters[] = PropertyIds::BASE_TYPE_ID;
        $filters[] = PropertyIds::OBJECT_TYPE_ID;

        if ($this->loadSecondaryTypeProperties()) {
            $filters[] = PropertyIds::SECONDARY_OBJECT_TYPE_IDS;
        }

        return implode(',', array_unique($filters));
    }

    /**
     * {@inheritdoc}
     */
    public function getRenditionFilterString()
    {
        if (count($this->renditionFilter) === 0) {
            return null;
        }

        return implode(',', $this->renditionFilter);
    }

    /**
     * {@inheritdoc}
     */
    public function setFilterString($propertyFilter)
    {
        if (empty($propertyFilter)) {
            $this->setFilter(array());
        } else {
            $this->setFilter(explode(',', $propertyFilter));
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setRenditionFilterString($renditionFilter)
    {
        if (empty($renditionFilter)) {
            $this->setRenditionFilter(array());
        } else {
            $this->setRenditionFilter(explode(',', $renditionFilter));
        }

        return $this;
    }
}
