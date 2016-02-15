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

use Dkd\PhpCmis\DataObjects\PropertyDecimal;
use Dkd\PhpCmis\Test\Unit\DataProviderCollectionTrait;

class PropertyDecimalTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PropertyDecimal
     */
    protected $propertyDecimal;

    public function setUp()
    {
        $this->propertyDecimal = new PropertyDecimal('testId');
    }

    public function testSetValuesSetsProperty()
    {
        $values = array(2.3, 5.0, null);
        $this->propertyDecimal->setValues($values);
        $this->assertAttributeSame($values, 'values', $this->propertyDecimal);
    }

    public function testSetValuesCastsIntegersSilentlyToDoublesAndSetsProperty()
    {
        $values = array(2, 5);
        $this->propertyDecimal->setValues($values);
        $this->assertAttributeSame(array(2.0, 5.0), 'values', $this->propertyDecimal);
    }

    public function testSetValuesThrowsExceptionIfInvalidValuesGiven()
    {
        $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException', '', 1413440336);
        $this->propertyDecimal->setValues(array(''));
    }

    public function testSetValueSetsValuesProperty()
    {
        $this->propertyDecimal->setValue(2.2);
        $this->assertAttributeSame(array(2.2), 'values', $this->propertyDecimal);
    }

    public function testSetValueThrowsExceptionIfInvalidValueGiven()
    {
        $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException', '', 1413440336);
        $this->propertyDecimal->setValue(array(''));
    }
}
