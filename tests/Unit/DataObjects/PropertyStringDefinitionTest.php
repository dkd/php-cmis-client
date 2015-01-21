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

use Dkd\PhpCmis\DataObjects\PropertyStringDefinition;
use Dkd\PhpCmis\Test\Unit\DataProviderCollectionTrait;

class PropertyStringDefinitionTest extends \PHPUnit_Framework_TestCase
{
    use DataProviderCollectionTrait;

    /**
     * @var PropertyStringDefinition
     */
    protected $propertyStringDefinition;

    public function setUp()
    {
        $this->propertyStringDefinition = new PropertyStringDefinition('testId');
    }

    public function testAssertIsInstanceOfAbstractPropertyDefinition()
    {
        $this->assertInstanceOf(
            '\\Dkd\\PhpCmis\\DataObjects\\AbstractPropertyDefinition',
            $this->propertyStringDefinition
        );
    }

    /**
     * @dataProvider integerCastDataProvider
     * @param integer $expected
     * @param mixed $value
     */
    public function testSetMaxLengthCastsValueToIntegerAndSetsProperty($expected, $value)
    {
        @$this->propertyStringDefinition->setMaxLength($value);
        $this->assertAttributeSame($expected, 'maxLength', $this->propertyStringDefinition);
    }

    /**
     * @depends testSetMaxLengthCastsValueToIntegerAndSetsProperty
     */
    public function testGetMaxLengthReturnsPropertyValue()
    {
        $this->propertyStringDefinition->setMaxLength(100);
        $this->assertSame(100, $this->propertyStringDefinition->getMaxLength());
    }
}
