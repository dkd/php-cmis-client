<?php
namespace Dkd\PhpCmis\Test\Unit\Bindings\Browser;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Bindings\Browser\ObjectService;
use Dkd\PhpCmis\Constants;
use Dkd\PhpCmis\Data\AclInterface;
use Dkd\PhpCmis\Data\ExtensionDataInterface;
use Dkd\PhpCmis\Data\PropertiesInterface;
use Dkd\PhpCmis\DataObjects\AccessControlEntry;
use Dkd\PhpCmis\DataObjects\AccessControlList;
use Dkd\PhpCmis\DataObjects\ObjectData;
use Dkd\PhpCmis\DataObjects\Principal;
use Dkd\PhpCmis\DataObjects\Properties;
use Dkd\PhpCmis\DataObjects\PropertyString;
use Dkd\PhpCmis\Enum\IncludeRelationships;
use Dkd\PhpCmis\Enum\VersioningState;
use GuzzleHttp\Stream\StreamInterface;
use League\Url\Url;
use PHPUnit_Framework_MockObject_MockObject;

class ObjectServiceTest extends AbstractBrowserBindingServiceTestCase
{
    const CLASS_TO_TEST = '\\Dkd\\PhpCmis\\Bindings\\Browser\\ObjectService';

    /**
     * @dataProvider getObjectDataProvider
     * @param $expectedUrl
     * @param $repositoryId
     * @param $objectId
     * @param null $filter
     * @param bool $includeAllowableActions
     * @param IncludeRelationships $includeRelationships
     * @param null $renditionFilter
     * @param bool $includePolicyIds
     * @param bool $includeAcl
     * @param ExtensionDataInterface $extension
     */
    public function testGetObjectCallsReadFunctionWithParameterizedQuery(
        $expectedUrl,
        $repositoryId,
        $objectId,
        $filter = null,
        $includeAllowableActions = false,
        IncludeRelationships $includeRelationships = null,
        $renditionFilter = null,
        $includePolicyIds = false,
        $includeAcl = false,
        ExtensionDataInterface $extension = null
    ) {
        /** @var ObjectService|PHPUnit_Framework_MockObject_MockObject $objectService */
        $objectService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            array($this->getSessionMock())
        )->setMethods(
            array('getObjectUrl', 'read', 'getJsonConverter')
        )->getMock();

        $responseData = array('foo' => 'bar');
        $responseMock = $this->getMockBuilder('\\GuzzleHttp\\Message\\Response')->disableOriginalConstructor(
        )->setMethods(array('json'))->getMock();
        $responseMock->expects($this->any())->method('json')->willReturn($responseData);

