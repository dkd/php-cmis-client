<?php
namespace Dkd\PhpCmis\Test\Unit\Bindings\Browser;

/*
 * This file is part of php-cmis-client
 *
 * (c) Dimitri Ebert <dimitri.ebert@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Bindings\Browser\NavigationService;
use Dkd\PhpCmis\Constants;
use Dkd\PhpCmis\Data\ExtensionDataInterface;
use Dkd\PhpCmis\DataObjects\ObjectData;
use Dkd\PhpCmis\Enum\IncludeRelationships;
use League\Url\Url;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Class NavigationServiceTest
 */
class NavigationServiceTest extends AbstractBrowserBindingServiceTestCase
{
    const CLASS_TO_TEST = NavigationService::class;

    /**
     * @dataProvider getChildrenDataProvider
     * @param string $expectedUrl
     * @param string $repositoryId
     * @param string $folderId
     * @param string|null $filter
     * @param string|null $orderBy
     * @param boolean $includeAllowableActions
     * @param IncludeRelationships|null $includeRelationships
     * @param string $renditionFilter
     * @param boolean $includePathSegment
     * @param integer|null $maxItems
     * @param integer $skipCount
     */
    public function testGetChildrenCallsReadFunctionWithParameterizedQuery(
        $expectedUrl,
        $repositoryId,
        $folderId,
        $filter = null,
        $orderBy = null,
        $includeAllowableActions = false,
        IncludeRelationships $includeRelationships = null,
        $renditionFilter = Constants::RENDITION_NONE,
        $includePathSegment = false,
        $maxItems = null,
        $skipCount = 0
    ) {
        $navigationService = $this->getNavigationServiceMockForParameterizedQueryTest(
            $expectedUrl,
            'convertObjectInFolderList',
            [$repositoryId, $folderId, Constants::SELECTOR_CHILDREN]
        );

        $navigationService->getChildren(
            $repositoryId,
            $folderId,
            $filter,
            $orderBy,
            $includeAllowableActions,
            $includeRelationships,
            $renditionFilter,
            $includePathSegment,
            $maxItems,
            $skipCount
        );
    }

