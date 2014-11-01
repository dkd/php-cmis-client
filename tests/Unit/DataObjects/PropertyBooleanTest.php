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

use Dkd\PhpCmis\DataObjects\PropertyBoolean;
use Dkd\PhpCmis\Test\Unit\DataProviderCollectionTrait;

class PropertyBooleanTest extends \PHPUnit_Framework_TestCase
{
    use DataProviderCollectionTrait;

    /**
     * @var PropertyBoolean
     */
    protected $propertyBoolean;

    public function setUp()
    {
        $this->propertyBoolean = new PropertyBoolean();
    }

    public function testSetValuesSetsProperty()
    {
        $values = array(true, false);
        $this->propertyBoolean->setValues($values);
        $this->assertAttributeSame($values, 'values', $this->propertyBoolean);
    }

    /**
     * @dataProvider booleanCastDataProvider
     */
    public function testSetValuesThrowsExceptionIfInvalidValuesGiven($expected, $value)
    {
        // use all non boolean values
        if (!is_bool($value)) {
            $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException', null, 1413440336);
            $this->propertyBoolean->setValues(array($value));
        }
    }

    public function testSetValueSetsValuesProperty()
    {
        $this->propertyBoolean->setValue(true);
        $this->assertAttributeSame(array(true), 'values', $this->propertyBoolean);
    }

    /**
     * @dataProvider booleanCastDataProvider
     */
    public function testSetValueThrowsExceptionIfInvalidValueGiven($expected, $value)
    {
        // use all non boolean values
        if (!is_bool($value)) {
            $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException', null, 1413440336);
            $this->propertyBoolean->setValue(array($value));
        }
    }
}
