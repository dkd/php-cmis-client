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

use Dkd\PhpCmis\Bindings\Browser\ObjectService;
use Dkd\PhpCmis\Bindings\CmisBindingsHelper;
use Dkd\PhpCmis\Constants;
use Dkd\PhpCmis\Converter\JsonConverter;
use Dkd\PhpCmis\Data\AclInterface;
use Dkd\PhpCmis\Data\ExtensionDataInterface;
use Dkd\PhpCmis\Data\PropertiesInterface;
use Dkd\PhpCmis\DataObjects\AccessControlEntry;
use Dkd\PhpCmis\DataObjects\AccessControlList;
use Dkd\PhpCmis\DataObjects\FailedToDeleteData;
use Dkd\PhpCmis\DataObjects\ObjectData;
use Dkd\PhpCmis\DataObjects\Principal;
use Dkd\PhpCmis\DataObjects\Properties;
use Dkd\PhpCmis\DataObjects\PropertyId;
use Dkd\PhpCmis\DataObjects\PropertyString;
use Dkd\PhpCmis\DataObjects\RenditionData;
use Dkd\PhpCmis\Enum\IncludeRelationships;
use Dkd\PhpCmis\Enum\UnfileObject;
use Dkd\PhpCmis\Enum\VersioningState;
use Dkd\PhpCmis\SessionParameter;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Stream\StreamInterface;
use League\Url\Url;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * Class ObjectServiceTest
 */
class ObjectServiceTest extends AbstractBrowserBindingServiceTestCase
{
    const CLASS_TO_TEST = ObjectService::class;

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
        $responseData = ['foo' => 'bar'];
        $responseMock = $this->getMockBuilder('\\GuzzleHttp\\Message\\Response')->disableOriginalConstructor(
        )->setMethods(['getBody'])->getMock();
        $responseMock->expects($this->any())->method('getBody')->willReturn(json_encode($responseData));