        $jsonConverterMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Converter\\JsonConverter')->setMethods(
            array('convertObject')
        )->getMock();
        $dummyObjectData = new ObjectData();
        $jsonConverterMock->expects($this->once())->method('convertObject')->with($responseData)->willReturn(
            $dummyObjectData
        );
        $objectService->expects($this->any())->method('getJsonConverter')->willReturn($jsonConverterMock);
        $objectService->expects($this->any())->method('getObjectUrl')->with(
            $repositoryId,
            $objectId,
            Constants::SELECTOR_OBJECT
        )->willReturn(Url::createFromUrl(self::BROWSER_URL_TEST));
        $objectService->expects($this->any())->method('read')->with($expectedUrl)->willReturn($responseMock);

        $this->assertSame(
            $dummyObjectData,
            $objectService->getObject(
                $repositoryId,
                $objectId,
                $filter,
                $includeAllowableActions,
                $includeRelationships,
                $renditionFilter,
                $includePolicyIds,
                $includeAcl
            )
        );
    }

    public function getObjectDataProvider()
    {
        return array(
            array(
                Url::createFromUrl(
                    self::BROWSER_URL_TEST . '?filter=filter,123&includeAllowableActions=true'
                    . '&includeRelationships=none&renditionFilter=foo:bar&includePolicyIds=true&includeACL=true'
                    . '&succinct=false'
                ),
                'repositoryId',
                'objectId',
                'filter,123',
                true,
                IncludeRelationships::cast(IncludeRelationships::NONE),
                'foo:bar',
                true,
                true,
            ),
            array(
                Url::createFromUrl(
                    self::BROWSER_URL_TEST . '?filter=filter,123&includeAllowableActions=false'
                    . '&includeRelationships=both&renditionFilter=foo:bar&includePolicyIds=false&includeACL=false'
                    . '&succinct=false'
                ),
                'repositoryId',
                'objectId',
                'filter,123',
                false,
                IncludeRelationships::cast(IncludeRelationships::BOTH),
                'foo:bar',
                false,
                false,
            )
        );
    }


    /**
     * @dataProvider createDocumentDataProvider
     * @param $expectedUrl
     * @param $repositoryId
     * @param PropertiesInterface $properties
     * @param null $folderId
     * @param StreamInterface $contentStream
     * @param VersioningState $versioningState
     * @param array $policies
     * @param AclInterface $addAces
     * @param AclInterface $removeAces
     * @param ExtensionDataInterface $extension
     */
    public function testCreateDocumentCallsPostFunctionWithParameterizedQuery(
        $expectedUrl,
        $repositoryId,
        PropertiesInterface $properties,
        $folderId = null,
        StreamInterface $contentStream = null,
        VersioningState $versioningState = null,
        array $policies = array(),
        AclInterface $addAces = null,
        AclInterface $removeAces = null,
        ExtensionDataInterface $extension = null
    ) {
        /** @var ObjectService|PHPUnit_Framework_MockObject_MockObject $objectService */
        $objectService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            array($this->getSessionMock())
        )->setMethods(
            array('getObjectUrl', 'post', 'getJsonConverter')
        )->getMock();

        $responseData = array('foo' => 'bar');
        $responseMock = $this->getMockBuilder('\\GuzzleHttp\\Message\\Response')->disableOriginalConstructor(
        )->setMethods(array('json'))->getMock();
        $responseMock->expects($this->any())->method('json')->willReturn($responseData);

        $jsonConverterMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Converter\\JsonConverter')->setMethods(
            array('convertObject')
        )->getMock();
        $dummyObjectData = new ObjectData();
        $jsonConverterMock->expects($this->once())->method('convertObject')->with($responseData)->willReturn(
            $dummyObjectData
        );
        $objectService->expects($this->any())->method('getJsonConverter')->willReturn($jsonConverterMock);
        $objectService->expects($this->any())->method('getObjectUrl')->with(
            $repositoryId,
            $folderId
        )->willReturn(Url::createFromUrl(self::BROWSER_URL_TEST));
        $objectService->expects($this->any())->method('post')->with(
            $expectedUrl,
            array('content' => $contentStream)
        )->willReturn($responseMock);

        $this->assertSame(
            $dummyObjectData,
            $objectService->createDocument(
                $repositoryId,
                $properties,
                $folderId,
                $contentStream,
                $versioningState,
                $policies,
                $addAces,
                $removeAces,
                $extension
            )
        );
    }

    public function createDocumentDataProvider()
    {
        $property = new PropertyString('cmis:name', 'name');
        $properties = new Properties();
        $properties->addProperty($property);
        $stream = $this->getMockForAbstractClass('\\GuzzleHttp\\Stream\\StreamInterface');

        $principal = new Principal();
        $principal->setId('dummyPrincipal');
        $ace = new AccessControlEntry($principal, array());
        $acl = new AccessControlList(array($ace));

        return array(
            array(
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                    . '?propertyId[0]=cmis:name&propertyValue[0]=name&cmisaction=createDocument&succinct=false'
                ),
                'repositoryId',
                $properties
            ),
            array(
                // TODO adjust URL when addAces, removeAces and policies are implemented in createDocument
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                    . '?propertyId[0]=cmis:name&propertyValue[0]=name&cmisaction=createDocument'
                    . '&versioningState=major&succinct=false'
                ),
                'repositoryId',
                $properties,
                'folderId',
                $stream,
                VersioningState::cast(VersioningState::MAJOR),
                array('policyOne', 'policyTwo'),
                $acl,
                $acl
            )
        );
    }
}
