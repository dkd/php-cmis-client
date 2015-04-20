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

use Dkd\PhpCmis\Bindings\Browser\RepositoryService;
use Dkd\PhpCmis\DataObjects\AbstractTypeDefinition;
use Dkd\PhpCmis\DataObjects\ItemTypeDefinition;
use Dkd\PhpCmis\DataObjects\RepositoryInfoBrowserBinding;
use Dkd\PhpCmis\Definitions\TypeDefinitionInterface;
use League\Url\Url;
use PHPUnit_Framework_MockObject_MockObject;

class RepositoryServiceTest extends AbstractBrowserBindingServiceTestCase
{
    const CLASS_TO_TEST = '\\Dkd\\PhpCmis\\Bindings\\Browser\\RepositoryService';

    public function testGetRepositoryInfoReturnsRepositoryInfoObjectForGivenRepositoryId()
    {
        /** @var RepositoryService|PHPUnit_Framework_MockObject_MockObject $repositoryService */
        $repositoryService = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Bindings\\Browser\\RepositoryService'
        )->setMethods(array('getRepositoriesInternal'))->setConstructorArgs(array($this->getSessionMock()))->getMock();

        $repositoryInfo1 = new RepositoryInfoBrowserBinding();
        $repositoryInfo1->setId('repository-id-1');

        $repositoryInfo2 = new RepositoryInfoBrowserBinding();
        $repositoryInfo2->setId('repository-id-2');

        $repositoryService->expects($this->any())->method('getRepositoriesInternal')->willReturn(
            array($repositoryInfo1, $repositoryInfo2)
        );

        $this->assertSame($repositoryInfo2, $repositoryService->getRepositoryInfo('repository-id-2'));

