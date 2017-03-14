<?php
namespace Dkd\PhpCmis\Test\Unit\DataObjects;

/*
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Test\Unit\ReflectionHelperTrait;
use Dkd\PhpCmis\Traits\TypeHelperTrait;

/**
 * Class TypeHelperTraitTest
 */
class TypeHelperTraitTest extends \PHPUnit_Framework_TestCase
{
    use ReflectionHelperTrait;

    const CLASS_TO_TEST = 'DkdPhpCmisTypeHelperMockTrait';

    /**
     * @var TypeHelperTrait|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $typeHelperTrait;

    public function setUp()
    {
        $this->typeHelperTrait = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Traits\\TypeHelperTrait'
        )->setMockClassName(self::CLASS_TO_TEST)->getMockForTrait();
    }

    /**
     * @dataProvider checkTypeDataProvider
     * @param string $expectedType
     * @param string $value
     * @param \Closure $callback
     * @param boolean $nullAllowed
     */
    public function testCheckTypeThrowsExceptionIfGivenValueIsNotOfExpectedType(
        $expectedType,
        $value,
        \Closure $callback = null,
        $nullAllowed = false
    ) {
        if ($callback !== null) {
            $callback($this);
        }

        $method = $this->getMethod(self::CLASS_TO_TEST, 'checkType');
        $result = $method->invokeArgs($this->typeHelperTrait, [$expectedType, $value, $nullAllowed]);

        $this->assertTrue($result);
    }

    /**
     * @return array
     */
    public function checkTypeDataProvider()
    {
        return [
            [
                'string',
                'foo'
            ],
            [
                'integer',
                2
            ],
            [
                'integer',
                0
            ],
            [
                'integer',
                null,
                null,
                true
            ],
            [
                'double',
                2.3
            ],
            [
                'boolean',
                true
            ],
            [
                'string',
                1,
                function (TypeHelperTraitTest $parent) {
                    $parent->setExpectedException(
                        '\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException',
                        '',
                        1413440336
                    );
                }
            ],
            [
                'integer',
                '1',
                function (TypeHelperTraitTest $parent) {
                    $parent->setExpectedException(
                        '\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException',
                        '',
                        1413440336
                    );
                }
            ],
            [
                'double',
                1,
                function (TypeHelperTraitTest $parent) {
                    $parent->setExpectedException(
                        '\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException',
                        '',
                        1413440336
                    );
                }
            ],
            [
                '\\DateTime',
                new \DateTime()
            ],
            [
                '\\DateTime',
                null,
                null,
                true
            ],
            [
                '\\DateTime',
                'now',
                function (TypeHelperTraitTest $parent) {
                    $parent->setExpectedException(
                        '\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException',
                        '',
                        1413440336
                    );
                }
            ]
        ];
    }

    /**
     * @dataProvider castValueToSimpleTypeDataProvider
     * @param string $expectedType
     * @param mixed $expectedValue
     * @param mixed $value
     */
    public function testCastValueToSimpleTypeCastsValueToExpectedType(
        $expectedType,
        $expectedValue,
        $value
    ) {
        $method = $this->getMethod(self::CLASS_TO_TEST, 'castValueToSimpleType');
        $result = @$method->invokeArgs($this->typeHelperTrait, [$expectedType, $value]);
        $this->assertSame($expectedValue, $result);
    }

    /**
     * @dataProvider castValueToSimpleTypeDataProvider
     * @param string $expectedType
     * @param mixed $expectedValue
     * @param mixed $value
     * @param boolean $errorNoticeMessageExpected
     */
    public function testCastValueToSimpleTypeTriggersErrorNoticeIfValueIsCasted(
        $expectedType,
        $expectedValue,
        $value,
        $errorNoticeMessageExpected
    ) {
        if (PHP_INT_SIZE == 4) {
            //TODO: 32bit - handle this specially?
            //we might get doubles instead of values at other points, thus the notification is disabled
            return;
        }

        if ($errorNoticeMessageExpected) {
            $this->setExpectedException('\\PHPUnit_Framework_Error_Notice');
        }
        $method = $this->getMethod(self::CLASS_TO_TEST, 'castValueToSimpleType');
        $result = $method->invokeArgs($this->typeHelperTrait, [$expectedType, $value]);
        $this->assertSame($expectedValue, $result);
    }

    /**
     * @return array
     */
    public function castValueToSimpleTypeDataProvider()
    {
        return [
            [
                'integer',
                2,
                2,
                false
            ],
            [
                'integer',
                2,
                '2',
                true
            ],
            [
                'integer',
                2,
                2.2,
                true
            ],
            [
                'string',
                '2',
                2,
                true
            ],
            [
                'string',
                '2',
                '2',
                false
            ],
            [
                'boolean',
                true,
                1,
                true
            ]
        ];
    }
}
