<?php
namespace Dkd\PhpCmis\Test\Unit\Bindings\Browser;

/*
 * This file is part of php-cmis-lib.
 *
 * (c) Dimitri Ebert <dimitri.ebert@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Bindings\Browser\DiscoveryService;
use Dkd\PhpCmis\Constants;
use Dkd\PhpCmis\DataObjects\ObjectData;
use Dkd\PhpCmis\DataObjects\ObjectList;
use Dkd\PhpCmis\Enum\IncludeRelationships;
use GuzzleHttp\Psr7\Response;
use League\Url\Url;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Class DiscoveryServiceTest
 */
class DiscoveryServiceTest extends AbstractBrowserBindingServiceTestCase
{
    const CLASS_TO_TEST = DiscoveryService::class;

    /**
     * @dataProvider queryDataProvider
     * @param $expectedUrl
     * @param string $repositoryId
     * @param string $statement
     * @param boolean $searchAllVersions
     * @param IncludeRelationships|null $includeRelationships
     * @param string $renditionFilter
     * @param boolean $includeAllowableActions
     * @param integer|null $maxItems
     * @param integer $skipCount
     */
    public function testQueryReturnsNullIfRepositoryReturnsEmptyResponse(
        $expectedUrl,
        $repositoryId,
        $statement,
        $searchAllVersions = false,
        IncludeRelationships $includeRelationships = null,
        $renditionFilter = Constants::RENDITION_NONE,
        $includeAllowableActions = false,
        $maxItems = null,
        $skipCount = 0
    ) {
        $responseMock = $this->getMockBuilder(Response::class)->disableOriginalConstructor()
            ->setMethods(['getBody'])->getMock();
        $responseMock->expects($this->once())->method('getbody')->willReturn('{}');

        $jsonConverterMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Converter\\JsonConverter')->getMock();

        $cmisBindingsHelperMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBindingsHelper')->setMethods(
            ['getJsonConverter']
        )->getMock();
        $cmisBindingsHelperMock->expects($this->once())->method('getJsonConverter')->willReturn($jsonConverterMock);

