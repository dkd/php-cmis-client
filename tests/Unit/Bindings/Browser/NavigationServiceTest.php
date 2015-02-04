<?php
namespace Dkd\PhpCmis\Test\Unit\Bindings\Browser;

/**
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
use Dkd\PhpCmis\Data\ObjectInFolderListInterface;
use Dkd\PhpCmis\DataObjects\ObjectData;
use Dkd\PhpCmis\Enum\IncludeRelationships;
use League\Url\Url;
use PHPUnit_Framework_MockObject_MockObject;

class NavigationServiceTest extends AbstractBrowserBindingServiceTestCase
{
    const CLASS_TO_TEST = '\\Dkd\\PhpCmis\\Bindings\\Browser\\NavigationService';

    /**
     * @dataProvider getChildrenDataProvider
     * @param $expectedUrl
     * @param string $repositoryId
     * @param string $folderId
     * @param string $filter
     * @param string $orderBy
     * @param boolean $includeAllowableActions
     * @param IncludeRelationships $includeRelationships
     * @param string $renditionFilter
     * @param boolean $includePathSegment
     * @param integer $maxItems
     * @param integer $skipCount
     * @param ExtensionDataInterface $extension
     * @return ObjectInFolderListInterface
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
        $skipCount = 0,
        ExtensionDataInterface $extension = null
    ) {
        $responseData = array('foo' => 'bar');
        $responseMock = $this->getMockBuilder('\\GuzzleHttp\\Message\\Response')->disableOriginalConstructor(
        )->setMethods(array('json'))->getMock();
        $responseMock->expects($this->any())->method('json')->willReturn($responseData);

        $dummyObjectData = new ObjectData();
        $jsonConverterMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Converter\\JsonConverter')->setMethods(
            array('convertObjectInFolderList')
        )->getMock();

        $jsonConverterMock->expects($this->once())->method(
            'convertObjectInFolderList'
        )->with($responseData)->willReturn(
            $dummyObjectData
        );

        $cmisBindingsHelperMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBindingsHelper')->setMethods(
            array('getJsonConverter')
        )->getMock();
        $cmisBindingsHelperMock->expects($this->any())->method('getJsonConverter')->willReturn($jsonConverterMock);

        /** @var NavigationService|PHPUnit_Framework_MockObject_MockObject $navigationService */
        $navigationService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            array($this->getSessionMock(), $cmisBindingsHelperMock)
        )->setMethods(
            array('getObjectUrl', 'read')
        )->getMock();

        $navigationService->expects($this->once())->method('getObjectUrl')->with(
            $repositoryId,
            $folderId,
            Constants::SELECTOR_CHILDREN
        )->willReturn(Url::createFromUrl(self::BROWSER_URL_TEST));
        $navigationService->expects($this->once())->method('read')->with($expectedUrl)->willReturn($responseMock);

        $this->assertSame(
            $dummyObjectData,
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
                $skipCount,
                $extension
            )
        );
    }

    public function getChildrenDataProvider()
    {
        return array(
            array(
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
                0,
                null
            ),
            array(
                Url::createFromUrl(
                    self::BROWSER_URL_TEST . '?filter=filter,123&orderBy=cmis:objectId&includeAllowableActions=false'
                    . '&includeRelationships=both&renditionFilter=cmis:none&includePathSegment=false&maxItems=99'
                    . '&skipCount=20&succinct=false&dateTimeFormat=simple'
                ),
                'repositoryId',
                'folderId',
                'filter,123',
                'cmis:objectId',
                false,
                IncludeRelationships::cast(IncludeRelationships::BOTH),
                'cmis:none',
                false,
                99,
                20,
                null
            )
        );
    }
}
