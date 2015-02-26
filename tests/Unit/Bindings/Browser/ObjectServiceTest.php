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
     * @param string $expectedUrl
     * @param string $repositoryId
     * @param string $objectId
     * @param string|null $filter
     * @param boolean $includeAllowableActions
     * @param IncludeRelationships|null $includeRelationships
     * @param string $renditionFilter
     * @param boolean $includePolicyIds
     * @param boolean $includeAcl
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
        $includeAcl = false
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
                    . '&succinct=false&dateTimeFormat=simple'
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
                    self::BROWSER_URL_TEST . '?includeAllowableActions=false'
                    . '&includeRelationships=both&renditionFilter=foo:bar&includePolicyIds=false&includeACL=false'
                    . '&succinct=false&dateTimeFormat=simple'
                ),
                'repositoryId',
                'objectId',
                null,
                false,
                IncludeRelationships::cast(IncludeRelationships::BOTH),
                'foo:bar',
                false,
                false,
            ),
            array(
                Url::createFromUrl(
                    self::BROWSER_URL_TEST . '?filter=filter,123&includeAllowableActions=false'
                    . '&renditionFilter=foo:bar&includePolicyIds=false&includeACL=false'
                    . '&succinct=false&dateTimeFormat=simple'
                ),
                'repositoryId',
                'objectId',
                'filter,123',
                false,
                null,
                'foo:bar',
                false,
                false,
            )
        );
    }


    /**
     * @dataProvider createDocumentDataProvider
     * @param string $expectedUrl
     * @param string $repositoryId
     * @param PropertiesInterface $properties
     * @param string|null $folderId
     * @param StreamInterface|null $contentStream
     * @param VersioningState|null  $versioningState
     * @param string[] $policies
     * @param AclInterface|null $addAces
     * @param AclInterface|null $removeAces
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
        AclInterface $removeAces = null
    ) {
        $responseData = array('foo' => 'bar');
        $responseMock = $this->getMockBuilder('\\GuzzleHttp\\Message\\Response')->disableOriginalConstructor(
        )->setMethods(array('json'))->getMock();
        $responseMock->expects($this->any())->method('json')->willReturn($responseData);

        $jsonConverterMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Converter\\JsonConverter')->setMethods(
            array('convertObject')
        )->getMock();
        $dummyObjectData = $this->getMockBuilder('\\Dkd\\PhpCmis\\ObjectData\\ObjectData')->setMethods(
            array('getId')
        )->getMock();

        /** @var  ObjectData|PHPUnit_Framework_MockObject_MockObject $dummyObjectData */
        $dummyObjectData->expects($this->any())->method('getId')->willReturn('foo-id');
        $jsonConverterMock->expects($this->atLeastOnce())->method('convertObject')->with($responseData)->willReturn(
            $dummyObjectData
        );

        $cmisBindingsHelperMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBindingsHelper')->setMethods(
            array('getJsonConverter')
        )->getMock();
        $cmisBindingsHelperMock->expects($this->atLeastOnce())->method(
            'getJsonConverter'
        )->willReturn($jsonConverterMock);

        /** @var ObjectService|PHPUnit_Framework_MockObject_MockObject $objectService */
        $objectService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            array($this->getSessionMock(), $cmisBindingsHelperMock)
        )->setMethods(
            array('getObjectUrl', 'getRepositoryUrl', 'post')
        )->getMock();

        if ($folderId === null) {
            $objectService->expects($this->atLeastOnce())->method('getRepositoryUrl')->with(
                $repositoryId
            )->willReturn(Url::createFromUrl(self::BROWSER_URL_TEST));
        } else {
            $objectService->expects($this->atLeastOnce())->method('getObjectUrl')->with(
                $repositoryId,
                $folderId
            )->willReturn(Url::createFromUrl(self::BROWSER_URL_TEST));
        }

        $objectService->expects($this->atLeastOnce())->method('post')->with(
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
                $removeAces
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

    /**
     * @dataProvider createFolderDataProvider
     * @param string $expectedUrl
     * @param string $repositoryId
     * @param PropertiesInterface $properties
     * @param string $folderId
     * @param string[] $policies
     * @param AclInterface $addAces
     * @param AclInterface $removeAces
     */
    public function testCreateFolderCallsPostFunctionWithParameterizedQuery(
        $expectedUrl,
        $repositoryId,
        PropertiesInterface $properties,
        $folderId,
        array $policies = array(),
        AclInterface $addAces = null,
        AclInterface $removeAces = null
    ) {
        $responseData = array('foo' => 'bar');
        $responseMock = $this->getMockBuilder('\\GuzzleHttp\\Message\\Response')->disableOriginalConstructor(
        )->setMethods(array('json'))->getMock();
        $responseMock->expects($this->any())->method('json')->willReturn($responseData);

        $jsonConverterMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Converter\\JsonConverter')->setMethods(
            array('convertObject')
        )->getMock();

        /** @var  ObjectData|PHPUnit_Framework_MockObject_MockObject $dummyObjectData */
        $dummyObjectData = $this->getMockBuilder('\\Dkd\\PhpCmis\\ObjectData\\ObjectData')->setMethods(
            array('getId')
        )->getMock();
        $dummyObjectData->expects($this->any())->method('getId')->willReturn('foo-id');
        $jsonConverterMock->expects($this->atLeastOnce())->method('convertObject')->with($responseData)->willReturn(
            $dummyObjectData
        );

        $cmisBindingsHelperMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBindingsHelper')->setMethods(
            array('getJsonConverter')
        )->getMock();
        $cmisBindingsHelperMock->expects($this->atLeastOnce())->method(
            'getJsonConverter'
        )->willReturn($jsonConverterMock);

        /** @var ObjectService|PHPUnit_Framework_MockObject_MockObject $objectService */
        $objectService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            array($this->getSessionMock(), $cmisBindingsHelperMock)
        )->setMethods(
            array('getObjectUrl', 'post')
        )->getMock();

        $objectService->expects($this->atLeastOnce())->method('getObjectUrl')->with(
            $repositoryId,
            $folderId
        )->willReturn(Url::createFromUrl(self::BROWSER_URL_TEST));
        $objectService->expects($this->atLeastOnce())->method('post')->with(
            $expectedUrl
        )->willReturn($responseMock);

        $this->assertSame(
            $dummyObjectData->getId(),
            $objectService->createFolder(
                $repositoryId,
                $properties,
                $folderId,
                $policies,
                $addAces,
                $removeAces
            )
        );
    }

    /**
     * Data provider for createFolder
     *
     * @return array
     */
    public function createFolderDataProvider()
    {
        $property = new PropertyString('cmis:name', 'name');
        $properties = new Properties();
        $properties->addProperty($property);

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
                    . '?propertyId[0]=cmis:name&propertyValue[0]=name&cmisaction=createFolder&succinct=false'
                ),
                'repositoryId',
                $properties,
                'parentFolderId'
            ),
            array(
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                    . '?propertyId[0]=cmis:name&propertyValue[0]=name&cmisaction=createFolder'
                    . '&succinct=false'
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
                'parentFolderId',
                array('policyOne', 'policyTwo'),
                $addAcl,
                $removeAcl
            )
        );
    }

    /**
     * @dataProvider deleteObjectDataProvider
     * @param string $expectedUrl
     * @param string $repositoryId
     * @param string $objectId
     * @param boolean $allVersions
     */
    public function testDeleteObjectCallsPostFunctionWithParameterizedQuery(
        $expectedUrl,
        $repositoryId,
        $objectId,
        $allVersions = true
    ) {
        $responseMock = $this->getMockBuilder('\\GuzzleHttp\\Message\\Response')->disableOriginalConstructor(
        )->getMock();

        $cmisBindingsHelperMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBindingsHelper')->getMock();

        /** @var ObjectService|PHPUnit_Framework_MockObject_MockObject $objectService */
        $objectService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            array($this->getSessionMock(), $cmisBindingsHelperMock)
        )->setMethods(
            array('getObjectUrl', 'post')
        )->getMock();

        $objectService->expects($this->atLeastOnce())->method('getObjectUrl')->with(
            $repositoryId,
            $objectId
        )->willReturn(Url::createFromUrl(self::BROWSER_URL_TEST));
        $objectService->expects($this->atLeastOnce())->method('post')->with(
            $expectedUrl
        )->willReturn($responseMock);

        $objectService->deleteObject(
            $repositoryId,
            $objectId,
            $allVersions
        );
    }

    /**
     * Data provider for deleteObject
     *
     * @return array
     */
    public function deleteObjectDataProvider()
    {
        return array(
            array(
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                    . '?cmisaction=delete&allVersions=true'
                ),
                'repositoryId',
                'objectId',
                true
            ),
            array(
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                    . '?cmisaction=delete&allVersions=false'
                ),
                'repositoryId',
                'objectId',
                false
            )
        );
    }


    /**
     * @dataProvider moveObjectDataProvider
     * @param string $expectedUrl
     * @param string $repositoryId
     * @param string $objectId
     * @param string $targetFolderId
     * @param string $sourceFolderId
     * @param ExtensionDataInterface|null $extension
     */
    public function testMoveObjectCallsPostFunctionWithParameterizedQueryAndModifiesObjectId(
        $expectedUrl,
        $repositoryId,
        $objectId,
        $targetFolderId,
        $sourceFolderId,
        ExtensionDataInterface $extension = null
    ) {
        $responseData = array('foo' => 'bar');
        $responseMock = $this->getMockBuilder('\\GuzzleHttp\\Message\\Response')->disableOriginalConstructor(
        )->setMethods(array('json'))->getMock();
        $responseMock->expects($this->any())->method('json')->willReturn($responseData);

        $jsonConverterMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Converter\\JsonConverter')->setMethods(
            array('convertObject')
        )->getMock();

        /** @var  ObjectData|PHPUnit_Framework_MockObject_MockObject $dummyObjectData */
        $dummyObjectData = $this->getMockBuilder('\\Dkd\\PhpCmis\\ObjectData\\ObjectData')->setMethods(
            array('getId')
        )->getMock();
        $dummyObjectData->expects($this->any())->method('getId')->willReturn('foo-id');
        $jsonConverterMock->expects($this->atLeastOnce())->method('convertObject')->with($responseData)->willReturn(
            $dummyObjectData
        );

        $cmisBindingsHelperMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBindingsHelper')->setMethods(
            array('getJsonConverter')
        )->getMock();
        $cmisBindingsHelperMock->expects($this->atLeastOnce())->method(
            'getJsonConverter'
        )->willReturn($jsonConverterMock);

        /** @var ObjectService|PHPUnit_Framework_MockObject_MockObject $objectService */
        $objectService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            array($this->getSessionMock(), $cmisBindingsHelperMock)
        )->setMethods(
            array('getObjectUrl', 'post')
        )->getMock();

        $objectService->expects($this->atLeastOnce())->method('getObjectUrl')->with(
            $repositoryId,
            $objectId
        )->willReturn(Url::createFromUrl(self::BROWSER_URL_TEST));
        $objectService->expects($this->atLeastOnce())->method('post')->with(
            $expectedUrl
        )->willReturn($responseMock);

        $this->assertSame(
            $dummyObjectData,
            $objectService->moveObject(
                $repositoryId,
                $objectId,
                $targetFolderId,
                $sourceFolderId,
                $extension
            )
        );

        $this->assertSame($objectId, $dummyObjectData->getId());
    }

    /**
     * Data provider for moveObject
     *
     * @return array
     */
    public static function moveObjectDataProvider()
    {
        return array(
            array(
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                    . '?cmisaction=move&targetFolderId=targetFolderId&sourceFolderId=sourceFolderId&succinct=false'
                ),
                'repositoryId',
                'objectId',
                'targetFolderId',
                'sourceFolderId'
            )
        );
    }

    /**
     * @dataProvider getPropertiesDataProvider
     * @param string $expectedUrl
     * @param string $repositoryId
     * @param string $objectId
     * @param string|null $filter
     * @param ExtensionDataInterface|null $extension
     */
    public function testGetPropertiesCallsReadFunctionWithParameterizedQuery(
        $expectedUrl,
        $repositoryId,
        $objectId,
        $filter = null,
        ExtensionDataInterface $extension = null
    ) {
        $responseData = array('foo' => 'bar');
        $responseMock = $this->getMockBuilder('\\GuzzleHttp\\Message\\Response')->disableOriginalConstructor(
        )->setMethods(array('json'))->getMock();
        $responseMock->expects($this->any())->method('json')->willReturn($responseData);

        $dummyProperties = new Properties();
        $jsonConverterMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Converter\\JsonConverter')->setMethods(
            array('convertProperties')
        )->getMock();
        $jsonConverterMock->expects($this->once())->method('convertProperties')->with($responseData)->willReturn(
            $dummyProperties
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
            Constants::SELECTOR_PROPERTIES
        )->willReturn(Url::createFromUrl(self::BROWSER_URL_TEST));
        $objectService->expects($this->any())->method('read')->with($expectedUrl)->willReturn($responseMock);

        $objectService->getProperties(
            $repositoryId,
            $objectId,
            $filter,
            $extension
        );
    }

    /**
     * Data provider for getProperties
     *
     * @return array
     */
    public static function getPropertiesDataProvider()
    {
        return array(
            array(
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                    . '?filter=filter,123&succinct=false&dateTimeFormat=simple'
                ),
                'repositoryId',
                'objectId',
                'filter,123'
            ),
            array(
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                    . '?succinct=false&dateTimeFormat=simple'
                ),
                'repositoryId',
                'objectId'
            )
        );

    }

    /**
     * @dataProvider createItemDataProvider
     * @param string $expectedUrl
     * @param string $repositoryId
     * @param PropertiesInterface $properties
     * @param string|null $folderId
     * @param string[] $policies
     * @param AclInterface|null $addAces
     * @param AclInterface|null $removeAces
     * @param ExtensionDataInterface|null $extension
     */
    public function testCreateItemCallsPostFunctionWithParameterizedQuery(
        $expectedUrl,
        $repositoryId,
        PropertiesInterface $properties,
        $folderId = null,
        array $policies = array(),
        AclInterface $addAces = null,
        AclInterface $removeAces = null,
        ExtensionDataInterface $extension = null
    ) {
        $responseData = array('foo' => 'bar');
        $responseMock = $this->getMockBuilder('\\GuzzleHttp\\Message\\Response')->disableOriginalConstructor(
        )->setMethods(array('json'))->getMock();
        $responseMock->expects($this->any())->method('json')->willReturn($responseData);

        $jsonConverterMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Converter\\JsonConverter')->setMethods(
            array('convertObject')
        )->getMock();

        /** @var  ObjectData|PHPUnit_Framework_MockObject_MockObject $dummyObjectData */
        $dummyObjectData = $this->getMockBuilder('\\Dkd\\PhpCmis\\ObjectData\\ObjectData')->setMethods(
            array('getId')
        )->getMock();
        $dummyObjectData->expects($this->any())->method('getId')->willReturn('foo-id');
        $jsonConverterMock->expects($this->atLeastOnce())->method('convertObject')->with($responseData)->willReturn(
            $dummyObjectData
        );

        $cmisBindingsHelperMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBindingsHelper')->setMethods(
            array('getJsonConverter')
        )->getMock();
        $cmisBindingsHelperMock->expects($this->atLeastOnce())->method(
            'getJsonConverter'
        )->willReturn($jsonConverterMock);

        /** @var ObjectService|PHPUnit_Framework_MockObject_MockObject $objectService */
        $objectService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            array($this->getSessionMock(), $cmisBindingsHelperMock)
        )->setMethods(
            array('getObjectUrl', 'post')
        )->getMock();

        $objectService->expects($this->atLeastOnce())->method('getObjectUrl')->with(
            $repositoryId,
            $folderId
        )->willReturn(Url::createFromUrl(self::BROWSER_URL_TEST));
        $objectService->expects($this->atLeastOnce())->method('post')->with(
            $expectedUrl
        )->willReturn($responseMock);

        $objectService->createItem(
            $repositoryId,
            $properties,
            $folderId,
            $policies,
            $addAces,
            $removeAces,
            $extension
        );
    }

    /**
     * Data provider for testCreateItem
     *
     * @return array
     */
    public function createItemDataProvider()
    {
        $property = new PropertyString('cmis:name', 'name');
        $properties = new Properties();
        $properties->addProperty($property);

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
                    . '?propertyId[0]=cmis:name&propertyValue[0]=name&cmisaction=createItem&succinct=false'
                ),
                'repositoryId',
                $properties,
                'folderId'
            ),
            array(
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                    . '?propertyId[0]=cmis:name&propertyValue[0]=name&cmisaction=createItem'
                    . '&succinct=false'
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
                array('policyOne', 'policyTwo'),
                $addAcl,
                $removeAcl
            )
        );
    }

    /**
     * @dataProvider createDocumentFromSourceDataProvider
     * @param string $expectedUrl
     * @param string $repositoryId
     * @param string $sourceId
     * @param PropertiesInterface $properties
     * @param string|null $folderId
     * @param VersioningState|null $versioningState
     * @param string[] $policies
     * @param AclInterface|null $addAces
     * @param AclInterface|null $removeAces
     * @param ExtensionDataInterface|null $extension
     */
    public function testCreateDocumentFromSourceCallsPostFunctionWithParameterizedQuery(
        $expectedUrl,
        $repositoryId,
        $sourceId,
        PropertiesInterface $properties,
        $folderId = null,
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

        $jsonConverterMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Converter\\JsonConverter')->setMethods(
            array('convertObject')
        )->getMock();
        $dummyObjectData = $this->getMockBuilder('\\Dkd\\PhpCmis\\ObjectData\\ObjectData')->setMethods(
            array('getId')
        )->getMock();

        /** @var  ObjectData|PHPUnit_Framework_MockObject_MockObject $dummyObjectData */
        $dummyObjectData->expects($this->any())->method('getId')->willReturn('foo-id');
        $jsonConverterMock->expects($this->atLeastOnce())->method('convertObject')->with($responseData)->willReturn(
            $dummyObjectData
        );

        $cmisBindingsHelperMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBindingsHelper')->setMethods(
            array('getJsonConverter')
        )->getMock();
        $cmisBindingsHelperMock->expects($this->atLeastOnce())->method(
            'getJsonConverter'
        )->willReturn($jsonConverterMock);

        /** @var ObjectService|PHPUnit_Framework_MockObject_MockObject $objectService */
        $objectService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            array($this->getSessionMock(), $cmisBindingsHelperMock)
        )->setMethods(
            array('getObjectUrl', 'getRepositoryUrl', 'post')
        )->getMock();

        if ($folderId === null) {
            $objectService->expects($this->atLeastOnce())->method('getRepositoryUrl')->with(
                $repositoryId
            )->willReturn(Url::createFromUrl(self::BROWSER_URL_TEST));
        } else {
            $objectService->expects($this->atLeastOnce())->method('getObjectUrl')->with(
                $repositoryId,
                $folderId
            )->willReturn(Url::createFromUrl(self::BROWSER_URL_TEST));
        }

        $objectService->expects($this->atLeastOnce())->method('post')->with($expectedUrl)->willReturn($responseMock);

        $this->assertSame(
            $dummyObjectData->getId(),
            $objectService->createDocumentFromSource(
                $repositoryId,
                $sourceId,
                $properties,
                $folderId,
                $versioningState,
                $policies,
                $addAces,
                $removeAces,
                $extension
            )
        );
    }

    /**
     * Data provider for createDocumentFromSource
     *
     * @return array
     */
    public function createDocumentFromSourceDataProvider()
    {
        $property = new PropertyString('cmis:name', 'name');
        $properties = new Properties();
        $properties->addProperty($property);

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
                    . '?propertyId[0]=cmis:name&propertyValue[0]=name&cmisaction=createDocumentFromSource'
                    . '&sourceId=sourceId&succinct=false'
                ),
                'repositoryId',
                'sourceId',
                $properties,
                'folderId'
            ),
            array(
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                    . '?propertyId[0]=cmis:name&propertyValue[0]=name&cmisaction=createDocumentFromSource'
                    . '&succinct=false&sourceId=sourceId&versioningState=major'
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
                'sourceId',
                $properties,
                'folderId',
                VersioningState::cast(VersioningState::MAJOR),
                array('policyOne', 'policyTwo'),
                $addAcl,
                $removeAcl
            )
        );
    }

    /**
     * @dataProvider getObjectByPathDataProvider
     * @param string $expectedUrl
     * @param string $repositoryId
     * @param string $path
     * @param string|null $filter
     * @param boolean $includeAllowableActions
     * @param IncludeRelationships|null $includeRelationships
     * @param string $renditionFilter
     * @param boolean $includePolicyIds
     * @param boolean $includeAcl
     */
    public function testGetObjectByPathCallsReadFunctionWithParameterizedQuery(
        $expectedUrl,
        $repositoryId,
        $path,
        $filter = null,
        $includeAllowableActions = false,
        IncludeRelationships $includeRelationships = null,
        $renditionFilter = null,
        $includePolicyIds = false,
        $includeAcl = false
    ) {
        $responseData = array('foo' => 'bar');
        $responseMock = $this->getMockBuilder('\\GuzzleHttp\\Message\\Response')->disableOriginalConstructor(
        )->setMethods(array('json'))->getMock();
        $responseMock->expects($this->once())->method('json')->willReturn($responseData);

        $dummyObjectData = new ObjectData();
        $jsonConverterMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Converter\\JsonConverter')->setMethods(
            array('convertObject')
        )->getMock();
        $jsonConverterMock->expects($this->any())->method('convertObject')->with($responseData)->willReturn(
            $dummyObjectData
        );

        $cmisBindingsHelperMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBindingsHelper')->setMethods(
            array('getJsonConverter')
        )->getMock();
        $cmisBindingsHelperMock->expects($this->once())->method('getJsonConverter')->willReturn($jsonConverterMock);

        /** @var ObjectService|PHPUnit_Framework_MockObject_MockObject $objectService */
        $objectService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            array($this->getSessionMock(), $cmisBindingsHelperMock)
        )->setMethods(
            array('getPathUrl', 'read')
        )->getMock();

        $objectService->expects($this->once())->method('getPathUrl')->with(
            $repositoryId,
            $path,
            Constants::SELECTOR_OBJECT
        )->willReturn(Url::createFromUrl(self::BROWSER_URL_TEST));
        $objectService->expects($this->once())->method('read')->with($expectedUrl)->willReturn($responseMock);

        $objectService->getObjectByPath(
            $repositoryId,
            $path,
            $filter,
            $includeAllowableActions,
            $includeRelationships,
            $renditionFilter,
            $includePolicyIds,
            $includeAcl
        );
    }

    /**
     * Data provider for getObjectByPath
     *
     * @return array
     */
    public function getObjectByPathDataProvider()
    {
        return array(
            array(
                Url::createFromUrl(
                    self::BROWSER_URL_TEST . '?filter=filter,123&includeAllowableActions=true'
                    . '&includeRelationships=none&renditionFilter=foo:bar&includePolicyIds=true&includeACL=true'
                    . '&succinct=false&dateTimeFormat=simple'
                ),
                'repositoryId',
                'path/toAnObject',
                'filter,123',
                true,
                IncludeRelationships::cast(IncludeRelationships::NONE),
                'foo:bar',
                true,
                true,
            ),
            array(
                Url::createFromUrl(
                    self::BROWSER_URL_TEST . '?includeAllowableActions=false'
                    . '&includeRelationships=both&renditionFilter=foo:bar&includePolicyIds=false&includeACL=false'
                    . '&succinct=false&dateTimeFormat=simple'
                ),
                'repositoryId',
                'path/toAnObject',
                null,
                false,
                IncludeRelationships::cast(IncludeRelationships::BOTH),
                'foo:bar',
                false,
                false,
            ),
            array(
                Url::createFromUrl(
                    self::BROWSER_URL_TEST . '?filter=filter,345&includeAllowableActions=false'
                    . '&renditionFilter=foo:bar&includePolicyIds=false&includeACL=false'
                    . '&succinct=false&dateTimeFormat=simple'
                ),
                'repositoryId',
                'path/toAnObject',
                'filter,345',
                false,
                null,
                'foo:bar',
                false,
                false,
            )
        );
    }
}
