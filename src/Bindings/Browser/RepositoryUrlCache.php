<?php
namespace Dkd\PhpCmis\Bindings\Browser;

/*
 * This file is part of php-cmis-client.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Constants;
use Dkd\PhpCmis\Exception\CmisInvalidArgumentException;
use League\Url\Url;

/**
 * URL cache for repository and root URLs.
 */
class RepositoryUrlCache
{
    /**
     * @var array
     */
    protected $repositoryUrls = [];

    /**
     * @var array
     */
    protected $rootUrls = [];

    /**
     * Adds the URLs of a repository to the cache.
     *
     * @param string $repositoryId
     * @param string $repositoryUrl
     * @param string $rootUrl
     */
    public function addRepository($repositoryId, $repositoryUrl, $rootUrl)
    {
        if (empty($repositoryId) || empty($repositoryUrl) || empty($rootUrl)) {
            throw new CmisInvalidArgumentException(
                'Repository Id or Repository URL or Root URL is not set!',
                1408536098
            );
        }

        $this->repositoryUrls[$repositoryId] = $repositoryUrl;
        $this->rootUrls[$repositoryId] = $rootUrl;
    }

    /**
     * Removes the URLs of a repository from the cache.
     *
     * @param string $repositoryId
     */
    public function removeRepository($repositoryId)
    {
        unset($this->repositoryUrls[$repositoryId]);
        unset($this->rootUrls[$repositoryId]);
    }

    /**
     * @param string $repositoryId
     * @return string|null
     */
    public function getRepositoryBaseUrl($repositoryId)
    {
        return $this->repositoryUrls[$repositoryId] ?? null;
    }

    /**
     * Returns the repository URL of a repository.
     *
     * @param string $repositoryId
     * @param string|null $selector add optional cmis selector parameter
     * @return Url|null
     */
    public function getRepositoryUrl($repositoryId, $selector = null)
    {
        $baseUrl = $this->getRepositoryBaseUrl($repositoryId);
        if ($baseUrl === null) {
            return null;
        }
        $repositoryUrl = $this->buildUrl($baseUrl);

        if ($selector !== null && $selector !== '') {
            $repositoryUrl->getQuery()->modify([Constants::PARAM_SELECTOR => $selector]);
        }

        return $repositoryUrl;
    }

    /**
     * Returns the root URL of a repository.
     *
     * @param string $repositoryId
     * @return string
     */
    public function getRootUrl($repositoryId)
    {
        return $this->rootUrls[$repositoryId] ?? null;
    }

    /**
     * Get URL for an object request
     *
     * @param string $repositoryId
     * @param string $objectId
     * @param string|null $selector
     * @return Url|null
     */
    public function getObjectUrl($repositoryId, $objectId, $selector = null)
    {
        if ($this->getRootUrl($repositoryId) === null) {
            return null;
        }

        $url = $this->buildUrl($this->getRootUrl($repositoryId));
        $urlQuery = $url->getQuery();
        $urlQuery->modify([Constants::PARAM_OBJECT_ID => (string) $objectId]);

        if (!empty($selector)) {
            $urlQuery->modify([Constants::PARAM_SELECTOR => (string) $selector]);
        }

        return $url;
    }

    /**
     * Get Repository URL with given path
     *
     * @param string $repositoryId
     * @param string $path
     * @param string|null $selector
     * @return Url
     */
    public function getPathUrl($repositoryId, $path, $selector = null)
    {
        if ($this->getRootUrl($repositoryId) === null) {
            return null;
        }

        $url = $this->buildUrl($this->getRootUrl($repositoryId));
        $url->getPath()->append($path);

        if (!empty($selector)) {
            $url->getQuery()->modify([Constants::PARAM_SELECTOR => $selector]);
        }

        return $url;
    }

    /**
     * Build an instance of \League\Url\Url for the given url
     *
     * @param string $url
     * @return Url
     */
    public function buildUrl($url)
    {
        return Url::createFromUrl($url);
    }
}