    /**
     * Data provider for getChildren
     *
     * @return array
     */
    public function getChildrenDataProvider()
    {
        return [
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST . '?filter=filter,123&orderBy=cmis:objectId&includeAllowableActions=true'
                    . '&includeRelationships=none&renditionFilter=cmis:none&includePathSegment=true&maxItems=99'
                    . '&skipCount=0&succinct=false&dateTimeFormat=simple'
                ),
                'repositoryId',
                'folderId',
                'filter,123',
                'cmis:objectId',
                true,
                IncludeRelationships::cast(IncludeRelationships::NONE),
                'cmis:none',
                true,
                99,
                0
            ],
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST . '?orderBy=cmis:objectId&includeAllowableActions=false'
                    . '&renditionFilter=cmis:none&includePathSegment=false'
                    . '&skipCount=20&succinct=false&dateTimeFormat=simple'
                ),
                'repositoryId',
                'folderId',
                null,
                'cmis:objectId',
                false,
                null,
                'cmis:none',
                false,
                null,
                20
            ],
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST . '?filter=filter,123&includeAllowableActions=false'
                    . '&includeRelationships=both&renditionFilter=cmis:none&includePathSegment=false'
                    . '&skipCount=20&succinct=false&dateTimeFormat=simple'
                ),
                'repositoryId',
                'folderId',
                'filter,123',
                null,
                false,
                IncludeRelationships::cast(IncludeRelationships::BOTH),
                'cmis:none',
                false,
                null,
                20
            ]
        ];
    }

    /**
     * @dataProvider getCheckedOutDocsDataProvider
     * @param string $expectedUrl
     * @param string $repositoryId
     * @param string $folderId
     * @param string|null $filter
     * @param string|null $orderBy
     * @param boolean $includeAllowableActions
     * @param IncludeRelationships|null $includeRelationships
     * @param string $renditionFilter
     * @param integer|null $maxItems
     * @param integer $skipCount
     */
    public function testGetCheckedOutDocsCallsReadFunctionWithParameterizedQuery(
        $expectedUrl,
        $repositoryId,
        $folderId,
        $filter = null,
        $orderBy = null,
        $includeAllowableActions = false,
        IncludeRelationships $includeRelationships = null,
        $renditionFilter = Constants::RENDITION_NONE,
        $maxItems = null,
        $skipCount = 0
    ) {
        $navigationService = $this->getNavigationServiceMockForParameterizedQueryTest(
            $expectedUrl,
            'convertObjectList',
            [$repositoryId, $folderId, Constants::SELECTOR_CHECKEDOUT]
        );

        $navigationService->getCheckedOutDocs(
            $repositoryId,
            $folderId,
            $filter,
            $orderBy,
            $includeAllowableActions,
            $includeRelationships,
            $renditionFilter,
            $maxItems,
            $skipCount
        );
    }

    /**
     * Data provider for getCheckedOutDocs
     *
     * @return array
     */
    public function getCheckedOutDocsDataProvider()
    {
        return [
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST . '?filter=filter,123&orderBy=cmis:objectId&includeAllowableActions=true'
                    . '&includeRelationships=none&renditionFilter=cmis:none&maxItems=99'
                    . '&skipCount=0&succinct=false&dateTimeFormat=simple'
                ),
                'repositoryId',
                'folderId',
                'filter,123',
                'cmis:objectId',
                true,
                IncludeRelationships::cast(IncludeRelationships::NONE),
                'cmis:none',
                99,
                0
            ],
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST . '?includeAllowableActions=true&renditionFilter=cmis:none'
                    . '&skipCount=0&succinct=false&dateTimeFormat=simple'
                ),
                'repositoryId',
                'folderId',
                null,
                null,
                true,
                null,
                'cmis:none',
                null,
                0
            ]
        ];
    }

    /**
     * @dataProvider getDescendantsDataProvider
     * @param string $expectedUrl
     * @param string $repositoryId
     * @param string $folderId
     * @param integer $depth
     * @param string|null $filter
     * @param boolean $includeAllowableActions
     * @param IncludeRelationships|null $includeRelationships
     * @param string $renditionFilter
     * @param boolean $includePathSegment
     */
    public function testGetDescendantsCallsReadFunctionWithParameterizedQuery(
        $expectedUrl,
        $repositoryId,
        $folderId,
        $depth,
        $filter = null,
        $includeAllowableActions = false,
        IncludeRelationships $includeRelationships = null,
        $renditionFilter = Constants::RENDITION_NONE,
        $includePathSegment = false
    ) {
        $navigationService = $this->getNavigationServiceMockForParameterizedQueryTest(
            $expectedUrl,
            'convertDescendants',
            [$repositoryId, $folderId, Constants::SELECTOR_DESCENDANTS]
        );

        $navigationService->getDescendants(
            $repositoryId,
            $folderId,
            $depth,
            $filter,
            $includeAllowableActions,
            $includeRelationships,
            $renditionFilter,
            $includePathSegment
        );
    }

    /**
     * Data provider for getDescendants
     *
     * @return array
     */
    public function getDescendantsDataProvider()
    {
        return [
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST . '?depth=5&filter=filter,123&includeAllowableActions=true'
                    . '&includeRelationships=none&renditionFilter=cmis:none&includePathSegment=true'
                    .'&succinct=false&dateTimeFormat=simple'
                ),
                'repositoryId',
                'folderId',
                '5',
                'filter,123',
                true,
                IncludeRelationships::cast(IncludeRelationships::NONE),
                'cmis:none',
                true
            ],
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST . '?depth=5&filter=filter,123&includeAllowableActions=true'
                    . '&includeRelationships=none&renditionFilter=cmis:none&includePathSegment=true'
                    .'&succinct=false&dateTimeFormat=simple'
                ),
                'repositoryId',
                'folderId',
                '5',
                'filter,123',
                true,
                IncludeRelationships::cast(IncludeRelationships::NONE),
                'cmis:none',
                true
            ],
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST . '?depth=5&includeAllowableActions=true'
                    . '&includeRelationships=none&renditionFilter=cmis:none&includePathSegment=true'
                    .'&succinct=false&dateTimeFormat=simple'
                ),
                'repositoryId',
                'folderId',
                '5',
                null,
                true,
                IncludeRelationships::cast(IncludeRelationships::NONE),
                'cmis:none',
                true
            ]
        ];
    }

    /**
     * @dataProvider getFolderParentDataProvider
     * @param string $expectedUrl
     * @param string $repositoryId
     * @param string $folderId
     * @param string|null $filter
     */
    public function testGetFolderParentCallsReadFunctionWithParameterizedQuery(
        $expectedUrl,
        $repositoryId,
        $folderId,
        $filter = null
    ) {
        $navigationService = $this->getNavigationServiceMockForParameterizedQueryTest(
            $expectedUrl,
            'convertObject',
            [$repositoryId, $folderId, Constants::SELECTOR_PARENT]
        );

        $navigationService->getFolderParent(
            $repositoryId,
            $folderId,
            $filter
        );
    }

    /**
     * Data provider for getFolderParent
     *
     * @return array
     */
    public function getFolderParentDataProvider()
    {
        return [
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST . '?filter=filter,123&succinct=false&dateTimeFormat=simple'
                ),
                'repositoryId',
                'folderId',
                'filter,123'
            ],
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST . '?succinct=false&dateTimeFormat=simple'
                ),
                'repositoryId',
                'folderId'
            ]
        ];
    }

    /**
     * @dataProvider getFolderTreeDataProvider
     * @param string $expectedUrl
     * @param string $repositoryId
     * @param string $folderId
     * @param integer $depth
     * @param string|null $filter
     * @param boolean $includeAllowableActions
     * @param IncludeRelationships|null $includeRelationships
     * @param string $renditionFilter
     * @param boolean $includePathSegment
     */
    public function testGetFolderTreeCallsReadFunctionWithParameterizedQuery(
        $expectedUrl,
        $repositoryId,
        $folderId,
        $depth,
        $filter = null,
        $includeAllowableActions = false,
        IncludeRelationships $includeRelationships = null,
        $renditionFilter = Constants::RENDITION_NONE,
        $includePathSegment = false
    ) {
        $navigationService = $this->getNavigationServiceMockForParameterizedQueryTest(
            $expectedUrl,
            'convertDescendants',
            [$repositoryId, $folderId, Constants::SELECTOR_FOLDER_TREE]
        );

        $navigationService->getFolderTree(
            $repositoryId,
            $folderId,
            $depth,
            $filter,
            $includeAllowableActions,
            $includeRelationships,
            $renditionFilter,
            $includePathSegment
        );
    }

    /**
     * Data provider for getFolderTree
     *
     * @return array
     */
    public function getFolderTreeDataProvider()
    {
        return [
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST . '?depth=5&filter=filter,123&includeAllowableActions=true'
                    . '&includeRelationships=none&renditionFilter=cmis:none&includePathSegment=true'
                    . '&succinct=false&dateTimeFormat=simple'
                ),
                'repositoryId',
                'folderId',
                '5',
                'filter,123',
                true,
                IncludeRelationships::cast(IncludeRelationships::NONE),
                'cmis:none',
                true
            ],
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST . '?depth=5&includeAllowableActions=true'
                    . '&renditionFilter=cmis:none&includePathSegment=false'
                    . '&succinct=false&dateTimeFormat=simple'
                ),
                'repositoryId',
                'folderId',
                '5',
                null,
                true,
                null,
                'cmis:none',
                false
            ]
        ];
    }

    /**
     * @dataProvider getObjectParentsDataProvider
     * @param string $expectedUrl
     * @param string $repositoryId
     * @param string $objectId
     * @param string|null $filter
     * @param boolean $includeAllowableActions
     * @param IncludeRelationships|null $includeRelationships
     * @param string $renditionFilter
     * @param boolean $includeRelativePathSegment
     */
    public function testGetObjectParentsCallsReadFunctionWithParameterizedQuery(
        $expectedUrl,
        $repositoryId,
        $objectId,
        $filter = null,
        $includeAllowableActions = false,
        IncludeRelationships $includeRelationships = null,
        $renditionFilter = Constants::RENDITION_NONE,
        $includeRelativePathSegment = false
    ) {
        $navigationService = $this->getNavigationServiceMockForParameterizedQueryTest(
            $expectedUrl,
            'convertObjectParents',
            [$repositoryId, $objectId, Constants::SELECTOR_PARENTS]
        );

        $navigationService->getObjectParents(
            $repositoryId,
            $objectId,
            $filter,
            $includeAllowableActions,
            $includeRelationships,
            $renditionFilter,
            $includeRelativePathSegment
        );
    }

    /**
     * Data provider for getObjectParents
     *
     * @return array
     */
    public function getObjectParentsDataProvider()
    {
        return [
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST . '?filter=filter,123&includeAllowableActions=true'
                    . '&includeRelationships=none&renditionFilter=cmis:none&includeRelativePathSegment=true'
                    . '&succinct=false&dateTimeFormat=simple'
                ),
                'repositoryId',
                'objectId',
                'filter,123',
                true,
                IncludeRelationships::cast(IncludeRelationships::NONE),
                'cmis:none',
                true
            ],
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST . '?includeAllowableActions=true'
                    . '&renditionFilter=cmis:none&includeRelativePathSegment=false'
                    . '&succinct=false&dateTimeFormat=simple'
                ),
                'repositoryId',
                'objectId',
                null,
                true,
                null,
                'cmis:none',
                false
            ]
        ];
    }
    /**
     * Get a navigation service mock that expects that the $expectedUrl is called once
     *
     * @param string $expectedUrl
     * @param string $convertFunctionName
     * @param array $getObjectUrlParams
     * @return NavigationService|PHPUnit_Framework_MockObject_MockObject
     */
    protected function getNavigationServiceMockForParameterizedQueryTest(
        $expectedUrl,
        $convertFunctionName,
        $getObjectUrlParams
    ) {
        $responseData = ['foo' => 'bar'];
        $responseMock = $this->getMockBuilder('\\GuzzleHttp\\Message\\Response')->disableOriginalConstructor(
        )->setMethods(['getBody'])->getMock();
        $responseMock->expects($this->any())->method('getBody')->willReturn(json_encode($responseData));

        $jsonConverterMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Converter\\JsonConverter')->setMethods(
            [$convertFunctionName]
        )->getMock();

        $dummyObjectData = new ObjectData();
        $jsonConverterMock->expects($this->once())->method($convertFunctionName)->with($responseData)->willReturn(
            $dummyObjectData
        );

        $cmisBindingsHelperMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBindingsHelper')->setMethods(
            ['getJsonConverter']
        )->getMock();
        $cmisBindingsHelperMock->expects($this->any())->method('getJsonConverter')->willReturn($jsonConverterMock);

        /** @var NavigationService|PHPUnit_Framework_MockObject_MockObject $navigationService */
        $navigationService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            [$this->getSessionMock(), $cmisBindingsHelperMock]
        )->setMethods(
            ['getObjectUrl', 'read']
        )->getMock();

        list($repositoryId, $objectId, $selector) = $getObjectUrlParams;
        $navigationService->expects($this->once())->method('getObjectUrl')->with(
            $repositoryId,
            $objectId,
            $selector
        )->willReturn(Url::createFromUrl(self::BROWSER_URL_TEST));
        $navigationService->expects($this->once())->method('read')->with($expectedUrl)->willReturn($responseMock);

        return $navigationService;
    }
}
