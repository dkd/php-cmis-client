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
use Dkd\PhpCmis\Bindings\CmisBindingsHelper;
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
        $responseData = array('foo' => 'bar');
        $responseMock = $this->getMockBuilder('\\GuzzleHttp\\Message\\Response')->disableOriginalConstructor(
        )->setMethods(array('json'))->getMock();
        $responseMock->expects($this->any())->method('json')->willReturn($responseData);

        $dummyObjectData = new ObjectData();
        $jsonConverterMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Converter\\JsonConverter')->setMethods(
            array('convertObject')
        )->getMock();
        $jsonConverterMock->expects($this->once())->method('convertObject')->with($responseData)->willReturn(
            $dummyObjectData
        );

        $cmisBindingsHelperMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBindingsHelper')->setMethods(
            array('getJsonConverter')
        )->getMock();
        $cmisBindingsHelperMock->expects($this->any())->method('getJsonConverter')->willReturn($jsonConverterMock);

        /** @var ObjectService|PHPUnit_Framework_MockObject_MockObject $objectService */
        $objectService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            array($this->getSessionMock(), $cmisBindingsHelperMock)
        )->setMethods(
            array('getObjectUrl', 'read')
        )->getMock();

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
        $responseData = array('foo' => 'bar');
        $responseMock = $this->getMockBuilder('\\GuzzleHttp\\Message\\Response')->disableOriginalConstructor(
        )->setMethods(array('json'))->getMock();
        $responseMock->expects($this->any())->method('json')->willReturn($responseData);

        $dummyObjectData = new ObjectData();
        $jsonConverterMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Converter\\JsonConverter')->setMethods(
            array('convertObject')
        )->getMock();
        $dummyObjectData = $this->getMockBuilder('\\Dkd\\PhpCmis\\ObjectData\\ObjectData')->setMethods(
            array('getId')
        )->getMock();
        $dummyObjectData->expects($this->any())->method('getId')->willReturn('foo-id');
        $jsonConverterMock->expects($this->once())->method('convertObject')->with($responseData)->willReturn(
            $dummyObjectData
        );

        $cmisBindingsHelperMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBindingsHelper')->setMethods(
            array('getJsonConverter')
        )->getMock();
        $cmisBindingsHelperMock->expects($this->any())->method('getJsonConverter')->willReturn($jsonConverterMock);

        /** @var ObjectService|PHPUnit_Framework_MockObject_MockObject $objectService */
        $objectService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            array($this->getSessionMock(), $cmisBindingsHelperMock)
        )->setMethods(
            array('getObjectUrl', 'post')
        )->getMock();

        $objectService->expects($this->any())->method('getObjectUrl')->with(
            $repositoryId,
            $folderId
        )->willReturn(Url::createFromUrl(self::BROWSER_URL_TEST));
        $objectService->expects($this->any())->method('post')->with(
            $expectedUrl,
            array('content' => $contentStream)
        )->willReturn($responseMock);

        $this->assertSame(
            $dummyObjectData->getId(),
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

        $principal1 = new Principal('principalId1');
        $ace1 = new AccessControlEntry($principal1, array('permissionValue1', 'permissionValue2'));

        $principal2 = new Principal('principalId2');
        $ace2 = new AccessControlEntry($principal2, array('permissionValue3', 'permissionValue4'));

        $addAcl = new AccessControlList(array($ace1, $ace2));

        $principal3 = new Principal('principalId3');
        $ace3 = new AccessControlEntry($principal3, array('permissionValue5', 'permissionValue6'));

        $principal4 = new Principal('principalId4');
        $ace4 = new AccessControlEntry($principal4, array('permissionValue7', 'permissionValue8'));

        $removeAcl = new AccessControlList(array($ace3, $ace4));

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
                    . '&policy[0]=policyOne&policy[1]=policyTwo'
                    . '&addACEPrincipal[0]=principalId1'
                    . '&addACEPermission[0][0]=permissionValue1&addACEPermission[0][1]=permissionValue2'
                    . '&addACEPrincipal[1]=principalId2'
                    . '&addACEPermission[1][0]=permissionValue3&addACEPermission[1][1]=permissionValue4'
                    . '&removeACEPrincipal[0]=principalId3'
                    . '&removeACEPermission[0][0]=permissionValue5&removeACEPermission[0][1]=permissionValue6'
                    . '&removeACEPrincipal[1]=principalId4'
                    . '&removeACEPermission[1][0]=permissionValue7&removeACEPermission[1][1]=permissionValue8'
                ),
                'repositoryId',
                $properties,
                'folderId',
                $stream,
                VersioningState::cast(VersioningState::MAJOR),
                array('policyOne', 'policyTwo'),
                $addAcl,
                $removeAcl
            )
        );
    }
}
