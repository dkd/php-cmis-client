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

use Dkd\PhpCmis\DataObjects\PropertyUri;
use Dkd\PhpCmis\Test\Unit\DataProviderCollectionTrait;

class PropertyUriTest extends \PHPUnit_Framework_TestCase
{
    use DataProviderCollectionTrait;

    /**
     * @var PropertyUri
     */
    protected $propertyUri;

    public function setUp()
    {
        $this->propertyUri = new PropertyUri();
    }

    public function testSetValuesSetsProperty()
    {
        $values = array('foo', 'bar');
        $this->propertyUri->setValues($values);
        $this->assertAttributeSame($values, 'values', $this->propertyUri);
    }

    /**
     * @dataProvider StringCastDataProvider
     */
    public function testSetValuesThrowsExceptionIfInvalidValuesGiven($expected, $value)
    {
        // use all non string values
        if (!is_string($value)) {
            $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException', null, 1413440336);
            $this->propertyUri->setValues(array($value));
        }
    }

    public function testSetValueSetsValuesProperty()
    {
        $this->propertyUri->setValue('foo');
        $this->assertAttributeSame(array('foo'), 'values', $this->propertyUri);
    }

    /**
     * @dataProvider StringCastDataProvider
     */
    public function testSetValueThrowsExceptionIfInvalidValueGiven($expected, $value)
    {
        // use all non string values
        if (!is_string($value)) {
            $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException', null, 1413440336);
            $this->propertyUri->setValue(array($value));
        }
    }
}
