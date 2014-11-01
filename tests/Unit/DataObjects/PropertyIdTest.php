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

use Dkd\PhpCmis\DataObjects\PropertyId;
use Dkd\PhpCmis\Test\Unit\DataProviderCollectionTrait;

class PropertyIdTest extends \PHPUnit_Framework_TestCase
{
    use DataProviderCollectionTrait;

    /**
     * @var PropertyId
     */
    protected $propertyId;

    public function setUp()
    {
        $this->propertyId = new PropertyId();
    }

    public function testSetValuesSetsProperty()
    {
        $values = array('foo', 'bar');
        $this->propertyId->setValues($values);
        $this->assertAttributeSame($values, 'values', $this->propertyId);
    }

    /**
     * @dataProvider StringCastDataProvider
     */
    public function testSetValuesThrowsExceptionIfInvalidValuesGiven($expected, $value)
    {
        // use all non string values
        if (!is_string($value)) {
            $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException', null, 1413440336);
            $this->propertyId->setValues(array($value));
        }
    }

    public function testSetValueSetsValuesProperty()
    {
        $this->propertyId->setValue('foo');
        $this->assertAttributeSame(array('foo'), 'values', $this->propertyId);
    }

    /**
     * @dataProvider StringCastDataProvider
     */
    public function testSetValueThrowsExceptionIfInvalidValueGiven($expected, $value)
    {
        // use all non string values
        if (!is_string($value)) {
            $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException', null, 1413440336);
            $this->propertyId->setValue(array($value));
        }
    }
}
