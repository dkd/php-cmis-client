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

use Dkd\PhpCmis\DataObjects\NewTypeSettableAttributes;

class NewTypeSettableAttributesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NewTypeSettableAttributes
     */
    protected $newTypeSettableAttributes;

    public function setUp()
    {
        $this->newTypeSettableAttributes = new NewTypeSettableAttributes();
    }

    /**
     * DataProvider for all properties with a valid value and an invalid value
     *
     * @return array
     */
    public function propertiesOfSutDataProvider()
    {
        return array(
            array(
                'propertyName' => 'canSetControllableAcl'
            ),
            array(
                'propertyName' => 'canSetControllablePolicy'
            ),
            array(
                'propertyName' => 'canSetCreatable'
            ),
            array(
                'propertyName' => 'canSetDescription'
            ),
            array(
                'propertyName' => 'canSetDisplayName'
            ),
            array(
                'propertyName' => 'canSetFileable'
            ),
            array(
                'propertyName' => 'canSetFulltextIndexed'
            ),
            array(
                'propertyName' => 'canSetId'
            ),
            array(
                'propertyName' => 'canSetIncludedInSupertypeQuery'
            ),
            array(
                'propertyName' => 'canSetLocalName'
            ),
            array(
                'propertyName' => 'canSetLocalNamespace'
            ),
            array(
                'propertyName' => 'canSetQueryable'
            ),
            array(
                'propertyName' => 'canSetQueryName'
            )
        );
    }

    /**
     * Test setter for a property
     *
     * @dataProvider propertiesOfSutDataProvider
     * @param string $propertyName Name of the class property
     */
    public function testPropertySetterSetsProperty($propertyName)
    {
        $setterName = 'set' . ucfirst($propertyName);
        $this->newTypeSettableAttributes->$setterName(true);
        $this->assertAttributeSame(true, $propertyName, $this->newTypeSettableAttributes);
        $this->newTypeSettableAttributes->$setterName(false);
        $this->assertAttributeSame(false, $propertyName, $this->newTypeSettableAttributes);
    }

    /**
     * Test setter for a property - should cast value to expected type
     *
     * @dataProvider propertiesOfSutDataProvider
     * @param string $propertyName Name of the class property
     */
    public function testPropertySetterCastsValueToBoolean($propertyName)
    {
        $setterName = 'set' . ucfirst($propertyName);
        try {
            $this->newTypeSettableAttributes->$setterName(1);
        } catch (\PHPUnit_Framework_Error_Notice $exception) {
        }
        $this->assertAttributeInternalType('boolean', $propertyName, $this->newTypeSettableAttributes);
    }

    /**
     * Test getter for a property
     *
     * @dataProvider propertiesOfSutDataProvider
     * @param string $propertyName Name of the class property
     */
    public function testPropertyGetterReturnsPropertyValue($propertyName)
    {
        $setterName = 'set' . ucfirst($propertyName);
        $getterName = $propertyName;
        $this->setDependencies(array('testSetPropertySetsProperty'));
        $this->newTypeSettableAttributes->$setterName(true);
        $this->assertSame(true, $this->newTypeSettableAttributes->$getterName());
    }
}
