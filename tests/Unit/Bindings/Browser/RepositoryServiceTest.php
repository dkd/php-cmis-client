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
use Dkd\PhpCmis\DataObjects\RepositoryInfo;
use Dkd\PhpCmis\DataObjects\RepositoryInfoBrowserBinding;
use PHPUnit_Framework_MockObject_MockObject;

class RepositoryServiceTest extends AbstractBrowserBindingServiceTestCase
{
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
}
