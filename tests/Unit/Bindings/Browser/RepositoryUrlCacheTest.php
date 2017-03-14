<?php
namespace Dkd\PhpCmis\Test\Unit\Bindings\Browser;

/*
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Bindings\Browser\RepositoryUrlCache;
use Dkd\PhpCmis\Constants;
use League\Url\Url;

/**
 * Class RepositoryUrlCacheTest
 */
class RepositoryUrlCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var RepositoryUrlCache
     */
    protected $repositoryUrlCache;

    /**
     * @var array
     */
    protected $dummyData = [
        [
            'id' => 'repositoryId',
            'repositoryUrl' => 'http://foo.bar.baz/repositoryUrl',
            'rootUrl' => 'http://foo.bar.baz/rootUrl'
        ],
        [
            'id' => 'repositoryId2',
            'repositoryUrl' => 'http://foo.bar.baz/repositoryUrl2',
            'rootUrl' => 'http://foo.bar.baz/rootUrl2'
        ],
    ];

    public function setUp()
    {
        $this->repositoryUrlCache = new RepositoryUrlCache();
    }

    /**
     * @dataProvider addRepositoryParameterDataProvider
     * @param $repositoryId
     * @param $repositoryUrl
     * @param $rootUrl
     */
    public function testAddRepositoryThrowsExceptionIfEmptyParameterGiven($repositoryId, $repositoryUrl, $rootUrl)
    {
        $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException');
        $this->repositoryUrlCache->addRepository($repositoryId, $repositoryUrl, $rootUrl);
    }

    public function addRepositoryParameterDataProvider()
    {
        return [
            [
                null,
                'foo',
                'bar'
            ],
            [
                'foo',
                null,
                'bar'
            ],
            [
                'foo',
                'bar',
                null
            ],
        ];
    }

    public function testAddRepositoryAddsGivenUrlsToArrayCache()
    {
        $this->repositoryUrlCache->addRepository(
            $this->dummyData[0]['id'],
            $this->dummyData[0]['repositoryUrl'],
            $this->dummyData[0]['rootUrl']
        );

        $this->assertAttributeSame(
            [$this->dummyData[0]['id'] => $this->dummyData[0]['repositoryUrl']],
            'repositoryUrls',
            $this->repositoryUrlCache
        );
        $this->assertAttributeSame(
            [$this->dummyData[0]['id'] => $this->dummyData[0]['rootUrl']],
            'rootUrls',
            $this->repositoryUrlCache
        );

        $this->repositoryUrlCache->addRepository(
            $this->dummyData[1]['id'],
            $this->dummyData[1]['repositoryUrl'],
            $this->dummyData[1]['rootUrl']
        );

        $this->assertAttributeSame(
            [
                $this->dummyData[0]['id'] => $this->dummyData[0]['repositoryUrl'],
                $this->dummyData[1]['id'] => $this->dummyData[1]['repositoryUrl']
            ],
            'repositoryUrls',
            $this->repositoryUrlCache
        );
        $this->assertAttributeSame(
            [
                $this->dummyData[0]['id'] => $this->dummyData[0]['rootUrl'],
                $this->dummyData[1]['id'] => $this->dummyData[1]['rootUrl']
            ],
            'rootUrls',
            $this->repositoryUrlCache
        );

        return $this->repositoryUrlCache;
    }

    /**
     * @depends testAddRepositoryAddsGivenUrlsToArrayCache
     * @param $repositoryUrlCache RepositoryUrlCache
     */
    public function testRemoveRepositoryRemovesRepositoryFromCache($repositoryUrlCache)
    {
        $repositoryUrlCache = clone $repositoryUrlCache;
        $repositoryUrlCache->removeRepository($this->dummyData[1]['id']);

        $this->assertAttributeSame(
            [$this->dummyData[0]['id'] => $this->dummyData[0]['repositoryUrl']],
            'repositoryUrls',
            $repositoryUrlCache
        );
        $this->assertAttributeSame(
            [$this->dummyData[0]['id'] => $this->dummyData[0]['rootUrl']],
            'rootUrls',
            $repositoryUrlCache
        );
    }

    /**
     * @depends testAddRepositoryAddsGivenUrlsToArrayCache
     * @param $repositoryUrlCache RepositoryUrlCache
     */
    public function testGetRepositoryBaseUrlReturnsUrlForGivenRepositoryId($repositoryUrlCache)
    {
        $this->assertSame(
            $this->dummyData[0]['repositoryUrl'],
            $repositoryUrlCache->getRepositoryBaseUrl($this->dummyData[0]['id'])
        );
        $this->assertSame(
            $this->dummyData[1]['repositoryUrl'],
            $repositoryUrlCache->getRepositoryBaseUrl($this->dummyData[1]['id'])
        );
    }

    /**
     * @depends testAddRepositoryAddsGivenUrlsToArrayCache
     * @param $repositoryUrlCache RepositoryUrlCache
     */
    public function testGetRepositoryUrlReturnsNullIfNoBaseUrlForIdIsFoundInCache($repositoryUrlCache)
    {
        $this->assertNull($repositoryUrlCache->getRepositoryUrl('invalid-id'));
    }

    /**
     * @depends testAddRepositoryAddsGivenUrlsToArrayCache
     * @param $repositoryUrlCache RepositoryUrlCache
     */
    public function testGetRepositoryUrlReturnsInstanceOfUrlBasedOnBaseUrlString($repositoryUrlCache)
    {
        $expectedUrl = Url::createFromUrl($this->dummyData[0]['repositoryUrl']);
        $this->assertEquals($expectedUrl, $repositoryUrlCache->getRepositoryUrl($this->dummyData[0]['id']));
    }

    /**
     * @depends testAddRepositoryAddsGivenUrlsToArrayCache
     * @param $repositoryUrlCache RepositoryUrlCache
     */
    public function testGetRepositoryUrlWithSelectorReturnsInstanceOfUrlBasedOnBaseUrlStringWithSelector(
        $repositoryUrlCache
    ) {
        $selector = 'fooSelector';
        $expectedUrl = Url::createFromUrl(
            $this->dummyData[0]['repositoryUrl'] . '?' . Constants::PARAM_SELECTOR . '=' . $selector
        );
        $this->assertEquals($expectedUrl, $repositoryUrlCache->getRepositoryUrl($this->dummyData[0]['id'], $selector));
    }

    /**
     * @depends testAddRepositoryAddsGivenUrlsToArrayCache
     * @param $repositoryUrlCache RepositoryUrlCache
     */
    public function testGetRootUrlReturnsNullIfNoUrlForIdIsFoundInCache($repositoryUrlCache)
    {
        $this->assertNull($repositoryUrlCache->getRootUrl('invalid-id'));
    }

    /**
     * @depends testAddRepositoryAddsGivenUrlsToArrayCache
     * @param $repositoryUrlCache RepositoryUrlCache
     */
    public function testGetRootUrlReturnsInstanceOfUrlBasedOnBaseUrlString($repositoryUrlCache)
    {
        $expectedUrl = Url::createFromUrl($this->dummyData[0]['rootUrl']);
        $this->assertEquals($expectedUrl, $repositoryUrlCache->getRootUrl($this->dummyData[0]['id']));
    }

    /**
     * @depends testAddRepositoryAddsGivenUrlsToArrayCache
     * @param $repositoryUrlCache RepositoryUrlCache
     */
    public function testGetObjectUrlReturnsNullIfNoRootUrlForIdIsFoundInCache($repositoryUrlCache)
    {
        $this->assertNull($repositoryUrlCache->getObjectUrl('invalid-id', 'foo-id'));
    }

    /**
     * @depends testAddRepositoryAddsGivenUrlsToArrayCache
     * @param $repositoryUrlCache RepositoryUrlCache
     */
    public function testGetObjectUrlReturnsInstanceOfUrlBasedOnBaseUrlStringWithObjectIdSelector($repositoryUrlCache)
    {
        $objectId = 'object-id';
        $expectedUrl = Url::createFromUrl(
            $this->dummyData[0]['rootUrl'] . '?' . Constants::PARAM_OBJECT_ID . '=' . $objectId
        );
        $this->assertEquals($expectedUrl, $repositoryUrlCache->getObjectUrl($this->dummyData[0]['id'], $objectId));
    }

    /**
     * @depends testAddRepositoryAddsGivenUrlsToArrayCache
     * @param $repositoryUrlCache RepositoryUrlCache
     */
    public function testGetObjectUrlWithSelectorReturnsInstanceOfUrlBasedOnBaseUrlStringWithObjectIdSelectorAndSelector(
        $repositoryUrlCache
    ) {
        $selector = 'fooSelector';
        $objectId = 'object-id';
        $expectedUrl = Url::createFromUrl(
            $this->dummyData[0]['rootUrl'] . '?'
            . Constants::PARAM_OBJECT_ID . '=' . $objectId . '&'
            . Constants::PARAM_SELECTOR . '=' . $selector
        );
        $this->assertEquals(
            $expectedUrl,
            $repositoryUrlCache->getObjectUrl($this->dummyData[0]['id'], $objectId, $selector)
        );
    }

    /**
     * @depends testAddRepositoryAddsGivenUrlsToArrayCache
     * @param $repositoryUrlCache RepositoryUrlCache
     */
    public function testGetPathUrlReturnsNullIfNoRootUrlForIdIsFoundInCache($repositoryUrlCache)
    {
        $this->assertNull($repositoryUrlCache->getPathUrl('invalid-id', 'foo-path'));
    }

    /**
     * @depends testAddRepositoryAddsGivenUrlsToArrayCache
     * @param $repositoryUrlCache RepositoryUrlCache
     */
    public function testGetPathUrlReturnsInstanceOfUrlBasedOnBaseUrlStringWithObjectIdSelector($repositoryUrlCache)
    {
        $path = '/foo/bar/baz';
        $expectedUrl = Url::createFromUrl(
            $this->dummyData[0]['rootUrl'] . $path
        );
        $this->assertEquals($expectedUrl, $repositoryUrlCache->getPathUrl($this->dummyData[0]['id'], $path));
    }

    /**
     * @depends testAddRepositoryAddsGivenUrlsToArrayCache
     * @param $repositoryUrlCache RepositoryUrlCache
     */
    public function testGetPathUrlWithSelectorReturnsInstanceOfUrlBasedOnBaseUrlStringWithObjectIdSelectorAndSelector(
        $repositoryUrlCache
    ) {
        $selector = 'fooSelector';
        $path = '/foo/bar/baz';
        $expectedUrl = Url::createFromUrl(
            $this->dummyData[0]['rootUrl'] . $path . '?' . Constants::PARAM_SELECTOR . '=' . $selector
        );
        $this->assertEquals(
            $expectedUrl,
            $repositoryUrlCache->getPathUrl($this->dummyData[0]['id'], $path, $selector)
        );
    }

    public function testBuildUrlReturnsUrlInstanceBasedOnGivenUrlString()
    {
        $urlString = 'http://foo.bar.baz';
        $url = Url::createFromUrl($urlString);
        $this->assertEquals($url, $this->repositoryUrlCache->buildUrl($urlString));
    }
}