        /** @var DiscoveryService|PHPUnit_Framework_MockObject_MockObject $discoveryService */
        $discoveryService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            [$this->getSessionMock(), $cmisBindingsHelperMock]
        )->setMethods(
            ['getRepositoryUrl', 'post']
        )->getMock();

        $discoveryService->expects($this->once())->method('getRepositoryUrl')->with(
            $repositoryId
        )->willReturn(Url::createFromUrl(self::BROWSER_URL_TEST));
        $discoveryService->expects($this->once())->method('post')->with($expectedUrl)->willReturn($responseMock);

        $this->assertSame(
            null,
            $discoveryService->query(
                $repositoryId,
                $statement,
                $searchAllVersions,
                $includeRelationships,
                $renditionFilter,
                $includeAllowableActions,
                $maxItems,
                $skipCount
            )
        );
    }

    /**
     * @dataProvider queryDataProvider
     * @param $expectedUrl
     * @param string $repositoryId
     * @param string $statement
     * @param boolean $searchAllVersions
     * @param IncludeRelationships|null $includeRelationships
     * @param string $renditionFilter
     * @param boolean $includeAllowableActions
     * @param integer|null $maxItems
     * @param integer $skipCount
     */
    public function testQueryCallsPostFunctionWithParameterizedQuery(
        $expectedUrl,
        $repositoryId,
        $statement,
        $searchAllVersions = false,
        IncludeRelationships $includeRelationships = null,
        $renditionFilter = Constants::RENDITION_NONE,
        $includeAllowableActions = false,
        $maxItems = null,
        $skipCount = 0
    ) {
        $responseData = ['foo' => 'bar'];
        $responseMock = $this->getMockBuilder(Response::class)->disableOriginalConstructor(
        )->setMethods(['getBody'])->getMock();
        $responseMock->expects($this->once())->method('getBody')->willReturn(json_encode($responseData));

        $dummyObjectData = new ObjectData();
        $jsonConverterMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Converter\\JsonConverter')->setMethods(
            ['convertQueryResultList']
        )->getMock();
        $jsonConverterMock->expects($this->once())->method('convertQueryResultList')->with($responseData)->willReturn(
            $dummyObjectData
        );

        $cmisBindingsHelperMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBindingsHelper')->setMethods(
            ['getJsonConverter']
        )->getMock();
        $cmisBindingsHelperMock->expects($this->once())->method('getJsonConverter')->willReturn($jsonConverterMock);

        /** @var DiscoveryService|PHPUnit_Framework_MockObject_MockObject $discoveryService */
        $discoveryService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            [$this->getSessionMock(), $cmisBindingsHelperMock]
        )->setMethods(
            ['getRepositoryUrl', 'post']
        )->getMock();

        $discoveryService->expects($this->once())->method('getRepositoryUrl')->with(
            $repositoryId
        )->willReturn(Url::createFromUrl(self::BROWSER_URL_TEST));
        $discoveryService->expects($this->any())->method('post')->with($expectedUrl)->willReturn($responseMock);

        $discoveryService->query(
            $repositoryId,
            $statement,
            $searchAllVersions,
            $includeRelationships,
            $renditionFilter,
            $includeAllowableActions,
            $maxItems,
            $skipCount
        );
    }

    /**
     * @dataProvider queryDataProvider
     * @param $expectedUrl
     * @param string $repositoryId
     * @param string $statement
     * @param boolean $searchAllVersions
     * @param IncludeRelationships|null $includeRelationships
     * @param string $renditionFilter
     * @param boolean $includeAllowableActions
     * @param integer|null $maxItems
     * @param integer $skipCount
     */
    public function testQueryReturnObjectList(
        $expectedUrl,
        $repositoryId,
        $statement,
        $searchAllVersions = false,
        IncludeRelationships $includeRelationships = null,
        $renditionFilter = Constants::RENDITION_NONE,
        $includeAllowableActions = false,
        $maxItems = null,
        $skipCount = 0
    ) {
        $responseData = $this->getResponseFixtureContentAsArray('Cmis/v1.1/BrowserBinding/doQuery-response.log');
        $responseMock = $this->getMockBuilder('\\GuzzleHttp\\Message\\Response')->disableOriginalConstructor(
        )->setMethods(['getBody'])->getMock();
        $responseMock->expects($this->once())->method('getBody')->willReturn(json_encode($responseData));

        $dummyObjectData = new ObjectData();

        $jsonConverterMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Converter\\JsonConverter')->setMethods(
            ['convertObject']
        )->getMock();

        $expectedNumberOfItems = 4;
        $jsonConverterMock->expects($this->exactly($expectedNumberOfItems))->method('convertObject')->willReturn(
            $dummyObjectData
        );

        $cmisBindingsHelperMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBindingsHelper')->setMethods(
            ['getJsonConverter']
        )->getMock();
        $cmisBindingsHelperMock->expects($this->once())->method('getJsonConverter')->willReturn($jsonConverterMock);

        /** @var DiscoveryService|PHPUnit_Framework_MockObject_MockObject $discoveryService */
        $discoveryService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            [$this->getSessionMock(), $cmisBindingsHelperMock]
        )->setMethods(
            ['getRepositoryUrl', 'post']
        )->getMock();

        $discoveryService->expects($this->once())->method('getRepositoryUrl')->with(
            $repositoryId
        )->willReturn(Url::createFromUrl(self::BROWSER_URL_TEST));
        $discoveryService->expects($this->once())->method('post')->with($expectedUrl)->willReturn($responseMock);

        $expectedObjectList = new ObjectList();
        $expectedObjectList->setObjects(array_fill(0, $expectedNumberOfItems, new ObjectData()));
        $expectedObjectList->setNumItems($expectedNumberOfItems);
        $expectedObjectList->hasMoreItems(false);

        $this->assertEquals(
            $expectedObjectList,
            $discoveryService->query(
                $repositoryId,
                $statement,
                $searchAllVersions,
                $includeRelationships,
                $renditionFilter,
                $includeAllowableActions,
                $maxItems,
                $skipCount
            )
        );
    }

    /**
     * Data provider for query
     *
     * @return array
     */
    public function queryDataProvider()
    {
        return [
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST . '?cmisaction=query&statement=SELECT%20*%20FROM%20cmis:document'
                    . '&searchAllVersions=true&includeRelationships=none&renditionFilter=foo:bar'
                    . '&includeAllowableActions=true&maxItems=99&skipCount=0&dateTimeFormat=simple'
                ),
                'repositoryId',
                'SELECT * FROM cmis:document',
                true,
                IncludeRelationships::cast(IncludeRelationships::NONE),
                'foo:bar',
                true,
                99,
                0
            ],
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST . '?cmisaction=query&statement=SELECT%20*%20FROM%20cmis:document'
                    . '&searchAllVersions=false&includeRelationships=both&renditionFilter=foo:bar'
                    . '&includeAllowableActions=false&skipCount=99&dateTimeFormat=simple'
                ),
                'repositoryId',
                'SELECT * FROM cmis:document',
                false,
                IncludeRelationships::cast(IncludeRelationships::BOTH),
                'foo:bar',
                false,
                null,
                99
            ],
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST . '?cmisaction=query&statement=SELECT%20*%20FROM%20cmis:document'
                    . '&searchAllVersions=false&renditionFilter=foo:bar'
                    . '&includeAllowableActions=false&skipCount=99&dateTimeFormat=simple'
                ),
                'repositoryId',
                'SELECT * FROM cmis:document',
                false,
                null,
                'foo:bar',
                false,
                0,
                99
            ]
        ];
    }

    /**
     * @dataProvider getContentChangesDataProvider
     * @param string $expectedUrl
     * @param string $repositoryId
     * @param string|null $changeLogToken
     * @param boolean $includeProperties
     * @param boolean $includePolicyIds
     * @param boolean $includeAcl
     * @param integer|null $maxItems
     */
    public function testGetContentChangesCallsReadFunctionWithParameterizedQuery(
        $expectedUrl,
        $repositoryId,
        $changeLogToken = null,
        $includeProperties = false,
        $includePolicyIds = false,
        $includeAcl = false,
        $maxItems = null
    ) {
        $responseData = ['foo' => 'bar'];
        $responseMock = $this->getMockBuilder('\\GuzzleHttp\\Message\\Response')->disableOriginalConstructor(
        )->setMethods(['getBody'])->getMock();
        $responseMock->expects($this->once())->method('getBody')->willReturn(json_encode($responseData));

        $dummyObjectData = new ObjectData();
        $jsonConverterMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Converter\\JsonConverter')->setMethods(
            ['convertObjectList']
        )->getMock();
        $jsonConverterMock->expects($this->once())->method('convertObjectList')->with($responseData)->willReturn(
            $dummyObjectData
        );

        $cmisBindingsHelperMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBindingsHelper')->setMethods(
            ['getJsonConverter']
        )->getMock();
        $cmisBindingsHelperMock->expects($this->once())->method('getJsonConverter')->willReturn($jsonConverterMock);

        /** @var DiscoveryService|PHPUnit_Framework_MockObject_MockObject $discoveryService */
        $discoveryService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            [$this->getSessionMock(), $cmisBindingsHelperMock]
        )->setMethods(
            ['getRepositoryUrl', 'read']
        )->getMock();

        $discoveryService->expects($this->any())->method('getRepositoryUrl')->with(
            $repositoryId
        )->willReturn(Url::createFromUrl(self::BROWSER_URL_TEST));
        $discoveryService->expects($this->once())->method('read')->with($expectedUrl)->willReturn($responseMock);

        $discoveryService->getContentChanges(
            $repositoryId,
            $changeLogToken,
            $includeProperties,
            $includePolicyIds,
            $includeAcl,
            $maxItems
        );
    }

    /**
     * @dataProvider getContentChangesDataProvider
     * @param string $expectedUrl
     * @param string $repositoryId
     * @param string|null $changeLogToken
     * @param boolean $includeProperties
     * @param boolean $includePolicyIds
     * @param boolean $includeAcl
     * @param integer|null $maxItems
     */
    public function testGetContentChangesReturnObjectList(
        $expectedUrl,
        $repositoryId,
        $changeLogToken = null,
        $includeProperties = false,
        $includePolicyIds = false,
        $includeAcl = false,
        $maxItems = null
    ) {
        $responseData = $this->getResponseFixtureContentAsArray(
            'Cmis/v1.1/BrowserBinding/getContentChanges-response.log'
        );
        $responseMock = $this->getMockBuilder('\\GuzzleHttp\\Message\\Response')->disableOriginalConstructor(
        )->setMethods(['getBody'])->getMock();
        $responseMock->expects($this->once())->method('getBody')->willReturn(json_encode($responseData));

        $dummyObjectData = new ObjectData();

        $jsonConverterMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Converter\\JsonConverter')->setMethods(
            ['convertObject']
        )->getMock();

        $expectedNumberOfItems = 39;
        $jsonConverterMock->expects($this->exactly($expectedNumberOfItems))->method('convertObject')->willReturn(
            $dummyObjectData
        );

        $cmisBindingsHelperMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBindingsHelper')->setMethods(
            ['getJsonConverter']
        )->getMock();
        $cmisBindingsHelperMock->expects($this->once())->method('getJsonConverter')->willReturn($jsonConverterMock);

        /** @var DiscoveryService|PHPUnit_Framework_MockObject_MockObject $discoveryService */
        $discoveryService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            [$this->getSessionMock(), $cmisBindingsHelperMock]
        )->setMethods(
            ['getRepositoryUrl', 'read']
        )->getMock();

        $discoveryService->expects($this->once())->method('getRepositoryUrl')->with(
            $repositoryId
        )->willReturn(Url::createFromUrl(self::BROWSER_URL_TEST));
        $discoveryService->expects($this->once())->method('read')->with($expectedUrl)->willReturn($responseMock);

        $expectedObjectList = new ObjectList();
        $expectedObjectList->setObjects(array_fill(0, $expectedNumberOfItems, new ObjectData()));
        $expectedObjectList->setNumItems($expectedNumberOfItems);
        $expectedObjectList->hasMoreItems(false);

        $this->assertEquals(
            $expectedObjectList,
            $discoveryService->getContentChanges(
                $repositoryId,
                $changeLogToken,
                $includeProperties,
                $includePolicyIds,
                $includeAcl,
                $maxItems
            )
        );
    }

    /**
     * Data provider for getContentChanges
     *
     * @return array
     */
    public function getContentChangesDataProvider()
    {
        return [
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST . '?changeLogToken=changeLogToken&includeProperties=false'
                    . '&includePolicyIds=false&includeACL=false&maxItems=99&succinct=false'
                ),
                'repositoryId',
                'changeLogToken',
                false,
                false,
                false,
                99
            ],
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST . '?includeProperties=true'
                    . '&includePolicyIds=true&includeACL=true&succinct=false'
                ),
                'repositoryId',
                null,
                true,
                true,
                true,
                null
            ]
        ];
    }
}
