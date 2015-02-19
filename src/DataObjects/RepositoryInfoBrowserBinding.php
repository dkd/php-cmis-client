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

use Dkd\Populate\PopulateTrait;

/**
 * Repository info data implementation including browser binding specific data.
 */
class RepositoryInfoBrowserBinding extends RepositoryInfo
{
    use PopulateTrait;

    /**
     * @var string
     */
    private $repositoryUrl = '';

    /**
     * @var string
     */
    private $rootUrl = '';

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