        $dummyObjectData = new ObjectData();
        $jsonConverterMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Converter\\JsonConverter')->setMethods(
            ['convertObject']
        )->getMock();
        $jsonConverterMock->expects($this->once())->method('convertObject')->with($responseData)->willReturn(
            $dummyObjectData
        );

        $cmisBindingsHelperMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBindingsHelper')->setMethods(
            ['getJsonConverter']
        )->getMock();
        $cmisBindingsHelperMock->expects($this->any())->method('getJsonConverter')->willReturn($jsonConverterMock);

        /** @var ObjectService|PHPUnit_Framework_MockObject_MockObject $objectService */
        $objectService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            [$this->getSessionMock(), $cmisBindingsHelperMock]
        )->setMethods(
            ['getObjectUrl', 'read']
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
        return [
            [
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
            ],
            [
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
            ],
            [
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
            ]
        ];
    }


    /**
     * @dataProvider createDocumentDataProvider
     * @param string $expectedUrl
     * @param string $repositoryId
     * @param PropertiesInterface $properties
     * @param string|null $folderId
     * @param StreamInterface|null $contentStream
     * @param VersioningState|null $versioningState
     * @param string[] $policies
     * @param AclInterface|null $addAces
     * @param AclInterface|null $removeAces
     * @param mixed $expectedContentStream The expected content stream that should be passed to guzzle
     */
    public function testCreateDocumentCallsPostFunctionWithParameterizedQuery(
        Url $expectedUrl,
        array $expectedPostData,
        $expectedContentStream,
        $repositoryId,
        PropertiesInterface $properties,
        $folderId = null,
        StreamInterface $contentStream = null,
        VersioningState $versioningState = null,
        array $policies = [],
        AclInterface $addAces = null,
        AclInterface $removeAces = null
    ) {
        $responseData = ['foo' => 'bar'];
        $responseMock = $this->getMockBuilder(Response::class)->disableOriginalConstructor(
        )->setMethods(['getBody'])->getMock();
        $responseMock->expects($this->any())->method('getBody')->willReturn(json_encode($responseData));

        $jsonConverterMock = $this->getMockBuilder(JsonConverter::class)->setMethods(
            ['convertObject']
        )->getMock();
        $dummyObjectData = $this->getMockBuilder(ObjectData::class)->setMethods(
            ['getId']
        )->getMock();

        /** @var  ObjectData|PHPUnit_Framework_MockObject_MockObject $dummyObjectData */
        $dummyObjectData->expects($this->any())->method('getId')->willReturn('foo-id');
        $jsonConverterMock->expects($this->atLeastOnce())->method('convertObject')->with($responseData)->willReturn(
            $dummyObjectData
        );

        $cmisBindingsHelperMock = $this->getMockBuilder(CmisBindingsHelper::class)->setMethods(
            ['getJsonConverter']
        )->getMock();
        $cmisBindingsHelperMock->expects($this->atLeastOnce())->method(
            'getJsonConverter'
        )->willReturn($jsonConverterMock);

        /** @var ObjectService|PHPUnit_Framework_MockObject_MockObject $objectService */
        $objectService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            [$this->getSessionMock(), $cmisBindingsHelperMock]
        )->setMethods(
            ['getObjectUrl', 'getRepositoryUrl', 'post', 'setContentStream']
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

        if ($expectedContentStream) {
            $expectedPostData['content'] = $expectedContentStream;
        }

        $objectService->expects($this->atLeastOnce())->method('post')->with(
            $expectedUrl,
            $expectedPostData
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
        $property = new PropertyString('cmis:name', 'name.jpg');
        $properties = new Properties();
        $properties->addProperty($property);
        $streamWithFileExtension = $this->getMockBuilder('\\GuzzleHttp\\Stream\\StreamInterface')->setMethods(
            ['getMetadata']
        )->getMockForAbstractClass();
        $streamWithFileExtension->expects($this->any())->method('getMetadata')->with('uri')->willReturn(
            '/foo/bar/baz.jpg'
        );

        $streamWithoutFileExtension = $this->getMockBuilder('\\GuzzleHttp\\Stream\\StreamInterface')->setMethods(
            ['getMetadata']
        )->getMockForAbstractClass();
        $streamWithoutFileExtension->expects($this->any())->method('getMetadata')->with('uri')->willReturn(
            '/foo/bar/baz'
        );

        $expectedPostStream = $this->getMockBuilder(StreamInterface::class)->disableOriginalConstructor()->getMock();

        $principal1 = new Principal('principalId1');
        $ace1 = new AccessControlEntry($principal1, ['permissionValue1', 'permissionValue2']);

        $principal2 = new Principal('principalId2');
        $ace2 = new AccessControlEntry($principal2, ['permissionValue3', 'permissionValue4']);

        $addAcl = new AccessControlList([$ace1, $ace2]);

        $principal3 = new Principal('principalId3');
        $ace3 = new AccessControlEntry($principal3, ['permissionValue5', 'permissionValue6']);

        $principal4 = new Principal('principalId4');
        $ace4 = new AccessControlEntry($principal4, ['permissionValue7', 'permissionValue8']);

        $removeAcl = new AccessControlList([$ace3, $ace4]);

        return [
            'Create document without stream' => [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                ),
                [
                    'cmisaction' => 'createDocument',
                    'succinct' => 'false',
                    'propertyId' => [
                        'cmis:name'
                    ],
                    'propertyValue' => [
                        'name.jpg'
                    ]
                ],
                null,
                'repositoryId',
                $properties
            ],
            'Create document with a stream where the uri contains a file extension' => [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                ),
                [
                    'versioningState' => 'major',
                    'succinct' => 'false',
                    'cmisaction' => 'createDocument',
                    'propertyId' => [
                        'cmis:name'
                    ],
                    'propertyValue' => [
                        'name.jpg'
                    ],
                    'policy' => [
                        'policyOne', 'policyTwo'
                    ],
                    'addACEPrincipal' => [
                        'principalId1', 'principalId2'
                    ],
                    'addACEPermission' => [
                        ['permissionValue1', 'permissionValue2'],
                        ['permissionValue3', 'permissionValue4']
                    ],
                    'removeACEPrincipal' => [
                        'principalId3', 'principalId4'
                    ],
                    'removeACEPermission' => [
                        ['permissionValue5', 'permissionValue6'],
                        ['permissionValue7', 'permissionValue8']
                    ],
                ],
                $streamWithFileExtension,
                'repositoryId',
                $properties,
                'folderId',
                $streamWithFileExtension,
                VersioningState::cast(VersioningState::MAJOR),
                ['policyOne', 'policyTwo'],
                $addAcl,
                $removeAcl
            ],
            'Create document with a stream where the uri does not have a file extension' => [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                ),
                [
                    'versioningState' => 'major',
                    'succinct' => 'false',
                    'cmisaction' => 'createDocument',
                    'propertyId' => [
                        'cmis:name'
                    ],
                    'propertyValue' => [
                        'name.jpg'
                    ],
                    'policy' => [
                        'policyOne', 'policyTwo'
                    ],
                    'addACEPrincipal' => [
                        'principalId1', 'principalId2'
                    ],
                    'addACEPermission' => [
                        ['permissionValue1', 'permissionValue2'],
                        ['permissionValue3', 'permissionValue4']
                    ],
                    'removeACEPrincipal' => [
                        'principalId3', 'principalId4'
                    ],
                    'removeACEPermission' => [
                        ['permissionValue5', 'permissionValue6'],
                        ['permissionValue7', 'permissionValue8']
                    ],
                ],
                $streamWithoutFileExtension,
                'repositoryId',
                $properties,
                'folderId',
                $streamWithoutFileExtension,
                VersioningState::cast(VersioningState::MAJOR),
                ['policyOne', 'policyTwo'],
                $addAcl,
                $removeAcl
            ]
        ];
    }

    /**
     * @dataProvider createFolderDataProvider
     * @param string $expectedUrl
     * @param array $expectedPostData
     * @param string $repositoryId
     * @param PropertiesInterface $properties
     * @param string $folderId
     * @param string[] $policies
     * @param AclInterface $addAces
     * @param AclInterface $removeAces
     */
    public function testCreateFolderCallsPostFunctionWithParameterizedQuery(
        $expectedUrl,
        array $expectedPostData,
        $repositoryId,
        PropertiesInterface $properties,
        $folderId,
        array $policies = [],
        AclInterface $addAces = null,
        AclInterface $removeAces = null
    ) {
        $responseData = ['foo' => 'bar'];
        $responseMock = $this->getMockBuilder(Response::class)->disableOriginalConstructor(
        )->setMethods(['getBody'])->getMock();
        $responseMock->expects($this->any())->method('getBody')->willReturn(json_encode($responseData));

        $jsonConverterMock = $this->getMockBuilder(JsonConverter::class)->setMethods(
            ['convertObject']
        )->getMock();

        /** @var  ObjectData|PHPUnit_Framework_MockObject_MockObject $dummyObjectData */
        $dummyObjectData = $this->getMockBuilder(ObjectData::class)->setMethods(
            ['getId']
        )->getMock();
        $dummyObjectData->expects($this->any())->method('getId')->willReturn('foo-id');
        $jsonConverterMock->expects($this->atLeastOnce())->method('convertObject')->with($responseData)->willReturn(
            $dummyObjectData
        );

        $cmisBindingsHelperMock = $this->getMockBuilder(CmisBindingsHelper::class)->setMethods(
            ['getJsonConverter']
        )->getMock();
        $cmisBindingsHelperMock->expects($this->atLeastOnce())->method(
            'getJsonConverter'
        )->willReturn($jsonConverterMock);

        /** @var ObjectService|PHPUnit_Framework_MockObject_MockObject $objectService */
        $objectService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            [$this->getSessionMock(), $cmisBindingsHelperMock]
        )->setMethods(
            ['getObjectUrl', 'post']
        )->getMock();

        $objectService->expects($this->atLeastOnce())->method('getObjectUrl')->with(
            $repositoryId,
            $folderId
        )->willReturn(Url::createFromUrl(self::BROWSER_URL_TEST));
        $objectService->expects($this->atLeastOnce())->method('post')->with(
            $expectedUrl,
            $expectedPostData
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
        $ace1 = new AccessControlEntry($principal1, ['permissionValue1', 'permissionValue2']);

        $principal2 = new Principal('principalId2');
        $ace2 = new AccessControlEntry($principal2, ['permissionValue3', 'permissionValue4']);

        $addAcl = new AccessControlList([$ace1, $ace2]);

        $principal3 = new Principal('principalId3');
        $ace3 = new AccessControlEntry($principal3, ['permissionValue5', 'permissionValue6']);

        $principal4 = new Principal('principalId4');
        $ace4 = new AccessControlEntry($principal4, ['permissionValue7', 'permissionValue8']);

        $removeAcl = new AccessControlList([$ace3, $ace4]);

        return [
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                ),
                [
                    'cmisaction' => 'createFolder',
                    'succinct' => 'false',
                    'propertyId' => [
                        'cmis:name'
                    ],
                    'propertyValue' => [
                        'name'
                    ]
                ],
                'repositoryId',
                $properties,
                'parentFolderId'
            ],
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                ),
                [
                    'cmisaction' => 'createFolder',
                    'succinct' => 'false',
                    'propertyId' => [
                        'cmis:name'
                    ],
                    'propertyValue' => [
                        'name'
                    ],
                    'addACEPrincipal' => [
                        'principalId1',
                        'principalId2'
                    ],
                    'addACEPermission' => [
                        [
                            'permissionValue1',
                            'permissionValue2'
                        ],
                        [
                            'permissionValue3',
                            'permissionValue4'
                        ]
                    ],
                    'policy' => [
                        'policyOne',
                        'policyTwo'
                    ],
                    'removeACEPrincipal' => [
                        'principalId3',
                        'principalId4'
                    ],
                    'removeACEPermission' => [
                        [
                            'permissionValue5',
                            'permissionValue6'
                        ],
                        [
                            'permissionValue7',
                            'permissionValue8'
                        ]
                    ]
                ],
                'repositoryId',
                $properties,
                'parentFolderId',
                ['policyOne', 'policyTwo'],
                $addAcl,
                $removeAcl
            ]
        ];
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
        $responseMock = $this->getMockBuilder(
            Response::class
        )->disableOriginalConstructor()->getMock();

        $cmisBindingsHelperMock = $this->getMockBuilder(CmisBindingsHelper::class)->getMock();

        /** @var ObjectService|PHPUnit_Framework_MockObject_MockObject $objectService */
        $objectService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            [$this->getSessionMock(), $cmisBindingsHelperMock]
        )->setMethods(
            ['getObjectUrl', 'post']
        )->getMock();

        $objectService->expects($this->atLeastOnce())->method('getObjectUrl')->with(
            $repositoryId,
            $objectId
        )->willReturn(Url::createFromUrl(self::BROWSER_URL_TEST));
        $objectService->expects($this->atLeastOnce())->method('post')->with(
              $expectedUrl,
              [
                  'cmisaction' => 'delete',
                  'allVersions' => $allVersions ? 'true' : 'false'
              ]
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
        return [
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                ),
                'repositoryId',
                'objectId',
                true
            ],
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                ),
                'repositoryId',
                'objectId',
                false
            ]
        ];
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
        $responseData = ['foo' => 'bar'];
        $responseMock = $this->getMockBuilder(Response::class)->disableOriginalConstructor(
        )->setMethods(['getBody'])->getMock();
        $responseMock->expects($this->any())->method('getBody')->willReturn(json_encode($responseData));

        $jsonConverterMock = $this->getMockBuilder(JsonConverter::class)->setMethods(
            ['convertObject']
        )->getMock();

        /** @var  ObjectData|PHPUnit_Framework_MockObject_MockObject $dummyObjectData */
        $dummyObjectData = $this->getMockBuilder(ObjectData::class)->setMethods(
            ['getId']
        )->getMock();
        $dummyObjectData->expects($this->any())->method('getId')->willReturn('foo-id');
        $jsonConverterMock->expects($this->atLeastOnce())->method('convertObject')->with($responseData)->willReturn(
            $dummyObjectData
        );

        $cmisBindingsHelperMock = $this->getMockBuilder(CmisBindingsHelper::class)->setMethods(
            ['getJsonConverter']
        )->getMock();
        $cmisBindingsHelperMock->expects($this->atLeastOnce())->method(
            'getJsonConverter'
        )->willReturn($jsonConverterMock);

        /** @var ObjectService|PHPUnit_Framework_MockObject_MockObject $objectService */
        $objectService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            [$this->getSessionMock(), $cmisBindingsHelperMock]
        )->setMethods(
            ['getObjectUrl', 'post']
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
        return [
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                    . '?cmisaction=move&targetFolderId=targetFolderId&sourceFolderId=sourceFolderId&succinct=false'
                ),
                'repositoryId',
                'objectId',
                'targetFolderId',
                'sourceFolderId'
            ]
        ];
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
        $responseData = ['foo' => 'bar'];
        $responseMock = $this->getMockBuilder(Response::class)->disableOriginalConstructor(
        )->setMethods(['getBody'])->getMock();
        $responseMock->expects($this->any())->method('getBody')->willReturn(json_encode($responseData));

        $dummyProperties = new Properties();
        $jsonConverterMock = $this->getMockBuilder(JsonConverter::class)->setMethods(
            ['convertProperties']
        )->getMock();
        $jsonConverterMock->expects($this->once())->method('convertProperties')->with($responseData)->willReturn(
            $dummyProperties
        );

        $cmisBindingsHelperMock = $this->getMockBuilder(CmisBindingsHelper::class)->setMethods(
            ['getJsonConverter']
        )->getMock();
        $cmisBindingsHelperMock->expects($this->any())->method('getJsonConverter')->willReturn($jsonConverterMock);

        /** @var ObjectService|PHPUnit_Framework_MockObject_MockObject $objectService */
        $objectService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            [$this->getSessionMock(), $cmisBindingsHelperMock]
        )->setMethods(
            ['getObjectUrl', 'read']
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
        return [
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                    . '?filter=filter,123&succinct=false&dateTimeFormat=simple'
                ),
                'repositoryId',
                'objectId',
                'filter,123'
            ],
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                    . '?succinct=false&dateTimeFormat=simple'
                ),
                'repositoryId',
                'objectId'
            ]
        ];

    }

    /**
     * @dataProvider createItemDataProvider
     * @param string $expectedUrl
     * @param array $expectedPostData
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
        array $expectedPostData,
        $repositoryId,
        PropertiesInterface $properties,
        $folderId = null,
        array $policies = [],
        AclInterface $addAces = null,
        AclInterface $removeAces = null,
        ExtensionDataInterface $extension = null
    ) {
        $responseData = ['foo' => 'bar'];
        $responseMock = $this->getMockBuilder(Response::class)->disableOriginalConstructor(
        )->setMethods(['getBody'])->getMock();
        $responseMock->expects($this->any())->method('getBody')->willReturn(json_encode($responseData));

        $jsonConverterMock = $this->getMockBuilder(JsonConverter::class)->setMethods(
            ['convertObject']
        )->getMock();

        /** @var  ObjectData|PHPUnit_Framework_MockObject_MockObject $dummyObjectData */
        $dummyObjectData = $this->getMockBuilder(ObjectData::class)->setMethods(
            ['getId']
        )->getMock();
        $dummyObjectData->expects($this->any())->method('getId')->willReturn('foo-id');
        $jsonConverterMock->expects($this->atLeastOnce())->method('convertObject')->with($responseData)->willReturn(
            $dummyObjectData
        );

        $cmisBindingsHelperMock = $this->getMockBuilder(CmisBindingsHelper::class)->setMethods(
            ['getJsonConverter']
        )->getMock();
        $cmisBindingsHelperMock->expects($this->atLeastOnce())->method(
            'getJsonConverter'
        )->willReturn($jsonConverterMock);

        /** @var ObjectService|PHPUnit_Framework_MockObject_MockObject $objectService */
        $objectService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            [$this->getSessionMock(), $cmisBindingsHelperMock]
        )->setMethods(
            ['getObjectUrl', 'post']
        )->getMock();

        $objectService->expects($this->atLeastOnce())->method('getObjectUrl')->with(
            $repositoryId,
            $folderId
        )->willReturn(Url::createFromUrl(self::BROWSER_URL_TEST));
        $objectService->expects($this->atLeastOnce())->method('post')->with(
            $expectedUrl,
            $expectedPostData
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
        $ace1 = new AccessControlEntry($principal1, ['permissionValue1', 'permissionValue2']);

        $principal2 = new Principal('principalId2');
        $ace2 = new AccessControlEntry($principal2, ['permissionValue3', 'permissionValue4']);

        $addAcl = new AccessControlList([$ace1, $ace2]);

        $principal3 = new Principal('principalId3');
        $ace3 = new AccessControlEntry($principal3, ['permissionValue5', 'permissionValue6']);

        $principal4 = new Principal('principalId4');
        $ace4 = new AccessControlEntry($principal4, ['permissionValue7', 'permissionValue8']);

        $removeAcl = new AccessControlList([$ace3, $ace4]);

        return [
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                ),
                [
                    'cmisaction' => 'createItem',
                    'succinct' => 'false',
                    'propertyId' => [
                        'cmis:name'
                    ],
                    'propertyValue' => [
                        'name'
                    ]
                ],
                'repositoryId',
                $properties,
                'folderId'
            ],
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                ),
                [
                    'cmisaction' => 'createItem',
                    'succinct' => 'false',
                    'propertyId' => [
                        'cmis:name'
                    ],
                    'propertyValue' => [
                        'name'
                    ],
                    'addACEPrincipal' => [
                        'principalId1',
                        'principalId2'
                    ],
                    'addACEPermission' => [
                        [
                            'permissionValue1',
                            'permissionValue2'
                        ],
                        [
                            'permissionValue3',
                            'permissionValue4'
                        ]
                    ],
                    'policy' => [
                        'policyOne',
                        'policyTwo'
                    ],
                    'removeACEPrincipal' => [
                        'principalId3',
                        'principalId4'
                    ],
                    'removeACEPermission' => [
                        [
                            'permissionValue5',
                            'permissionValue6'
                        ],
                        [
                            'permissionValue7',
                            'permissionValue8'
                        ]
                    ]
                ],
                'repositoryId',
                $properties,
                'folderId',
                ['policyOne', 'policyTwo'],
                $addAcl,
                $removeAcl
            ]
        ];
    }

    /**
     * @dataProvider createDocumentFromSourceDataProvider
     * @param string $expectedUrl
     * @param array $expectedPostData
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
        array $expectedPostData,
        $repositoryId,
        $sourceId,
        PropertiesInterface $properties,
        $folderId = null,
        VersioningState $versioningState = null,
        array $policies = [],
        AclInterface $addAces = null,
        AclInterface $removeAces = null,
        ExtensionDataInterface $extension = null
    ) {
        $responseData = ['foo' => 'bar'];
        $responseMock = $this->getMockBuilder(Response::class)->disableOriginalConstructor(
        )->setMethods(['getBody'])->getMock();
        $responseMock->expects($this->any())->method('getBody')->willReturn(json_encode($responseData));

        $jsonConverterMock = $this->getMockBuilder(JsonConverter::class)->setMethods(
            ['convertObject']
        )->getMock();
        $dummyObjectData = $this->getMockBuilder(ObjectData::class)->setMethods(
            ['getId']
        )->getMock();

        /** @var  ObjectData|PHPUnit_Framework_MockObject_MockObject $dummyObjectData */
        $dummyObjectData->expects($this->any())->method('getId')->willReturn('foo-id');
        $jsonConverterMock->expects($this->atLeastOnce())->method('convertObject')->with($responseData)->willReturn(
            $dummyObjectData
        );

        $cmisBindingsHelperMock = $this->getMockBuilder(CmisBindingsHelper::class)->setMethods(
            ['getJsonConverter']
        )->getMock();
        $cmisBindingsHelperMock->expects($this->atLeastOnce())->method(
            'getJsonConverter'
        )->willReturn($jsonConverterMock);

        /** @var ObjectService|PHPUnit_Framework_MockObject_MockObject $objectService */
        $objectService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            [$this->getSessionMock(), $cmisBindingsHelperMock]
        )->setMethods(
            ['getObjectUrl', 'getRepositoryUrl', 'post']
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
        $ace1 = new AccessControlEntry($principal1, ['permissionValue1', 'permissionValue2']);

        $principal2 = new Principal('principalId2');
        $ace2 = new AccessControlEntry($principal2, ['permissionValue3', 'permissionValue4']);

        $addAcl = new AccessControlList([$ace1, $ace2]);

        $principal3 = new Principal('principalId3');
        $ace3 = new AccessControlEntry($principal3, ['permissionValue5', 'permissionValue6']);

        $principal4 = new Principal('principalId4');
        $ace4 = new AccessControlEntry($principal4, ['permissionValue7', 'permissionValue8']);

        $removeAcl = new AccessControlList([$ace3, $ace4]);

        return [
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                ),
                [
                    'succinct' => 'false',
                    'cmisaction' => 'createDocumentFromSource',
                    'propertyId' => [
                        'cmis:name'
                    ],
                    'propertyValue' => [
                        'name'
                    ],
                    'sourceId' => 'sourceId'
                ],
                'repositoryId',
                'sourceId',
                $properties,
                'folderId'
            ],
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                ),
                [
                    'versioningState' => 'major',
                    'succinct' => 'false',
                    'cmisaction' => 'createDocumentFromSource',
                    'propertyId' => [
                        'cmis:name'
                    ],
                    'propertyValue' => [
                        'name'
                    ],
                    'policy' => [
                        'policyOne', 'policyTwo'
                    ],
                    'addACEPrincipal' => [
                        'principalId1', 'principalId2'
                    ],
                    'addACEPermission' => [
                        ['permissionValue1', 'permissionValue2'],
                        ['permissionValue3', 'permissionValue4']
                    ],
                    'removeACEPrincipal' => [
                        'principalId3', 'principalId4'
                    ],
                    'removeACEPermission' => [
                        ['permissionValue5', 'permissionValue6'],
                        ['permissionValue7', 'permissionValue8']
                    ],
                    'sourceId' => 'sourceId'
                ],
                'repositoryId',
                'sourceId',
                $properties,
                'folderId',
                VersioningState::cast(VersioningState::MAJOR),
                ['policyOne', 'policyTwo'],
                $addAcl,
                $removeAcl
            ]
        ];
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
        $responseData = ['foo' => 'bar'];
        $responseMock = $this->getMockBuilder(Response::class)->disableOriginalConstructor(
        )->setMethods(['getBody'])->getMock();
        $responseMock->expects($this->once())->method('getBody')->willReturn(json_encode($responseData));

        $dummyObjectData = new ObjectData();
        $jsonConverterMock = $this->getMockBuilder(JsonConverter::class)->setMethods(
            ['convertObject']
        )->getMock();
        $jsonConverterMock->expects($this->any())->method('convertObject')->with($responseData)->willReturn(
            $dummyObjectData
        );

        $cmisBindingsHelperMock = $this->getMockBuilder(CmisBindingsHelper::class)->setMethods(
            ['getJsonConverter']
        )->getMock();
        $cmisBindingsHelperMock->expects($this->once())->method('getJsonConverter')->willReturn($jsonConverterMock);

        /** @var ObjectService|PHPUnit_Framework_MockObject_MockObject $objectService */
        $objectService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            [$this->getSessionMock(), $cmisBindingsHelperMock]
        )->setMethods(
            ['getPathUrl', 'read']
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
        return [
            [
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
            ],
            [
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
            ],
            [
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
            ]
        ];
    }

    /**
     * @dataProvider updatePropertiesDataProvider
     * @param string $expectedUrl
     * @param string $repositoryId
     * @param string $objectId
     * @param PropertiesInterface $properties
     * @param string|null $changeToken
     * @param array $sessionParameterMap
     */
    public function testUpdatePropertiesCallsPostFunctionWithParameterizedQuery(
        Url $expectedUrl,
        array $expectedPostData,
        $repositoryId,
        $objectId,
        PropertiesInterface $properties,
        $changeToken = null,
        $sessionParameterMap = []
    ) {
        $expectedUrl->setQuery($expectedPostData);

        $responseData = ['foo' => 'bar'];
        $responseMock = $this->getMockBuilder(Response::class)->disableOriginalConstructor(
        )->setMethods(['getBody'])->getMock();
        $responseMock->expects($this->any())->method('getBody')->willReturn(json_encode($responseData));

        $jsonConverterMock = $this->getMockBuilder(JsonConverter::class)->setMethods(
            ['convertObject']
        )->getMock();

        /** @var  ObjectData|PHPUnit_Framework_MockObject_MockObject $dummyObjectData */
        $dummyObjectData = $this->getMockBuilder(ObjectData::class)->setMethods(
            ['getId', 'getProperties']
        )->getMock();

        $newObjectId = 'foo-id';
        $newChangeTokenId = 'newTokenId';
        $dummyProperties = new Properties();
        $newChangeTokenProperty = new PropertyId('cmis:changeToken', $newChangeTokenId);
        $dummyProperties->addProperty($newChangeTokenProperty);

        $dummyObjectData->expects($this->any())->method('getId')->willReturn($newObjectId);
        $dummyObjectData->expects($this->any())->method('getProperties')->willReturn($dummyProperties);

        $jsonConverterMock->expects($this->atLeastOnce())->method('convertObject')->with($responseData)->willReturn(
            $dummyObjectData
        );

        $cmisBindingsHelperMock = $this->getMockBuilder(CmisBindingsHelper::class)->setMethods(
            ['getJsonConverter']
        )->getMock();
        $cmisBindingsHelperMock->expects($this->atLeastOnce())->method(
            'getJsonConverter'
        )->willReturn($jsonConverterMock);

        $sessionMock = $this->getSessionMock($sessionParameterMap);

        /** @var ObjectService|PHPUnit_Framework_MockObject_MockObject $objectService */
        $objectService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            [$sessionMock, $cmisBindingsHelperMock]
        )->setMethods(
            ['getObjectUrl', 'post', 'getSession']
        )->getMock();

        $objectService->expects($this->atLeastOnce())->method('getObjectUrl')->with(
            $repositoryId,
            $objectId
        )->willReturn(Url::createFromUrl(self::BROWSER_URL_TEST));

        $objectService->expects($this->atLeastOnce())->method('post')->with($expectedUrl)->willReturn($responseMock);
        $objectService->expects($this->atLeastOnce())->method('getSession')->willReturn($sessionMock);

        $objectService->updateProperties(
            $repositoryId,
            $objectId,
            $properties,
            $changeToken
        );

        $this->assertEquals($objectId, $newObjectId);
        if ($changeToken !== null) {
            $this->assertEquals($changeToken, $newChangeTokenId);
        }
    }

    /**
     * Data provider for updateProperties
     *
     * @return array
     */
    public function updatePropertiesDataProvider()
    {
        $propertySet1 = new Properties();
        $propertySet1->addProperties(
            [
                new PropertyString('cmis:name', 'name'),
                new PropertyString('cmis:description', 'description')
            ]
        );

        $propertySet2 = new Properties();
        $propertySet2->addProperties(
            [
                new PropertyString('cmis:name', 'foo'),
                new PropertyString('cmis:description', 'bar')
            ]
        );

        return [
            'Parameter set with defined changeToken and empty session parameters' => [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                    . '?propertyId[0]=cmis:name&propertyValue[0]=name'
                    . '&propertyId[1]=cmis:description&propertyValue[1]=description'
                    . '&changeToken=changeToken&cmisaction=update&succinct=false'
                ),
                [
                    'changeToken' => 'changeToken'
                ],
                'repositoryId',
                'objectId',
                $propertySet1,
                'changeToken',
                []
            ],
            'Parameter set with empty changeToken and defined session parameter' => [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                    . '?propertyId[0]=cmis:name&propertyValue[0]=foo'
                    . '&propertyId[1]=cmis:description&propertyValue[1]=bar'
                    . '&cmisaction=update&succinct=true'
                ),
                [],
                'repositoryId',
                'objectId',
                $propertySet2,
                null,
                [
                    [SessionParameter::BROWSER_SUCCINCT, null, true]
                ]
            ],
            'Parameter set with defined changeToken and defined OMIT_CHANGE_TOKENS session parameter' => [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                    . '?propertyId[0]=cmis:name&propertyValue[0]=foo'
                    . '&propertyId[1]=cmis:description&propertyValue[1]=bar'
                    . '&cmisaction=update&succinct=false'
                ),
                [],
                'repositoryId',
                'objectId',
                $propertySet2,
                'changeToken',
                [
                    [SessionParameter::OMIT_CHANGE_TOKENS, false, true]
                ]
            ]
        ];
    }

    /**
     * @dataProvider setContentStreamDataProvider
     * @param string $expectedUrl
     * @param string $repositoryId
     * @param string $objectId
     * @param StreamInterface $contentStream
     * @param boolean $overwriteFlag
     * @param string|null $changeToken
     * @param array $sessionParameterMap
     */
    public function testSetContentStreamCallsPostFunctionWithParameterizedQuery(
        $expectedUrl,
        $repositoryId,
        $objectId,
        StreamInterface $contentStream,
        $overwriteFlag = true,
        $changeToken = null,
        $sessionParameterMap = []
    ) {
        $responseData = ['foo' => 'bar'];
        $responseMock = $this->getMockBuilder(Response::class)->disableOriginalConstructor(
        )->setMethods(['getBody'])->getMock();
        $responseMock->expects($this->any())->method('getBody')->willReturn(json_encode($responseData));

        $jsonConverterMock = $this->getMockBuilder(JsonConverter::class)->setMethods(
            ['convertObject']
        )->getMock();

        /** @var  ObjectData|PHPUnit_Framework_MockObject_MockObject $dummyObjectData */
        $dummyObjectData = $this->getMockBuilder(ObjectData::class)->setMethods(
            ['getId', 'getProperties']
        )->getMock();

        $newObjectId = 'foo-id';
        $newChangeTokenId = 'newTokenId';
        $dummyProperties = new Properties();
        $newChangeTokenProperty = new PropertyId('cmis:changeToken', $newChangeTokenId);
        $dummyProperties->addProperty($newChangeTokenProperty);

        $dummyObjectData->expects($this->any())->method('getId')->willReturn($newObjectId);
        $dummyObjectData->expects($this->any())->method('getProperties')->willReturn($dummyProperties);

        $jsonConverterMock->expects($this->atLeastOnce())->method('convertObject')->with($responseData)->willReturn(
            $dummyObjectData
        );

        $cmisBindingsHelperMock = $this->getMockBuilder(CmisBindingsHelper::class)->setMethods(
            ['getJsonConverter']
        )->getMock();
        $cmisBindingsHelperMock->expects($this->atLeastOnce())->method(
            'getJsonConverter'
        )->willReturn($jsonConverterMock);

        $sessionMock = $this->getSessionMock($sessionParameterMap);

        /** @var ObjectService|PHPUnit_Framework_MockObject_MockObject $objectService */
        $objectService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            [$sessionMock, $cmisBindingsHelperMock]
        )->setMethods(
            ['getObjectUrl', 'post', 'getSession']
        )->getMock();

        $objectService->expects($this->atLeastOnce())->method('getObjectUrl')->with(
            $repositoryId,
            $objectId
        )->willReturn(Url::createFromUrl(self::BROWSER_URL_TEST));

        $objectService->expects($this->atLeastOnce())->method('post')->with(
            $expectedUrl,
            ['content' => $contentStream]
        )->willReturn($responseMock);
        $objectService->expects($this->atLeastOnce())->method('getSession')->willReturn($sessionMock);

        $objectService->setContentStream(
            $repositoryId,
            $objectId,
            $contentStream,
            $overwriteFlag,
            $changeToken
        );

        $this->assertEquals($objectId, $newObjectId);
        if ($changeToken !== null) {
            $this->assertEquals($changeToken, $newChangeTokenId);
        }
    }

    /**
     * Data provider for setContentStream
     *
     * @return array
     */
    public function setContentStreamDataProvider()
    {
        $contentStream = $this->getMockForAbstractClass(StreamInterface::class);
        return [
            'Parameter set with defined changeToken and empty session parameters' => [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                    . '?cmisaction=setContent&overwriteFlag=true'
                    . '&changeToken=changeToken&succinct=false'
                ),
                'repositoryId',
                'objectId',
                $contentStream,
                true,
                'changeToken',
                []
            ],
            'Parameter set with empty changeToken and defined session parameter' => [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                    . '?cmisaction=setContent&overwriteFlag=true&succinct=true'
                ),
                'repositoryId',
                'objectId',
                $contentStream,
                true,
                null,
                [
                    [SessionParameter::BROWSER_SUCCINCT, null, true]
                ]
            ],
            'Parameter set with defined changeToken and defined OMIT_CHANGE_TOKENS session parameter' => [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                    . '?cmisaction=setContent&overwriteFlag=false&succinct=false'
                ),
                'repositoryId',
                'objectId',
                $contentStream,
                false,
                'changeToken',
                [
                    [SessionParameter::OMIT_CHANGE_TOKENS, false, true]
                ]
            ]
        ];
    }

    /**
     * @dataProvider deleteContentStreamDataProvider
     * @param string $expectedUrl
     * @param string $repositoryId
     * @param string $objectId
     * @param StreamInterface $contentStream
     * @param boolean $overwriteFlag
     * @param string|null $changeToken
     * @param array $sessionParameterMap
     */
    public function testDeleteContentStreamCallsPostFunctionWithParameterizedQuery(
        $expectedUrl,
        $repositoryId,
        $objectId,
        $changeToken = null,
        $sessionParameterMap = []
    ) {
        $responseData = ['foo' => 'bar'];
        $responseMock = $this->getMockBuilder(Response::class)->disableOriginalConstructor(
        )->setMethods(['getBody'])->getMock();
        $responseMock->expects($this->any())->method('getBody')->willReturn(json_encode($responseData));

        $jsonConverterMock = $this->getMockBuilder(JsonConverter::class)->setMethods(
            ['convertObject']
        )->getMock();

        /** @var  ObjectData|PHPUnit_Framework_MockObject_MockObject $dummyObjectData */
        $dummyObjectData = $this->getMockBuilder(ObjectData::class)->setMethods(
            ['getId', 'getProperties']
        )->getMock();

        $newObjectId = 'foo-id';
        $newChangeTokenId = 'newTokenId';
        $dummyProperties = new Properties();
        $newChangeTokenProperty = new PropertyId('cmis:changeToken', $newChangeTokenId);
        $dummyProperties->addProperty($newChangeTokenProperty);

        $dummyObjectData->expects($this->any())->method('getId')->willReturn($newObjectId);
        $dummyObjectData->expects($this->any())->method('getProperties')->willReturn($dummyProperties);

        $jsonConverterMock->expects($this->atLeastOnce())->method('convertObject')->with($responseData)->willReturn(
            $dummyObjectData
        );

        $cmisBindingsHelperMock = $this->getMockBuilder(CmisBindingsHelper::class)->setMethods(
            ['getJsonConverter']
        )->getMock();
        $cmisBindingsHelperMock->expects($this->atLeastOnce())->method(
            'getJsonConverter'
        )->willReturn($jsonConverterMock);

        $sessionMock = $this->getSessionMock($sessionParameterMap);

        /** @var ObjectService|PHPUnit_Framework_MockObject_MockObject $objectService */
        $objectService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            [$sessionMock, $cmisBindingsHelperMock]
        )->setMethods(
            ['getObjectUrl', 'post', 'getSession']
        )->getMock();

        $objectService->expects($this->atLeastOnce())->method('getObjectUrl')->with(
            $repositoryId,
            $objectId
        )->willReturn(Url::createFromUrl(self::BROWSER_URL_TEST));

        $objectService->expects($this->atLeastOnce())->method('post')->with($expectedUrl)->willReturn($responseMock);
        $objectService->expects($this->atLeastOnce())->method('getSession')->willReturn($sessionMock);

        $objectService->deleteContentStream(
            $repositoryId,
            $objectId,
            $changeToken
        );

        $this->assertEquals($objectId, $newObjectId);
        if ($changeToken !== null) {
            $this->assertEquals($changeToken, $newChangeTokenId);
        }
    }

    /**
     * Data provider for deleteContentStream
     *
     * @return array
     */
    public function deleteContentStreamDataProvider()
    {
        return [
            'Parameter set with defined changeToken and empty session parameters' => [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                    . '?cmisaction=deleteContent&changeToken=changeToken&succinct=false'
                ),
                'repositoryId',
                'objectId',
                'changeToken',
                []
            ],
            'Parameter set with empty changeToken and defined session parameter' => [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                    . '?cmisaction=deleteContent&succinct=true'
                ),
                'repositoryId',
                'objectId',
                null,
                [
                    [SessionParameter::BROWSER_SUCCINCT, null, true]
                ]
            ],
            'Parameter set with defined changeToken and defined OMIT_CHANGE_TOKENS session parameter' => [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                    . '?cmisaction=deleteContent&succinct=false'
                ),
                'repositoryId',
                'objectId',
                'changeToken',
                [
                    [SessionParameter::OMIT_CHANGE_TOKENS, false, true]
                ]
            ]
        ];
    }

    /**
     * @dataProvider getContentStreamDataProvider
     * @param string $expectedUrl
     * @param string $repositoryId
     * @param string $objectId
     * @param string|null $streamId
     * @param integer|null $offset
     * @param integer|null $length
     * @param array $sessionParameterMap
     */
    public function testGetContentStreamCallsGetFunctionWithParameterizedQuery(
        $expectedUrl,
        $repositoryId,
        $objectId,
        $streamId = null,
        $offset = null,
        $length = null
    ) {
        $contentStream = $stream = $this->getMockForAbstractClass(StreamInterface::class);
        $responseMock = $this->getMockBuilder(Response::class)->disableOriginalConstructor(
        )->setMethods(['getBody'])->getMock();
        $responseMock->expects($this->any())->method('getBody')->willReturn($contentStream);

        $httpInvoker = $this->getMockBuilder(CmisBindingsHelper::class)->setMethods(
            ['get']
        )->getMock();
        $httpInvoker->expects($this->any())->method('get')->willReturn($responseMock);

        $cmisBindingsHelperMock = $this->getMockBuilder(CmisBindingsHelper::class)->setMethods(
            ['getHttpInvoker']
        )->getMock();
        $cmisBindingsHelperMock->expects($this->any())->method('getHttpInvoker')->willReturn($httpInvoker);

        /** @var ObjectService|PHPUnit_Framework_MockObject_MockObject $objectService */
        $objectService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            [$this->getSessionMock(), $cmisBindingsHelperMock]
        )->setMethods(
            ['getObjectUrl']
        )->getMock();

        $objectService->expects($this->any())->method('getObjectUrl')->with(
            $repositoryId,
            $objectId,
            Constants::SELECTOR_CONTENT
        )->willReturn(Url::createFromUrl(self::BROWSER_URL_TEST));
        $objectService->expects($this->any())->method('read')->with($expectedUrl)->willReturn($responseMock);

        $responseContentStream = $objectService->getContentStream(
            $repositoryId,
            $objectId,
            $streamId,
            $offset,
            $length
        );

        if ($offset !== null) {
            $this->assertInstanceOf('\\GuzzleHttp\\Stream\\LimitStream', $responseContentStream);
        } else {
            $this->assertSame($contentStream, $responseContentStream);
        }
    }

    /**
     * Data provider for getContentStream
     *
     * @return array
     */
    public function getContentStreamDataProvider()
    {
        return [
            'Parameter set without optional parameters' => [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                ),
                'repositoryId',
                'objectId',
            ],
            'Parameter set with streamId' => [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                    . '?streamId=streamId'
                ),
                'repositoryId',
                'objectId',
                'streamId'
            ],
            'Parameter set with offset and length' => [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                    . '?streamId=streamId'
                ),
                'repositoryId',
                'objectId',
                null,
                0,
                20
            ]
        ];
    }

    /**
     * @dataProvider deleteTreeDataProvider
     * @param string $expectedUrl
     * @param string $repositoryId
     * @param string $folderId
     * @param boolean $allVersions
     * @param UnfileObject $unfileObjects
     * @param boolean $continueOnFailure
     */
    public function testDeleteTreeCallsPostFunctionWithParameterizedQuery(
        $expectedUrl,
        $repositoryId,
        $folderId,
        $allVersions = true,
        UnfileObject $unfileObjects = null,
        $continueOnFailure = false
    ) {
        $responseData = ['foo' => 'bar'];
        $responseMock = $this->getMockBuilder(Response::class)->disableOriginalConstructor(
        )->setMethods(['getBody'])->getMock();
        $responseMock->expects($this->any())->method('getBody')->willReturn(json_encode($responseData));

        $jsonConverterMock = $this->getMockBuilder(JsonConverter::class)->setMethods(
            ['convertFailedToDelete']
        )->getMock();

        /** @var  ObjectData|PHPUnit_Framework_MockObject_MockObject $dummyObjectData */
        $dummyFailedToDeleteData = $this->getMock(FailedToDeleteData::class);

        $jsonConverterMock->expects($this->atLeastOnce())->method(
            'convertFailedToDelete'
        )->with($responseData)->willReturn(
            $dummyFailedToDeleteData
        );

        $sessionMock = $this->getSessionMock();

        /** @var ObjectService|PHPUnit_Framework_MockObject_MockObject $objectService */
        $objectService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            [$sessionMock]
        )->setMethods(
            ['getObjectUrl', 'post', 'getJsonConverter']
        )->getMock();

        $objectService->expects($this->atLeastOnce())->method('getObjectUrl')->with(
            $repositoryId,
            $folderId
        )->willReturn(Url::createFromUrl(self::BROWSER_URL_TEST));

        $objectService->expects($this->atLeastOnce())->method('post')->with($expectedUrl)->willReturn($responseMock);
        $objectService->expects($this->atLeastOnce())->method('getJsonConverter')->willReturn($jsonConverterMock);

        $result = $objectService->deleteTree(
            $repositoryId,
            $folderId,
            $allVersions,
            $unfileObjects,
            $continueOnFailure
        );

        $this->assertSame($dummyFailedToDeleteData, $result);
    }

    /**
     * @return array
     */
    public function deleteTreeDataProvider()
    {
        return [
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                    . '?cmisaction=deleteTree&folderId=folderIdValue&allVersions=true'
                    . '&continueOnFailure=false'
                ),
                'repositoryIdValue',
                'folderIdValue'
            ],
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                    . '?cmisaction=deleteTree&folderId=folderIdValue&allVersions=false'
                    . '&continueOnFailure=false'
                ),
                'repositoryIdValue',
                'folderIdValue',
                false
            ],
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                    . '?cmisaction=deleteTree&folderId=folderIdValue&allVersions=true'
                    . '&continueOnFailure=true'
                ),
                'repositoryIdValue',
                'folderIdValue',
                true,
                null,
                true
            ],
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                    . '?cmisaction=deleteTree&folderId=folderIdValue&allVersions=true'
                    . '&continueOnFailure=true&unfileObjects=delete'
                ),
                'repositoryIdValue',
                'folderIdValue',
                true,
                UnfileObject::cast(UnfileObject::DELETE),
                true
            ]
        ];
    }


    /**
     * @dataProvider getRenditionsDataProvider
     * @param string $expectedUrl
     * @param string $repositoryId
     * @param string $objectId
     * @param string $renditionFilter
     * @param integer|null $maxItems
     * @param integer $skipCount
     */
    public function testGetRenditionsCallsReadFunctionWithParameterizedQuery(
        $expectedUrl,
        $repositoryId,
        $objectId,
        $renditionFilter = Constants::RENDITION_NONE,
        $maxItems = null,
        $skipCount = 0
    ) {
        $responseData = ['foo' => 'bar'];
        $responseMock = $this->getMockBuilder(Response::class)->disableOriginalConstructor(
        )->setMethods(['getBody'])->getMock();
        $responseMock->expects($this->once())->method('getBody')->willReturn(json_encode($responseData));

        $dummyRenditionData = new RenditionData();
        $jsonConverterMock = $this->getMockBuilder(JsonConverter::class)->setMethods(
            ['convertRendition']
        )->getMock();
        $jsonConverterMock->expects($this->any())->method('convertRendition')->with($responseData)->willReturn(
            $dummyRenditionData
        );

        $cmisBindingsHelperMock = $this->getMockBuilder(CmisBindingsHelper::class)->setMethods(
            ['getJsonConverter']
        )->getMock();
        $cmisBindingsHelperMock->expects($this->once())->method('getJsonConverter')->willReturn($jsonConverterMock);

        /** @var ObjectService|PHPUnit_Framework_MockObject_MockObject $objectService */
        $objectService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            [$this->getSessionMock(), $cmisBindingsHelperMock]
        )->setMethods(
            ['getObjectUrl', 'read']
        )->getMock();

        $objectService->expects($this->once())->method('getObjectUrl')->with(
            $repositoryId,
            $objectId,
            Constants::SELECTOR_RENDITIONS
        )->willReturn(Url::createFromUrl(self::BROWSER_URL_TEST));
        $objectService->expects($this->once())->method('read')->with($expectedUrl)->willReturn($responseMock);

        $objectService->getRenditions(
            $repositoryId,
            $objectId,
            $renditionFilter,
            $maxItems,
            $skipCount
        );
    }

    /**
     * Data provider for getRenditions
     *
     * @return array
     */
    public function getRenditionsDataProvider()
    {
        return [
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST . '?renditionFilter=cmis:thumbnail&skipCount=0'
                ),
                'repositoryId',
                'objectId',
                'cmis:thumbnail'
            ],
            [
                Url::createFromUrl(
                    self::BROWSER_URL_TEST . '?renditionFilter=cmis:thumbnail&maxItems=99&skipCount=10'
                ),
                'repositoryId',
                'objectId',
                'cmis:thumbnail',
                99,
                10
            ]
        ];
    }
}
