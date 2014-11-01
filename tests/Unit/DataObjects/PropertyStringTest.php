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

use Dkd\PhpCmis\DataObjects\PropertyString;
use Dkd\PhpCmis\Test\Unit\DataProviderCollectionTrait;

class PropertyStringTest extends \PHPUnit_Framework_TestCase
{
    use DataProviderCollectionTrait;

    /**
     * @var PropertyString
     */
    protected $propertyString;

    public function setUp()
    {
        $this->propertyString = new PropertyString();
    }

    public function testSetValuesSetsProperty()
    {
        $values = array('foo', 'bar');
        $this->propertyString->setValues($values);
        $this->assertAttributeSame($values, 'values', $this->propertyString);
    }

    /**
     * @dataProvider StringCastDataProvider
     */
    public function testSetValuesThrowsExceptionIfInvalidValuesGiven($expected, $value)
    {
        // use all non string values
        if (!is_string($value)) {
            $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException', null, 1413440336);
            $this->propertyString->setValues(array($value));
        }
    }

    public function testSetValueSetsValuesProperty()
    {
        $this->propertyString->setValue('foo');
        $this->assertAttributeSame(array('foo'), 'values', $this->propertyString);
    }

    /**
     * @dataProvider StringCastDataProvider
     */
    public function testSetValueThrowsExceptionIfInvalidValueGiven($expected, $value)
    {
        // use all non string values
        if (!is_string($value)) {
            $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException', null, 1413440336);
            $this->propertyString->setValue(array($value));
        }
    }
}
