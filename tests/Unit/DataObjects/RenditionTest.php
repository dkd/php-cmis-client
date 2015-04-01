<?php
namespace Dkd\PhpCmis\Test\Unit;

use Dkd\PhpCmis\Data\RepositoryInfoInterface;
use Dkd\PhpCmis\DataObjects\ObjectId;
use Dkd\PhpCmis\DataObjects\Rendition;
use Dkd\PhpCmis\SessionInterface;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Dimitri Ebert <dimitri.ebert@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class RenditionTest extends \PHPUnit_Framework_TestCase
{
    use ReflectionHelperTrait;

    /**
     * @var SessionInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $sessionMock;

    public function setUp()
    {
        $this->sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->getMockForAbstractClass();
    }

    public function testConstructorSetsSessionFromGivenSessionParameter()
    {
        $rendition = new Rendition($this->sessionMock, 'objectId');
        $this->assertAttributeSame($this->sessionMock, 'session', $rendition);
    }

    public function testConstructorSetsObjectIdFromGivenObjectId()
    {
        $objectId = 'fooObjectId';
        $rendition = new Rendition($this->sessionMock, $objectId);
        $this->assertAttributeSame($objectId, 'objectId', $rendition);
    }

    public function testGetHeightReturnsHeight()
    {
        $height = 123;
        $rendition = new Rendition($this->sessionMock, 'objectId');
        $rendition->setHeight($height);
        $this->assertSame($height, $rendition->getHeight());
    }

    public function testGetHeightReturnsMinus1IfNoAvailable()
    {
        $rendition = new Rendition($this->sessionMock, 'objectId');
        $this->assertSame(-1, $rendition->getHeight());
    }

    public function testGetLengthReturnsLength()
    {
        $length = 124;
        $rendition = new Rendition($this->sessionMock, 'objectId');
        $rendition->setLength($length);
        $this->assertSame($length, $rendition->getLength());
    }

    public function testGetLengthReturnsMinus1IfNoAvailable()
    {
        $rendition = new Rendition($this->sessionMock, 'objectId');
        $this->assertSame(-1, $rendition->getLength());
    }

    public function testGetWidthReturnsWidth()
    {
        $width = 125;
        $rendition = new Rendition($this->sessionMock, 'objectId');
        $rendition->setWidth($width);
        $this->assertSame($width, $rendition->getWidth());
    }

    public function testGetWidthReturnsMinus1IfNoAvailable()
    {
        $rendition = new Rendition($this->sessionMock, 'objectId');
        $this->assertSame(-1, $rendition->getWidth());
    }

    public function testGetRenditionDocumentReturnsDocument()
    {
        $renditionDocumentId = 'foo';
        $documentMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Data\\DocumentInterface')->getMockForAbstractClass();

        $this->sessionMock->expects($this->once())->method('createObjectId')->with(
            $renditionDocumentId
        )->willReturn(new ObjectId('foo'));

        $this->sessionMock->expects($this->once())->method('getObject')->with(
            $renditionDocumentId
        )->willReturn($documentMock);

        $rendition = new Rendition($this->sessionMock, 'objectId');
        $rendition->setRenditionDocumentId($renditionDocumentId);

        $this->assertSame($documentMock, $rendition->getRenditionDocument());
    }

    public function testGetContentStreamReturnsStream()
    {
        $streamId = 'bar';
        $objectId = 'foo';

        /** @var  RepositoryInfoInterface|PHPUnit_Framework_MockObject_MockObject $repositoryInfoMock */
        $repositoryInfoMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Data\\RepositoryInfoInterface'
        )->setMethods(array('getId'))->getMockForAbstractClass();
        $repositoryInfoMock->expects($this->any())->method('getId')->willReturn('repositoryId');

        $streamMock = $this->getMockBuilder('\\GuzzleHttp\\Stream\\StreamInterface')->getMockForAbstractClass();

        $objectServiceMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Bindings\\Browser\\ObjectService'
        )->setMethods(array('getContentStream'))->disableOriginalConstructor()->getMockForAbstractClass();

        $objectServiceMock->expects($this->once())->method('getContentStream')->with(
            $repositoryInfoMock->getId(),
            $objectId,
            $streamId
        )->willReturn($streamMock);

        $bindingMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Bindings\\CmisBindingInterface'
        )->setMethods(array('getObjectService'))->disableOriginalConstructor()->getMockForAbstractClass();

        $bindingMock->expects($this->once())->method('getObjectService')->willReturn($objectServiceMock);


        $this->sessionMock->expects($this->once())->method('getBinding')->willReturn($bindingMock);
        $this->sessionMock->expects($this->once())->method('getRepositoryInfo')->willReturn($repositoryInfoMock);

        $rendition = new Rendition($this->sessionMock, $objectId);
        $rendition->setStreamId($streamId);
        $this->assertSame($streamMock, $rendition->getContentStream());
    }
}
