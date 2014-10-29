<?php
namespace Dkd\PhpCmis\Test\Unit\DataObjects;

/**
 * This file is part of php-cmis-client
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\DataObjects\AbstractExtensionData;
use Dkd\PhpCmis\Test\Unit\ReflectionHelperTrait;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

/**
 * Unit Tests for AbstractExtensionData
 */
class AbstractExtensionDataTest extends PHPUnit_Framework_TestCase
{
    use ReflectionHelperTrait;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|AbstractExtensionData
     */
    protected $abstractExtensionData;

    const CLASS_TO_TEST = '\\Dkd\\PhpCmis\\DataObjects\\AbstractExtensionData';

    public function setUp()
    {
        $this->abstractExtensionData = $this->getMockBuilder(self::CLASS_TO_TEST)->enableProxyingToOriginalMethods(
        )->getMockForAbstractClass();
    }

    public function testSetExtensionsSetsAttributeAndGetExtensionReturnsAttribute()
    {
        $extensions = array(
            $this->getMockBuilder(
                '\\Dkd\\PhpCmis\\Data\\CmisExtensionElementInterface'
            )->getMockForAbstractClass()
        );

        $this->abstractExtensionData->setExtensions($extensions);
        $this->assertAttributeSame($extensions, 'extensions', $this->abstractExtensionData);
        $this->assertSame($extensions, $this->abstractExtensionData->getExtensions());
    }

    public function testSetExtensionsWithInvalidDataThrowsException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'Argument of type "stdClass" given but argument of type '
            . '"\\Dkd\\PhpCmis\\Data\\CmisExtensionElementInterface" was expected.'
        );
        $this->abstractExtensionData->setExtensions(array(new \stdClass()));
    }

    /**
     * @dataProvider checkTypeDataProvider
     * @param string $expectedType
     * @param string $value
     * @param boolean $isExceptionExpected
     */
    public function testCheckTypeThrowsExceptionIfGivenValueIsNotOfExpectedType(
        $expectedType,
        $value,
        $isExceptionExpected
    ) {
        if ($isExceptionExpected === true) {
            $this->setExpectedException('\\InvalidArgumentException', 1413440336);
        }

        $method = $this->getMethod(self::CLASS_TO_TEST, 'checkType');
        $result = $method->invokeArgs($this->abstractExtensionData, array($expectedType, $value));

        if ($isExceptionExpected === false) {
            $this->assertTrue($result);
        }
    }

    public function checkTypeDataProvider()
    {
        return array(
            array(
                'string',
                'foo',
                false
            ),
            array(
                'integer',
                2,
                false
            ),
            array(
                'double',
                2.3,
                false
            ),
            array(
                'boolean',
                true,
                false
            ),
            array(
                'string',
                1,
                true
            ),
            array(
                'integer',
                '1',
                true
            ),
            array(
                'double',
                1,
                true
            ),
            array(
                '\\DateTime',
                new \DateTime(),
                false
            ),
            array(
                '\\DateTime',
                'now',
                true
            )
        );
    }
}
