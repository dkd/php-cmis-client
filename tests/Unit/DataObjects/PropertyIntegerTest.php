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

use Dkd\PhpCmis\DataObjects\PropertyInteger;
use Dkd\PhpCmis\Test\Unit\DataProviderCollectionTrait;

class PropertyIntegerTest extends \PHPUnit_Framework_TestCase
{
    use DataProviderCollectionTrait;

    /**
     * @var PropertyInteger
     */
    protected $propertyInteger;

    public function setUp()
    {
        $this->propertyInteger = new PropertyInteger('testId');
    }

    /**
     * @dataProvider integerCastDataProvider
     * @param integer $expected
     * @param mixed $value
     */
    public function testSetValuesSetsProperty($expected, $value)
    {
        if ($value === null) {
            $expected = $value;
        }

        if (!is_integer($value) && $value !== null && !(PHP_INT_SIZE == 4 && is_double($value))) {
            $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException', '', 1413440336);
        }

        $this->propertyInteger->setValues(array($value));
        $this->assertAttributeSame(array($expected), 'values', $this->propertyInteger);
    }

    /**
     * @dataProvider integerCastDataProvider
     * @param integer $expected
     * @param mixed $value
     */
    public function testSetValueSetsValuesProperty($expected, $value)
    {
        if ($value === null) {
            $expected = $value;
        }

        if (!is_integer($value) && $value !== null && !(PHP_INT_SIZE == 4 && is_double($value))) {
            $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException', '', 1413440336);
        }

        $this->propertyInteger->setValue($value);
        $this->assertAttributeSame(array($expected), 'values', $this->propertyInteger);
    }
}
