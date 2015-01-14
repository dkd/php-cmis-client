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

use Dkd\PhpCmis\DataObjects\RenditionData;
use Dkd\PhpCmis\Test\Unit\DataProviderCollectionTrait;

class RenditionDataTest extends \PHPUnit_Framework_TestCase
{
    use DataProviderCollectionTrait;

    /**
     * @var RenditionData
     */
    protected $renditionData;

    public function setUp()
    {
        $this->renditionData = new RenditionData();
    }

    public function testSetStreamIdSetsProperty()
    {
        $this->renditionData->setStreamId('stream-id');
        $this->assertAttributeSame('stream-id', 'streamId', $this->renditionData);
    }

    /**
     * @depends testSetStreamIdSetsProperty
     */
    public function testGetStreamIdReturnsPropertyValue()
    {
        $this->renditionData->setStreamId('stream-id');
        $this->assertSame('stream-id', $this->renditionData->getStreamId());
    }

    /**
     * @dataProvider integerCastDataProvider
     * @param integer $expected
     * @param mixed $value
     */
    public function testSetHeightSetsPropertyAsInteger($expected, $value)
    {
        $this->renditionData->setHeight($value);
        $this->assertAttributeSame($expected, 'height', $this->renditionData);
    }

    /**
     * @depends testSetHeightSetsPropertyAsInteger
     */
    public function testGetHeightReturnsPropertyValue()
    {
        $this->renditionData->setHeight(10);
        $this->assertSame(10, $this->renditionData->getHeight());
    }

    /**
     * @dataProvider integerCastDataProvider
     * @param integer $expected
     * @param mixed $value
     */
    public function testSetWidthSetsPropertyAsInteger($expected, $value)
    {
        $this->renditionData->setWidth($value);
        $this->assertAttributeSame($expected, 'width', $this->renditionData);
    }

    /**
     * @depends testSetWidthSetsPropertyAsInteger
     */
    public function testGetWidthReturnsPropertyValue()
    {
        $this->renditionData->setWidth(10);
        $this->assertSame(10, $this->renditionData->getWidth());
    }

    /**
     * @dataProvider integerCastDataProvider
     * @param integer $expected
     * @param mixed $value
     */
    public function testSetLengthSetsPropertyAsInteger($expected, $value)
    {
        $this->renditionData->setLength($value);
        $this->assertAttributeSame($expected, 'length', $this->renditionData);
    }

    /**
     * @depends testSetLengthSetsPropertyAsInteger
     */
    public function testGetLengthReturnsPropertyValue()
    {
        $this->renditionData->setLength(10);
        $this->assertSame(10, $this->renditionData->getLength());
    }

    /**
     * @dataProvider stringCastDataProvider
     * @param string $expected
     * @param mixed $value
     */
    public function testSetMimeTypeSetsPropertyAsString($expected, $value)
    {
        $this->renditionData->setMimeType($value);
        $this->assertAttributeSame($expected, 'mimeType', $this->renditionData);
    }

    /**
     * @depends testSetMimeTypeSetsPropertyAsString
     */
    public function testGetMimeTypeReturnsPropertyValue()
    {
        $this->renditionData->setMimeType('foo');
        $this->assertSame('foo', $this->renditionData->getMimeType());
    }

    /**
     * @dataProvider stringCastDataProvider
     * @param string $expected
     * @param mixed $value
     */
    public function testSetKindSetsPropertyAsString($expected, $value)
    {
        $this->renditionData->setKind($value);
        $this->assertAttributeSame($expected, 'kind', $this->renditionData);
    }

    /**
     * @depends testSetKindSetsPropertyAsString
     */
    public function testGetKindReturnsPropertyValue()
    {
        $this->renditionData->setKind('foo');
        $this->assertSame('foo', $this->renditionData->getKind());
    }

    /**
     * @dataProvider stringCastDataProvider
     * @param string $expected
     * @param mixed $value
     */
    public function testSetTitleSetsPropertyAsString($expected, $value)
    {
        $this->renditionData->setTitle($value);
        $this->assertAttributeSame($expected, 'title', $this->renditionData);
    }

    /**
     * @depends testSetTitleSetsPropertyAsString
     */
    public function testGetTitleReturnsPropertyValue()
    {
        $this->renditionData->setTitle('foo');
        $this->assertSame('foo', $this->renditionData->getTitle());
    }

    /**
     * @dataProvider stringCastDataProvider
     * @param string $expected
     * @param mixed $value
     */
    public function testSetRenditionDocumentIdSetsPropertyAsString($expected, $value)
    {
        $this->renditionData->setRenditionDocumentId($value);
        $this->assertAttributeSame($expected, 'renditionDocumentId', $this->renditionData);
    }

    /**
     * @depends testSetRenditionDocumentIdSetsPropertyAsString
     */
    public function testGetRenditionDocumentIdReturnsPropertyValue()
    {
        $this->renditionData->setRenditionDocumentId('foo');
        $this->assertSame('foo', $this->renditionData->getRenditionDocumentId());
    }
}
