<?php
namespace Dkd\PhpCmis\Test\Unit\DataObjects;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\DataObjects\DocumentType;
use Dkd\PhpCmis\Definitions\DocumentTypeDefinitionInterface;
use Dkd\PhpCmis\Enum\ContentStreamAllowed;
use PHPUnit_Framework_MockObject_MockObject;

class DocumentTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorSetsSession()
    {
        /**
         * @var \Dkd\PhpCmis\SessionInterface|PHPUnit_Framework_MockObject_MockObject $sessionMock
         */
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->getMockForAbstractClass();

        /**
         * @var DocumentTypeDefinitionInterface|PHPUnit_Framework_MockObject_MockObject $documentTypeDefinition
         */
        $documentTypeDefinition = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Definitions\\DocumentTypeDefinitionInterface'
        )->getMockForAbstractClass();
        $documentTypeDefinition->expects($this->any())->method('getId')->willReturn('typeId');
        $documentTypeDefinition->expects($this->any())->method('getContentStreamAllowed')->willReturn(
            ContentStreamAllowed::cast(ContentStreamAllowed::ALLOWED)
        );
        $documentTypeDefinition->expects($this->any())->method('getPropertyDefinitions')->willReturn(array());
        $documentTypeDefinition->expects($this->any())->method('isVersionable')->willReturn(false);
        $documentType = new DocumentType($sessionMock, $documentTypeDefinition);

        $this->assertAttributeSame($sessionMock, 'session', $documentType);
    }

    public function testConstructorCallsInitializeMethod()
    {
        /**
         * @var \Dkd\PhpCmis\SessionInterface|PHPUnit_Framework_MockObject_MockObject $sessionMock
         */
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->getMockForAbstractClass();

        /**
         * @var DocumentTypeDefinitionInterface|PHPUnit_Framework_MockObject_MockObject $documentTypeDefinition
         */
        $documentTypeDefinition = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Definitions\\MutableDocumentTypeDefinitionInterface'
        )->getMockForAbstractClass();
        $documentTypeDefinition->expects($this->any())->method('getId')->willReturn('typeId');

        /**
         * @var DocumentType|PHPUnit_Framework_MockObject_MockObject $documentType
         */
        $documentType = $this->getMockBuilder('\\Dkd\\PhpCmis\\DataObjects\\DocumentType')->setMethods(
            array('initialize')
        )->disableOriginalConstructor()->getMock();
        $documentType->expects($this->once())->method('initialize')->with(
            $documentTypeDefinition
        );
        $documentType->__construct($sessionMock, $documentTypeDefinition);
    }
}
