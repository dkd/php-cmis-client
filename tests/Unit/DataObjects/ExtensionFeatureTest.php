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

use Dkd\PhpCmis\DataObjects\ExtensionFeature;
use Dkd\PhpCmis\Test\Unit\DataProviderCollectionTrait;

class ExtensionFeatureTest extends \PHPUnit_Framework_TestCase
{
    use DataProviderCollectionTrait;

    /**
     * @var ExtensionFeature
     */
    protected $extensionFeature;

    public function setUp()
    {
        $this->extensionFeature = new ExtensionFeature();
    }

    /**
     * @dataProvider stringCastDataProvider
     * @param $value
     * @param $expected
     */
    public function testSetCommonNameSetsProperty($expected, $value)
    {
        $this->extensionFeature->setCommonName($value);
        $this->assertAttributeSame($expected, 'commonName', $this->extensionFeature);
    }

    /**
     * @depends testSetCommonNameSetsProperty
     */
    public function testGetCommonNameReturnsPropertyValue()
    {
        $this->extensionFeature->setCommonName('string');
        $this->assertSame('string', $this->extensionFeature->getCommonName());
    }

    /**
     * @dataProvider stringCastDataProvider
     * @param $value
     * @param $expected
     */
    public function testSetDescriptionSetsProperty($expected, $value)
    {
        $this->extensionFeature->setDescription($value);
        $this->assertAttributeSame($expected, 'description', $this->extensionFeature);
    }

    /**
     * @depends testSetDescriptionSetsProperty
     */
    public function testGetDescriptionReturnsPropertyValue()
    {
        $this->extensionFeature->setDescription('string');
        $this->assertSame('string', $this->extensionFeature->getDescription());
    }

    /**
     * @dataProvider stringCastDataProvider
     * @param $value
     * @param $expected
     */
    public function testSetIdSetsProperty($expected, $value)
    {
        $this->extensionFeature->setId($value);
        $this->assertAttributeSame($expected, 'id', $this->extensionFeature);
    }

    /**
     * @depends testSetIdSetsProperty
     */
    public function testGetIdReturnsPropertyValue()
    {
        $this->extensionFeature->setId('string');
        $this->assertSame('string', $this->extensionFeature->getId());
    }

    /**
     * @dataProvider stringCastDataProvider
     * @param $value
     * @param $expected
     */
    public function testSetUrlSetsProperty($expected, $value)
    {
        $this->extensionFeature->setUrl($value);
        $this->assertAttributeSame($expected, 'url', $this->extensionFeature);
    }

    /**
     * @depends testSetUrlSetsProperty
     */
    public function testGetUrlReturnsPropertyValue()
    {
        $this->extensionFeature->setUrl('string');
        $this->assertSame('string', $this->extensionFeature->getUrl());
    }

    /**
     * @dataProvider stringCastDataProvider
     * @param $value
     * @param $expected
     */
    public function testSetVersionLabelSetsProperty($expected, $value)
    {
        $this->extensionFeature->setVersionLabel($value);
        $this->assertAttributeSame($expected, 'versionLabel', $this->extensionFeature);
    }

    /**
     * @depends testSetVersionLabelSetsProperty
     */
    public function testGetVersionLabelReturnsPropertyValue()
    {
        $this->extensionFeature->setVersionLabel('string');
        $this->assertSame('string', $this->extensionFeature->getVersionLabel());
    }

    public function testSetFeatureDataSetsProperty()
    {
        $this->extensionFeature->setFeatureData(array(1, true, 'string'));
        $this->assertAttributeSame(array('1', '1', 'string'), 'featureData', $this->extensionFeature);
    }

    /**
     * @depends testSetVersionLabelSetsProperty
     */
    public function testGetFeatureDataReturnsPropertyValue()
    {
        $this->extensionFeature->setFeatureData(array('string'));
        $this->assertSame(array('string'), $this->extensionFeature->getFeatureData());
    }
}