        return $repositoryService;
    }

    public function testGetRepositoryInfoThrowsExceptionIfNoRepositoryInfoCouldBeFetched()
    {
        /** @var RepositoryService|PHPUnit_Framework_MockObject_MockObject $repositoryService */
        $repositoryService = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Bindings\\Browser\\RepositoryService'
        )->setMethods(array('getRepositoriesInternal'))->setConstructorArgs(array($this->getSessionMock()))->getMock();

        $repositoryInfo1 = new RepositoryInfoBrowserBinding();
        $repositoryInfo1->setId('repository-id-1');

        $repositoryService->expects($this->any())->method('getRepositoriesInternal')->willReturn(
            array($repositoryInfo1)
        );

        $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisObjectNotFoundException');
        $repositoryService->getRepositoryInfo('invalid-id');
    }

    public function testGetRepositoryInfosReturnsArrayWithRepositoryInternals()
    {
        /** @var RepositoryService|PHPUnit_Framework_MockObject_MockObject $repositoryService */
        $repositoryService = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Bindings\\Browser\\RepositoryService'
        )->setMethods(array('getRepositoriesInternal'))->setConstructorArgs(array($this->getSessionMock()))->getMock();

        $repositoryInfo1 = new RepositoryInfoBrowserBinding();
        $repositoryInfo1->setId('repository-id-1');

        $repositoryInfo2 = new RepositoryInfoBrowserBinding();
        $repositoryInfo2->setId('repository-id-2');

        $repositoryService->expects($this->any())->method('getRepositoriesInternal')->willReturn(
            array($repositoryInfo1, $repositoryInfo2)
        );

        $this->assertSame(array($repositoryInfo1, $repositoryInfo2), $repositoryService->getRepositoryInfos());
    }

    public function testGetTypeDefinitionReturnsTypeDefinitionObjectForGivenTypeId()
    {
        /** @var RepositoryService|PHPUnit_Framework_MockObject_MockObject $repositoryService */
        $repositoryService = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Bindings\\Browser\\RepositoryService'
        )->setMethods(array('getTypeDefinitionInternal'))->setConstructorArgs(
            array($this->getSessionMock())
        )->getMock();

        $dummyTypeDefinition = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Definitions\\TypeDefinitionInterface'
        )->getMockForAbstractClass();

        $repositoryService->expects($this->any())->method('getTypeDefinitionInternal')->willReturn(
            $dummyTypeDefinition
        );

        $this->assertSame($dummyTypeDefinition, $repositoryService->getTypeDefinition('foo', 'bar'));
    }

    /**
     * @dataProvider createTypeDataProvider
     * @param string $expectedUrl
     * @param array $typeDefinitionArrayRepresentation
     * @param string $repositoryId
     * @param TypeDefinitionInterface $type
     */
    public function testCreateTypeCallsPostFunctionWithParameterizedQuery(
        $expectedUrl,
        array $typeDefinitionArrayRepresentation,
        $repositoryId,
        TypeDefinitionInterface $type
    ) {
        $responseData = array('foo' => 'bar');
        $responseMock = $this->getMockBuilder('\\GuzzleHttp\\Message\\Response')->disableOriginalConstructor(
        )->setMethods(array('json'))->getMock();
        $responseMock->expects($this->any())->method('json')->willReturn($responseData);

        $jsonConverterMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Converter\\JsonConverter')->setMethods(
            array('convertFromTypeDefinition','convertTypeDefinition')
        )->getMock();

        /** @var  AbstractTypeDefinition|PHPUnit_Framework_MockObject_MockObject $dummyTypeDefinition */
        $dummyTypeDefinition = $this->getMockBuilder('\\Dkd\\PhpCmis\\ObjectData\\AbstractTypeDefinition')->getMock();
        $jsonConverterMock->expects($this->atLeastOnce())->method('convertTypeDefinition')->with(
            $responseData
        )->willReturn($dummyTypeDefinition);
        $jsonConverterMock->expects($this->atLeastOnce())->method('convertFromTypeDefinition')->willReturn(
            $typeDefinitionArrayRepresentation
        );

        $cmisBindingsHelperMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBindingsHelper')->setMethods(
            array('getJsonConverter')
        )->getMock();
        $cmisBindingsHelperMock->expects($this->exactly(2))->method(
            'getJsonConverter'
        )->willReturn($jsonConverterMock);

        /** @var RepositoryService|PHPUnit_Framework_MockObject_MockObject $repositoryService */
        $repositoryService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            array($this->getSessionMock(), $cmisBindingsHelperMock)
        )->setMethods(
            array('getRepositoryUrl', 'post')
        )->getMock();

        $repositoryService->expects($this->atLeastOnce())->method('getRepositoryUrl')->with(
            $repositoryId
        )->willReturn(Url::createFromUrl(self::BROWSER_URL_TEST));
        $repositoryService->expects($this->atLeastOnce())->method('post')->with(
            $expectedUrl
        )->willReturn($responseMock);

        $repositoryService->createType(
            $repositoryId,
            $type
        );
    }

    /**
     * Data provider for updateType
     *
     * @return array
     */
    public function createTypeDataProvider()
    {
        $typeDefinitionArrayRepresentation = array('foo' => 'bar', 'baz' => 'bazz');

        return array(
            array(
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                    . '?cmisaction=createType&type[foo]=bar&type[baz]=bazz'
                ),
                $typeDefinitionArrayRepresentation,
                'repositoryId',
                new ItemTypeDefinition('typeId'),
            )
        );
    }


    /**
     * @dataProvider deleteTypeDataProvider
     * @param string $expectedUrl
     * @param string $repositoryId
     * @param string $typeId
     */
    public function testDeleteTypeCallsPostFunctionWithParameterizedQuery(
        $expectedUrl,
        $repositoryId,
        $typeId
    ) {
        $responseMock = $this->getMockBuilder('\\GuzzleHttp\\Message\\Response')->disableOriginalConstructor(
        )->getMock();

        $cmisBindingsHelperMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBindingsHelper')->getMock();

        /** @var RepositoryService|PHPUnit_Framework_MockObject_MockObject $repositoryService */
        $repositoryService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            array($this->getSessionMock(), $cmisBindingsHelperMock)
        )->setMethods(
            array('getRepositoryUrl', 'post')
        )->getMock();

        $repositoryService->expects($this->atLeastOnce())->method('getRepositoryUrl')->with(
            $repositoryId
        )->willReturn(Url::createFromUrl(self::BROWSER_URL_TEST));
        $repositoryService->expects($this->atLeastOnce())->method('post')->with(
            $expectedUrl
        )->willReturn($responseMock);

        $repositoryService->deleteType(
            $repositoryId,
            $typeId
        );
    }

    /**
     * Data provider for deleteType
     *
     * @return array
     */
    public function deleteTypeDataProvider()
    {
        return array(
            array(
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                    . '?cmisaction=deleteType&typeId=typeId'
                ),
                'repositoryId',
                'typeId',
            )
        );
    }

    /**
     * @dataProvider updateTypeDataProvider
     * @param string $expectedUrl
     * @param array $typeDefinitionArrayRepresentation
     * @param string $repositoryId
     * @param TypeDefinitionInterface $type
     */
    public function testUpdateTypeCallsPostFunctionWithParameterizedQuery(
        $expectedUrl,
        array $typeDefinitionArrayRepresentation,
        $repositoryId,
        TypeDefinitionInterface $type
    ) {
        $responseData = array('foo' => 'bar');
        $responseMock = $this->getMockBuilder('\\GuzzleHttp\\Message\\Response')->disableOriginalConstructor(
        )->setMethods(array('json'))->getMock();
        $responseMock->expects($this->any())->method('json')->willReturn($responseData);

        $jsonConverterMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Converter\\JsonConverter')->setMethods(
            array('convertFromTypeDefinition','convertTypeDefinition')
        )->getMock();

        /** @var  AbstractTypeDefinition|PHPUnit_Framework_MockObject_MockObject $dummyTypeDefinition */
        $dummyTypeDefinition = $this->getMockBuilder('\\Dkd\\PhpCmis\\ObjectData\\AbstractTypeDefinition')->getMock();
        $jsonConverterMock->expects($this->atLeastOnce())->method('convertTypeDefinition')->with(
            $responseData
        )->willReturn($dummyTypeDefinition);
        $jsonConverterMock->expects($this->atLeastOnce())->method('convertFromTypeDefinition')->willReturn(
            $typeDefinitionArrayRepresentation
        );

        $cmisBindingsHelperMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Bindings\\CmisBindingsHelper')->setMethods(
            array('getJsonConverter')
        )->getMock();
        $cmisBindingsHelperMock->expects($this->exactly(2))->method(
            'getJsonConverter'
        )->willReturn($jsonConverterMock);

        /** @var RepositoryService|PHPUnit_Framework_MockObject_MockObject $repositoryService */
        $repositoryService = $this->getMockBuilder(self::CLASS_TO_TEST)->setConstructorArgs(
            array($this->getSessionMock(), $cmisBindingsHelperMock)
        )->setMethods(
            array('getRepositoryUrl', 'post')
        )->getMock();

        $repositoryService->expects($this->atLeastOnce())->method('getRepositoryUrl')->with(
            $repositoryId
        )->willReturn(Url::createFromUrl(self::BROWSER_URL_TEST));
        $repositoryService->expects($this->atLeastOnce())->method('post')->with(
            $expectedUrl
        )->willReturn($responseMock);

        $repositoryService->updateType(
            $repositoryId,
            $type
        );
    }

    /**
     * Data provider for updateType
     *
     * @return array
     */
    public function updateTypeDataProvider()
    {
        $typeDefinitionArrayRepresentation = array('foo' => 'bar');
        $typeDefinitionJsonRepresentation = '{"foo":"bar"}';

        return array(
            array(
                Url::createFromUrl(
                    self::BROWSER_URL_TEST
                    . '?cmisaction=updateType&type=' . $typeDefinitionJsonRepresentation
                ),
                $typeDefinitionArrayRepresentation,
                'repositoryId',
                new ItemTypeDefinition('typeId'),
            )
        );
    }
}
