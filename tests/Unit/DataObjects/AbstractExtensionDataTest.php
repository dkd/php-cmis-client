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
            '\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException',
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
     * @param boolean $nullAllowed
     */
    public function testCheckTypeThrowsExceptionIfGivenValueIsNotOfExpectedType(
        $expectedType,
        $value,
        $isExceptionExpected,
        $nullAllowed = false
    ) {
        if ($isExceptionExpected === true) {
            $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException', 1413440336);
        }

        $method = $this->getMethod(self::CLASS_TO_TEST, 'checkType');
        $result = $method->invokeArgs($this->abstractExtensionData, array($expectedType, $value, $nullAllowed));

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
                'integer',
                0,
                false
            ),
            array(
                'integer',
                null,
                false,
                true
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
                null,
                false,
                true
            ),
            array(
                '\\DateTime',
                'now',
                true
            )
        );
    }

    /**
     * @dataProvider castValueToSimpleTypeDataProvider
     * @param $expectedType
     * @param $expectedValue
     * @param $value
     * @param $errorNoticeMessageExpected
     */
    public function testCastValueToSimpleTypeCastsValueToExpectedType(
        $expectedType,
        $expectedValue,
        $value
    ) {
        $method = $this->getMethod(self::CLASS_TO_TEST, 'castValueToSimpleType');
        $result = @$method->invokeArgs($this->abstractExtensionData, array($expectedType, $value));
        $this->assertSame($expectedValue, $result);
    }

    /**
     * @dataProvider castValueToSimpleTypeDataProvider
     * @param $expectedType
     * @param $expectedValue
     * @param $value
     * @param $errorNoticeMessageExpected
     */
    public function testCastValueToSimpleTypeTriggersErrorNoticeIfValueIsCasted(
        $expectedType,
        $expectedValue,
        $value,
        $errorNoticeMessageExpected
    ) {
        if ($errorNoticeMessageExpected) {
            $this->setExpectedException('\\PHPUnit_Framework_Error_Notice');
        }
        $method = $this->getMethod(self::CLASS_TO_TEST, 'castValueToSimpleType');
        $result = $method->invokeArgs($this->abstractExtensionData, array($expectedType, $value));
        $this->assertSame($expectedValue, $result);
    }

    public function castValueToSimpleTypeDataProvider()
    {
        return array(
            array(
                'integer',
                2,
                2,
                false
            ),
            array(
                'integer',
                2,
                '2',
                true
            ),
            array(
                'integer',
                2,
                2.2,
                true
            ),
            array(
                'string',
                '2',
                2,
                true
            ),
            array(
                'string',
                '2',
                '2',
                false
            ),
            array(
                'boolean',
                true,
                1,
                true
            )
        );
    }
}
