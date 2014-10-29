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

use Dkd\PhpCmis\DataObjects\PropertyHtml;
use Dkd\PhpCmis\Test\Unit\DataProviderCollectionTrait;

class PropertyHtmlTest extends \PHPUnit_Framework_TestCase
{
    use DataProviderCollectionTrait;

    /**
     * @var PropertyHtml
     */
    protected $propertyHtml;

    public function setUp()
    {
        $this->propertyHtml = new PropertyHtml();
    }

    public function testSetValuesSetsProperty()
    {
        $values = array('foo', 'bar');
        $this->propertyHtml->setValues($values);
        $this->assertAttributeSame($values, 'values', $this->propertyHtml);
    }

    /**
     * @dataProvider StringCastDataProvider
     */
    public function testSetValuesThrowsExceptionIfInvalidValuesGiven($expected, $value)
    {
        // use all non string values
        if (!is_string($value)) {
            $this->setExpectedException('\\InvalidArgumentException', null, 1413440336);
            $this->propertyHtml->setValues(array($value));
        }
    }

    public function testSetValueSetsValuesProperty()
    {
        $this->propertyHtml->setValue('foo');
        $this->assertAttributeSame(array('foo'), 'values', $this->propertyHtml);
    }

    /**
     * @dataProvider StringCastDataProvider
     */
    public function testSetValueThrowsExceptionIfInvalidValueGiven($expected, $value)
    {
        // use all non string values
        if (!is_string($value)) {
            $this->setExpectedException('\\InvalidArgumentException', null, 1413440336);
            $this->propertyHtml->setValue(array($value));
        }
    }
}
