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

use Dkd\PhpCmis\DataObjects\PropertyDateTime;
use Dkd\PhpCmis\Test\Unit\DataProviderCollectionTrait;

class PropertyDateTimeTest extends \PHPUnit_Framework_TestCase
{
    use DataProviderCollectionTrait;

    /**
     * @var PropertyDateTime
     */
    protected $propertyDateTime;

    public function setUp()
    {
        $this->propertyDateTime = new PropertyDateTime();
    }

    public function testSetValuesSetsProperty()
    {
        $values = array(new \DateTime());
        $this->propertyDateTime->setValues($values);
        $this->assertAttributeSame($values, 'values', $this->propertyDateTime);
    }

    public function testSetValuesThrowsExceptionIfInvalidValuesGiven()
    {
        $this->setExpectedException('\\InvalidArgumentException', null, 1413440336);
        $this->propertyDateTime->setValues(array('now'));
    }

    public function testSetValueSetsValuesProperty()
    {
        $date = new \DateTime();
        $this->propertyDateTime->setValue($date);
        $this->assertAttributeSame(array($date), 'values', $this->propertyDateTime);
    }

    public function testSetValueThrowsExceptionIfInvalidValueGiven()
    {
        $this->setExpectedException('\\InvalidArgumentException', null, 1413440336);
        $this->propertyDateTime->setValue('now');
    }
}
