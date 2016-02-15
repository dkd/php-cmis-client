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
        $this->propertyBoolean = new PropertyBoolean('testId');
    }

    /**
     * @dataProvider booleanCastDataProvider
     * @param boolean $expected
     * @param mixed $value
     */
    public function testSetValuesSetsProperty($expected, $value)
    {
        if ($value === null) {
            $expected = $value;
        }
        if (!is_bool($value) && $value !== null) {
            $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException', '', 1413440336);
        }
        $values = array(true, $value);
        $this->propertyBoolean->setValues($values);
        $this->assertAttributeSame(array(true, $expected), 'values', $this->propertyBoolean);
    }

    /**
     * @dataProvider booleanCastDataProvider
     * @param boolean $expected
     * @param mixed $value
     */
    public function testSetValueSetsValuesProperty($expected, $value)
    {
        if ($value === null) {
            $expected = $value;
        }
        if (!is_bool($value) && $value !== null) {
            $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException', '', 1413440336);
        }
        $this->propertyBoolean->setValue($value);
        $this->assertAttributeSame(array($expected), 'values', $this->propertyBoolean);
    }
}
