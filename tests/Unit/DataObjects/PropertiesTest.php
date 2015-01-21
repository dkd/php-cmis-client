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

use Dkd\PhpCmis\DataObjects\Properties;
use Dkd\PhpCmis\DataObjects\PropertyString;

class PropertiesTest extends \PHPUnit_Framework_TestCase
{
    public function testAddPropertyAddsProperty()
    {
        $properties = new Properties();
        $stringProperty = new PropertyString('stringProp', 'stringPropValue');

        $properties->addProperty($stringProperty);

        $this->assertAttributeSame(array($stringProperty->getId() => $stringProperty), 'properties', $properties);
    }

    /**
     * @depends testAddPropertyAddsProperty
     */
    public function testAddPropertyReplacesPropertyWithEqualId()
    {
        $properties = new Properties();
        $stringProperty = new PropertyString('stringProp', 'stringPropValue');

        $properties->addProperty($stringProperty);

        $stringProperty2 = new PropertyString('stringProp', 'stringPropValue2');

        $properties->addProperty($stringProperty2);

        $this->assertAttributeSame(array($stringProperty2->getId() => $stringProperty2), 'properties', $properties);
    }

    /**
     * @depends testAddPropertyAddsProperty
     */
    public function testGetPropertiesReturnsAttributeValue()
    {
        $properties = new Properties();
        $stringProperty = new PropertyString('stringProp', 'stringPropValue');

        $properties->addProperty($stringProperty);

        $this->assertSame(array($stringProperty->getId() => $stringProperty), $properties->getProperties());
    }

    /**
     * @depends testAddPropertyAddsProperty
     */
    public function testRemovePropertyRemovedPropertyWithGivenId()
    {

        $properties = new Properties();
        $stringProperty = new PropertyString('stringProp', 'stringPropValue');
        $properties->addProperty($stringProperty);

        $stringProperty2 = new PropertyString('stringProp2', 'stringPropValue2');
        $properties->addProperty($stringProperty2);

        $properties->removeProperty('stringProp');

        $this->assertAttributeSame(array($stringProperty2->getId() => $stringProperty2), 'properties', $properties);
    }

    /**
     * @depends testAddPropertyAddsProperty
     */
    public function testAddPropertiesAddsAListOfProperties()
    {
        $properties = new Properties();

        $stringProperty = new PropertyString('stringProp', 'stringPropValue');
        $properties->addProperty($stringProperty);

        $stringProperty2 = new PropertyString('stringProp2', 'stringPropValue2');

        $stringProperty3 = new PropertyString('stringProp3', 'stringPropValue3');
        $properties->addProperties(array($stringProperty2, $stringProperty3));

        $this->assertAttributeSame(
            array(
                $stringProperty->getId() => $stringProperty,
                $stringProperty2->getId() => $stringProperty2,
                $stringProperty3->getId() => $stringProperty3
            ),
            'properties',
            $properties
        );
    }
}
