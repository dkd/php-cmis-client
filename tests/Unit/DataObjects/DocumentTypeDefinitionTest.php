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

use Dkd\PhpCmis\DataObjects\DocumentTypeDefinition;
use Dkd\PhpCmis\DataObjects\RelationshipTypeDefinition;
use Dkd\PhpCmis\Enum\ContentStreamAllowed;
use Dkd\PhpCmis\Test\Unit\DataProviderCollectionTrait;

class DocumentTypeDefinitionTest extends \PHPUnit_Framework_TestCase
{
    use DataProviderCollectionTrait;

    /**
     * @var DocumentTypeDefinition
     */
    protected $documentTypeDefinition;

    public function setUp()
    {
        $this->documentTypeDefinition = new DocumentTypeDefinition('typeId');
    }

    public function testInitializeMethodThrowsExceptionIfInvalidTypeGiven()
    {
        $relationshipTypeDefinition = new RelationshipTypeDefinition('typeId');
        $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException');
        $this->documentTypeDefinition->initialize($relationshipTypeDefinition);
    }

    /**
     * @covers \Dkd\PhpCmis\DataObjects\DocumentTypeDefinition::initialize
     */
    public function testInitializeMethodCopiesPropertyValuesFromGivenTypeDefinition()
    {
        $dummyTypeDefinition = new DocumentTypeDefinition('typeId');
        $dummyTypeDefinition->setIsVersionable(true);
        $dummyTypeDefinition->setContentStreamAllowed(ContentStreamAllowed::cast(ContentStreamAllowed::ALLOWED));

        $this->documentTypeDefinition->initialize($dummyTypeDefinition);

        $this->assertEquals($dummyTypeDefinition, $this->documentTypeDefinition);
    }

    public function testDefaultValueForContentStreamAllowedIsSet()
    {
        $this->assertAttributeEquals(
            ContentStreamAllowed::cast(ContentStreamAllowed::NOTALLOWED),
            'contentStreamAllowed',
            $this->documentTypeDefinition
        );
    }

    public function testSetIsVersionableSetsProperty()
    {
        $this->documentTypeDefinition->setIsVersionable(true);
        $this->assertAttributeSame(true, 'isVersionable', $this->documentTypeDefinition);
        $this->documentTypeDefinition->setIsVersionable(false);
        $this->assertAttributeSame(false, 'isVersionable', $this->documentTypeDefinition);
    }

    /**
     * @depends testSetIsVersionableSetsProperty
     */
    public function testIsVersionableReturnsPropertyValue()
    {
        $this->documentTypeDefinition->setIsVersionable(true);
        $this->assertTrue($this->documentTypeDefinition->isVersionable());
        $this->documentTypeDefinition->setIsVersionable(false);
        $this->assertFalse($this->documentTypeDefinition->isVersionable());
    }

    public function testSetContentStreamAllowedSetsProperty()
    {
        $contentStreamAllowed = ContentStreamAllowed::cast(ContentStreamAllowed::ALLOWED);
        $this->documentTypeDefinition->setContentStreamAllowed($contentStreamAllowed);
        $this->assertAttributeSame($contentStreamAllowed, 'contentStreamAllowed', $this->documentTypeDefinition);
    }

    /**
     * @depends testDefaultValueForContentStreamAllowedIsSet
     */
    public function testGetContentStreamAllowedGetsPropertyValue()
    {
        $this->assertInstanceOf(
            '\\Dkd\\PhpCmis\\Enum\\ContentStreamAllowed',
            $this->documentTypeDefinition->getContentStreamAllowed()
        );
    }
}
