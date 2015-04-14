<?php
namespace Dkd\PhpCmis\DataObjects;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Repository info data implementation including browser binding specific data.
 */
class RepositoryInfoBrowserBinding extends RepositoryInfo
{
    /**
     * @var string
     */
    protected $repositoryUrl = '';

    /**
     * @var string
     */
    protected $rootUrl = '';

    /**
     * @return string
     */
    public function getRepositoryUrl()
    {
        return $this->repositoryUrl;
    }

    /**
     * @param string $repositoryUrl
     */
    public function setRepositoryUrl($repositoryUrl)
    {
        $this->repositoryUrl = $this->castValueToSimpleType('string', $repositoryUrl);
    }

    /**
     * @return string
     */
    public function getRootUrl()
    {
        return $this->rootUrl;
    }

    /**
     * @param string $rootUrl
     */
    public function setRootUrl($rootUrl)
    {
        $this->rootUrl = $this->castValueToSimpleType('string', $rootUrl);
    }
}
