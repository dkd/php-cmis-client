<?php
namespace Dkd\PhpCmis\Test\Unit;

use Dkd\PhpCmis\Data\ObjectIdInterface;
use Dkd\PhpCmis\Data\ObjectTypeInterface;
use Dkd\PhpCmis\DataObjects\DocumentType;
use Dkd\PhpCmis\DataObjects\FolderType;
use Dkd\PhpCmis\DataObjects\ObjectId;
use Dkd\PhpCmis\DataObjects\PropertyIdDefinition;
use Dkd\PhpCmis\DataObjects\PropertyStringDefinition;
use Dkd\PhpCmis\Definitions\PropertyDefinitionInterface;
use Dkd\PhpCmis\QueryStatement;
use Dkd\PhpCmis\SessionInterface;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Dimitri Ebert <dimitri.ebert@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class QueryStatementTest extends \PHPUnit_Framework_TestCase
{
    use ReflectionHelperTrait;
    use DataProviderCollectionTrait;

    const CLASS_TO_TEST = '\\Dkd\\PhpCmis\\QueryStatement';

    /**
     * Creates and returns QueryStatement object
     *
     * @param string $statement
     * @return QueryStatement
     */
    protected function getQueryStatementObject($statement = 'SELECT * FROM foo')
    {
        /** @var PHPUnit_Framework_MockObject_MockObject|SessionInterface $sessionMock */
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->getMockForAbstractClass();
        $queryStatementObject = new QueryStatement($sessionMock, $statement);

        return $queryStatementObject;
    }

    /**
     * @dataProvider escapeDataProvider
     * @param string $inputString
     * @param string $escapedString
     */
    public function testEscapeReturnsEscapedString($inputString, $escapedString)
    {
        $this->assertEquals(
            $escapedString,
            $this->getMethod(self::CLASS_TO_TEST, 'escape')->invokeArgs(
                $this->getQueryStatementObject(),
                array($inputString)
            )
        );
    }

    /**
     * Data provider for escape
     *
     * @return array
     */
    public function escapeDataProvider()
    {
        return array(
            array('\'', '\'\\\'\''),
            array('\\', '\'\\\\\''),
            array('\\\'', '\'\\\\\\\'\''),
            array('\"', '\'\\\\"\''),
            array('\%', '\'\\\\%\''),
            array('\_', '\'\\\\_\''),
            array('\*', '\'\\\\*\''),
            array('\?', '\'\\\\?\''),
            array('\\?', '\'\\\\?\''),
            array('\foo', '\'\\\\foo\'')
        );
    }

    /**
     * @dataProvider escapeLikeDataProvider
     * @param string $inputString
     * @param string $escapedString
     */
    public function testEscapeLikeReturnsEscapedString($inputString, $escapedString)
    {
        $this->assertEquals(
            $escapedString,
            $this->getMethod(self::CLASS_TO_TEST, 'escapeLike')->invokeArgs(
                $this->getQueryStatementObject(),
                array($inputString)
            )
        );
    }

    /**
     * Data provider for escapeLike
     *
     * @return array
     */
    public function escapeLikeDataProvider()
    {
        return array(
            array('\'', '\'\\\'\''),
            array('\\', '\'\\\\\''),
            array('\\\'', '\'\\\\\\\'\''),
            array('\"', '\'\\\\"\''),
            array('\%', '\'\%\''),
            array('\_', '\'\_\''),
            array('\\\\%', '\'\\\\\\%\''),
            array('\\\\_', '\'\\\\\\_\''),
            array('\*', '\'\\\\*\''),
            array('\?', '\'\\\\?\'')
        );
    }

    /**
     * @dataProvider escapeContainsDataProvider
     * @param string $inputString
     * @param string $escapedString
     */
    public function testEscapeContainsReturnsEscapedString($inputString, $escapedString)
    {
        $this->assertEquals(
            $escapedString,
            $this->getMethod(self::CLASS_TO_TEST, 'escapeContains')->invokeArgs(
                $this->getQueryStatementObject(),
                array($inputString)
            )
        );
    }

    /**
     * Data provider for escapeContains
     *
     * @return array
     */
    public function escapeContainsDataProvider()
    {
        return array(
            array('\'', '\'\\\'\''),
            array('\\', '\'\\\\\''),
            array('\\\'', '\'\\\\\\\'\''),
            array('"', '\'\"\''),
            array('\"', '\'\\\\\\"\''),
            array('\%', '\'\\\\%\''),
            array('\_', '\'\\\\_\''),
            array('\*', '\'\*\''),
            array('\?', '\'\?\''),
            array('\\\\*', '\'\\\\\\*\''),
            array('\\\\?', '\'\\\\\\?\'')
        );
    }

    public function testConstructorSetsSessionPropertyToGivenSession()
    {
        $statement = 'SELECT foo FROM bar';
        /** @var PHPUnit_Framework_MockObject_MockObject|SessionInterface $sessionMock */
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->getMockForAbstractClass();
        $queryStatementObject = new QueryStatement($sessionMock, $statement);

        $this->assertAttributeSame(
            $sessionMock,
            'session',
            $queryStatementObject
        );
    }

    public function testConstructorSetsStatementPropertyToGivenStatement()
    {
        $statement = 'SELECT foo FROM bar';
        /** @var PHPUnit_Framework_MockObject_MockObject|SessionInterface $sessionMock */
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->getMockForAbstractClass();
        $queryStatementObject = new QueryStatement($sessionMock, $statement);

        $this->assertAttributeSame(
            $statement,
            'statement',
            $queryStatementObject
        );
    }

    /**
     * @dataProvider invalidStatementDataProvider
     * @param mixed $idValue
     */
    public function testConstructorThrowsExceptionIfStatementIsEmpty($statement)
    {
        $this->setExpectedException(
            '\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException',
            'Statement must not be empty!'
        );
        $this->getQueryStatementObject($statement);
    }

    /**
     * Data provider for escapeContains
     *
     * @return array
     */
    public function invalidStatementDataProvider()
    {
        return array(
            array(''),
            array('  '),
            array(null)
        );
    }

    /**
     * @dataProvider setBooleanDataProvider
     * @param integer $parameterIndex
     * @param boolean $value
     * @param string $expectedValue
     * @param \Closure $callback
     */
    public function testSetBooleanAddsParametersMapValue(
        $parameterIndex,
        $value,
        $expectedValue,
        \Closure $callback = null
    ) {
        if ($callback) {
            $callback($this);
        }
        $queryStatement = $this->getQueryStatementObject();
        $queryStatement->setBoolean($parameterIndex, $value);

        $this->assertAttributeSame(
            array($parameterIndex => $expectedValue),
            'parametersMap',
            $queryStatement
        );
    }

    /**
     * Data provider for setBoolean
     *
     * @return array
     */
    public function setBooleanDataProvider()
    {
        return array(
            array(1, true, 'TRUE'),
            array(2, false, 'FALSE'),
            array(
                '2',
                false,
                'FALSE',
                function (QueryStatementTest $parent) {
                    $parent->setExpectedException(
                        '\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException',
                        'Parameter index must be of type integer!'
                    );
                }
            ),
        );
    }

    /**
     * @dataProvider setDateTimeDataProvider
     * @param integer $parameterIndex
     * @param \DateTime $value
     * @param string $expectedValue
     * @param \Closure $callback
     */
    public function testSetDateTimeAddsParametersMapValue(
        $parameterIndex,
        \DateTime $value,
        $expectedValue,
        \Closure $callback = null
    ) {
        if ($callback) {
            $callback($this);
        }
        $queryStatement = $this->getQueryStatementObject();
        $queryStatement->setDateTime($parameterIndex, $value);

        $this->assertAttributeSame(
            array($parameterIndex => $expectedValue),
            'parametersMap',
            $queryStatement
        );
    }

    /**
     * Data provider for setDateTime
     *
     * @return array
     */
    public function setDateTimeDataProvider()
    {
        return array(
            array(1, new \DateTime('2015-03-27 11:14:15.638276+02:00'), '2015-03-27T11:14:15.638276+02:00'),
            array(2, new \DateTime('2015-02-26 10:21:25.523185+01:00'), '2015-02-26T10:21:25.523185+01:00'),
            array(
                '2',
                new \DateTime('2015-02-26 10:21:25.523185+01:00'),
                '2015-02-26T10:21:25.523185+01:00',
                function (QueryStatementTest $parent) {
                    $parent->setExpectedException(
                        '\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException',
                        'Parameter index must be of type integer!'
                    );
                }
            )
        );
    }

    /**
     * @dataProvider setDateTimeTimestampDataProvider
     * @param integer $parameterIndex
     * @param \DateTime $value
     * @param string $expectedValue
     * @param \Closure $callback
     */
    public function testSetDateTimeTimestampAddsParametersMapValue(
        $parameterIndex,
        \DateTime $value,
        $expectedValue,
        \Closure $callback = null
    ) {
        if ($callback) {
            $callback($this);
        }

        $queryStatement = $this->getQueryStatementObject();
        $queryStatement->setDateTimeTimestamp($parameterIndex, $value);

        $this->assertAttributeSame(
            array($parameterIndex => $expectedValue),
            'parametersMap',
            $queryStatement
        );
    }

    /**
     * Data provider for setDateTimeTimestamp
     *
     * @return array
     */
    public function setDateTimeTimestampDataProvider()
    {
        return array(
            array(1, new \DateTime('2015-03-27 11:14:15.638276+02:00'), 'TIMESTAMP 2015-03-27T11:14:15.638276+02:00'),
            array(2, new \DateTime('2015-02-26 10:21:25.523185+01:00'), 'TIMESTAMP 2015-02-26T10:21:25.523185+01:00'),
            array(
                '2',
                new \DateTime('2015-02-26 10:21:25.523185+01:00'),
                'TIMESTAMP 2015-02-26T10:21:25.523185+01:00',
                function (QueryStatementTest $parent) {
                    $parent->setExpectedException(
                        '\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException',
                        'Parameter index must be of type integer!'
                    );
                }
            )
        );
    }

    /**
     * @dataProvider setIdDataProvider
     * @param integer $parameterIndex
     * @param ObjectIdInterface $value
     * @param string $expectedValue
     * @param \Closure $callback
     */
    public function testSetIdAddsParametersMapValue(
        $parameterIndex,
        ObjectIdInterface $value,
        $expectedValue,
        \Closure $callback = null
    ) {
        if ($callback) {
            $callback($this);
        }

        $queryStatement = $this->getQueryStatementObject();
        $queryStatement->setId($parameterIndex, $value);

        $this->assertAttributeSame(
            array($parameterIndex => $expectedValue),
            'parametersMap',
            $queryStatement
        );
    }

    /**
     * Data provider for setIdData
     *
     * @return array
     */
    public function setIdDataProvider()
    {
        $folderMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\Document'
        )->disableOriginalConstructor()->getMock();
        $folderMock->expects($this->once())->method('getId')->willReturn('bar');

        $documentMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\Folder'
        )->disableOriginalConstructor()->getMock();
        $documentMock->expects($this->once())->method('getId')->willReturn('baz');

        return array(
            array(1, new ObjectId('foo'), '\'foo\''),
            array(2, $folderMock, '\'bar\''),
            array(3, $documentMock, '\'baz\''),
            array(
                '1',
                new ObjectId('foo'),
                '\'foo\'',
                function (QueryStatementTest $parent) {
                    $parent->setExpectedException(
                        '\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException',
                        'Parameter index must be of type integer!'
                    );
                }
            )
        );
    }

    /**
     * @dataProvider setNumberDataProvider
     * @param integer $parameterIndex
     * @param integer|string $value
     * @param string $expectedValue
     * @param \Closure $callback
     */
    public function testSetNumberAddsParametersMapValue(
        $parameterIndex,
        $value,
        $expectedValue,
        \Closure $callback = null
    ) {
        if ($callback) {
            $callback($this);
        }

        $queryStatement = $this->getQueryStatementObject();
        $queryStatement->setNumber($parameterIndex, $value);

        $this->assertAttributeSame(
            array($parameterIndex => $expectedValue),
            'parametersMap',
            $queryStatement
        );
    }

    /**
     * Data provider for setNumber
     *
     * @return array
     */
    public function setNumberDataProvider()
    {
        return array(
            array(1, 123, 123),
            array(2, 456, 456),
            array(
                2,
                'abz',
                456,
                function (QueryStatementTest $parent) {
                    $parent->setExpectedException(
                        '\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException',
                        'Number must be of type integer!'
                    );
                }
            ),
            array(
                '1',
                456,
                456,
                function (QueryStatementTest $parent) {
                    $parent->setExpectedException(
                        '\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException',
                        'Parameter index must be of type integer!'
                    );
                }
            )
        );
    }

    /**
     * @dataProvider setPropertyDataProvider
     * @param integer $parameterIndex
     * @param PropertyDefinitionInterface $value
     * @param string $expectedValue
     * @param \Closure $callback
     */
    public function testSetPropertyAddsParametersMapValue(
        $parameterIndex,
        PropertyDefinitionInterface $value,
        $expectedValue,
        \Closure $callback = null
    ) {
        if ($callback) {
            $callback($this);
        }

        $queryStatement = $this->getQueryStatementObject();
        $queryStatement->setProperty($parameterIndex, $value);

        $this->assertAttributeSame(
            array($parameterIndex => $expectedValue),
            'parametersMap',
            $queryStatement
        );
    }

    /**
     * Data provider for setProperty
     *
     * @return array
     */
    public function setPropertyDataProvider()
    {
        $propertyStringDefinition = new PropertyStringDefinition('foo');
        $propertyStringDefinition->setQueryName('foo:bar');

        $propertyIdDefinition = new PropertyIdDefinition('bar');
        $propertyIdDefinition->setQueryName('bar:baz');

        $propertyIdDefinitionWithEmptyQueryName = new PropertyIdDefinition('bar');

        return array(
            array(1, $propertyStringDefinition, '\'foo:bar\''),
            array(2, $propertyIdDefinition, '\'bar:baz\''),
            array(
                2,
                $propertyIdDefinitionWithEmptyQueryName,
                '\'bar:baz\'',
                function (QueryStatementTest $parent) {
                    $parent->setExpectedException(
                        '\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException',
                        'Property has no query name!'
                    );
                }
            ),
            array(
                '2',
                $propertyIdDefinition,
                '\'bar:baz\'',
                function (QueryStatementTest $parent) {
                    $parent->setExpectedException(
                        '\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException',
                        'Parameter index must be of type integer!'
                    );
                }
            )
        );
    }

    /**
     * @dataProvider setStringDataProvider
     * @param integer $parameterIndex
     * @param string $value
     * @param string $expectedValue
     * @param \Closure $callback
     */
    public function testSetStringAddsParametersMapValue(
        $parameterIndex,
        $value,
        $expectedValue,
        \Closure $callback = null
    ) {
        if ($callback) {
            $callback($this);
        }

        $queryStatement = $this->getQueryStatementObject();
        $queryStatement->setString($parameterIndex, $value);

        $this->assertAttributeSame(
            array($parameterIndex => $expectedValue),
            'parametersMap',
            $queryStatement
        );
    }

    /**
     * Data provider for setString
     *
     * @return array
     */
    public function setStringDataProvider()
    {
        return array(
            array(1, 'foo', '\'foo\''),
            array(2, 'foo:bar', '\'foo:bar\''),
            array(2, 'foo\'bar', '\'foo\\\'bar\''),
            array(3, 'foo\bar\baz', '\'foo\\\\bar\\\\baz\''),
            array(3, 'foo\\bar\\baz', '\'foo\\\\bar\\\\baz\''),
            array(
                3,
                1,
                '',
                function (QueryStatementTest $parent) {
                    $parent->setExpectedException(
                        '\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException',
                        'Parameter string must be of type string!'
                    );
                }
            ),
            array(
                '3',
                'foo\\bar\\baz',
                '\'foo\\\\bar\\\\baz\'',
                function (QueryStatementTest $parent) {
                    $parent->setExpectedException(
                        '\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException',
                        'Parameter index must be of type integer!'
                    );
                }
            )
        );
    }

    /**
     * @dataProvider setStringContainsDataProvider
     * @param integer $parameterIndex
     * @param string $value
     * @param string $expectedValue
     * @param \Closure $callback
     */
    public function testSetStringContainsAddsParametersMapValue(
        $parameterIndex,
        $value,
        $expectedValue,
        \Closure $callback = null
    ) {
        if ($callback) {
            $callback($this);
        }
        $queryStatement = $this->getQueryStatementObject();
        $queryStatement->setStringContains($parameterIndex, $value);

        $this->assertAttributeSame(
            array($parameterIndex => $expectedValue),
            'parametersMap',
            $queryStatement
        );
    }

    /**
     * Data provider for setStringContains
     *
     * @return array
     */
    public function setStringContainsDataProvider()
    {
        return array(
            array(1, 'foo', '\'foo\''),
            array(2, 'foo:bar', '\'foo:bar\''),
            array(2, 'foo\'bar', '\'foo\\\'bar\''),
            array(3, 'foo\bar\baz', '\'foo\\\\bar\\\\baz\''),
            array(3, 'foo\*bar\\baz', '\'foo\\*bar\\\\baz\''),
            array(3, 'foo\?"bar"', '\'foo\\?\\"bar\\"\''),
            array(3, 'foo%bar\\baz', '\'foo%bar\\\\baz\''),
            array(3, 'foo_?"bar"', '\'foo_?\\"bar\\"\''),
            array(
                1,
                1,
                '',
                function (QueryStatementTest $parent) {
                    $parent->setExpectedException(
                        '\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException',
                        'Parameter string must be of type string!'
                    );
                }
            ),
            array(
                '1',
                'foo',
                '\'foo\'',
                function (QueryStatementTest $parent) {
                    $parent->setExpectedException(
                        '\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException',
                        'Parameter index must be of type integer!'
                    );
                }
            )
        );
    }

    /**
     * @dataProvider setStringLikeDataProvider
     * @param integer $parameterIndex
     * @param $value
     * @param string $expectedValue
     * @param \Closure $callback
     */
    public function testSetStringLikeAddsParametersMapValue(
        $parameterIndex,
        $value,
        $expectedValue,
        \Closure $callback = null
    ) {
        if ($callback) {
            $callback($this);
        }
        $queryStatement = $this->getQueryStatementObject();
        $queryStatement->setStringLike($parameterIndex, $value);

        $this->assertAttributeSame(
            array($parameterIndex => $expectedValue),
            'parametersMap',
            $queryStatement
        );
    }

    /**
     * Data provider for setStringLike
     *
     * @return array
     */
    public function setStringLikeDataProvider()
    {
        return array(
            array(1, 'foo', '\'foo\''),
            array(2, 'foo:bar', '\'foo:bar\''),
            array(2, 'foo\'bar', '\'foo\\\'bar\''),
            array(3, 'foo\bar\baz', '\'foo\\\\bar\\\\baz\''),
            array(3, 'foo\*bar\\baz', '\'foo\\\\*bar\\\\baz\''),
            array(3, 'foo\?"bar"', '\'foo\\\\?"bar"\''),
            array(3, 'foo%bar\%baz', '\'foo%bar\\%baz\''),
            array(3, 'foo\_?"bar"', '\'foo\\_?"bar"\''),
            array(
                1,
                1,
                '',
                function (QueryStatementTest $parent) {
                    $parent->setExpectedException(
                        '\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException',
                        'Parameter string must be of type string!'
                    );
                }
            ),
            array(
                '1',
                'foo',
                '\'foo\'',
                function (QueryStatementTest $parent) {
                    $parent->setExpectedException(
                        '\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException',
                        'Parameter index must be of type integer!'
                    );
                }
            )
        );
    }

    /**
     * @dataProvider setTypeDataProvider
     * @param integer $parameterIndex
     * @param ObjectTypeInterface $value
     * @param string $expectedValue
     * @param \Closure $callback
     */
    public function testSetTypeAddsParametersMapValue(
        $parameterIndex,
        ObjectTypeInterface $value,
        $expectedValue,
        \Closure $callback = null
    ) {
        if ($callback) {
            $callback($this);
        }

        $queryStatement = $this->getQueryStatementObject();
        $queryStatement->setType($parameterIndex, $value);

        $this->assertAttributeSame(
            array($parameterIndex => $expectedValue),
            'parametersMap',
            $queryStatement
        );
    }

    /**
     * Data provider for setType
     *
     * @return array
     */
    public function setTypeDataProvider()
    {
        /** @var DocumentType|PHPUnit_Framework_MockObject_MockObject $folderTypeMock */
        $folderTypeMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\DocumentType'
        )->disableOriginalConstructor()->getMock();
        $folderTypeMock2 = clone $folderTypeMock;
        $folderTypeMock->expects($this->once())->method('getQueryName')->willReturn('foo:bar');
        $folderTypeMock2->expects($this->once())->method('getQueryName')->willReturn('foo:bar');

        /** @var FolderType|PHPUnit_Framework_MockObject_MockObject $documentTypeMock */
        $documentTypeMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\FolderType'
        )->disableOriginalConstructor()->getMock();
        $documentTypeMock->expects($this->once())->method('getQueryName')->willReturn('bar:baz');

        return array(
            array(1, $folderTypeMock, '\'foo:bar\''),
            array(2, $documentTypeMock, '\'bar:baz\''),
            array(
                '1',
                $folderTypeMock2,
                '\'foo:bar\'',
                function (QueryStatementTest $parent) {
                    $parent->setExpectedException(
                        '\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException',
                        'Parameter index must be of type integer!'
                    );
                }
            )
        );
    }

    /**
     * @dataProvider toQueryStringDataProvider
     * @param $statement
     * @param $parameters
     * @param $expectedQueryString
     */
    public function testToQueryStringReturnQueryAsString($statement, $parameters, $expectedQueryString)
    {
        $queryStatement = $this->getQueryStatementObject($statement);

        foreach ($parameters as $function => $arguments) {
            list($parameterIndex, $value) = $arguments;
            $queryStatement->$function($parameterIndex, $value);
        }

        $this->assertEquals(
            $expectedQueryString,
            $queryStatement->toQueryString()
        );
    }

    /**
     * Data provider for toQueryString
     *
     * @return array
     */
    public function toQueryStringDataProvider()
    {
        return array(
            array(
                'SELECT * FROM foo:bar WHERE property1 = ? AND property2 > ? ',
                array(
                    'setString' => array(1, 'baz'),
                    'setNumber' => array(2, 123)
                ),
                'SELECT * FROM foo:bar WHERE property1 = \'baz\' AND property2 > 123'
            ),
            array(
                'SELECT * FROM bar WHERE id = ?',
                array(
                    'setId' => array(1, new ObjectId('foobar'))
                ),
                'SELECT * FROM bar WHERE id = \'foobar\''
            ),
            array(
                'SELECT * FROM bar WHERE property1 = \'as\\\'df\' AND id = ?',
                array(
                    'setId' => array(1, new ObjectId('foobar'))
                ),
                'SELECT * FROM bar WHERE property1 = \'as\\\'df\' AND id = \'foobar\''
            ),
        );
    }

    public function testQueryExecutesQuery()
    {
        $queryResultInterfaceMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\QueryResultInterface'
        )->getMockForAbstractClass();
        $queryResultArray = array($queryResultInterfaceMock);
        /** @var PHPUnit_Framework_MockObject_MockObject|SessionInterface $sessionMock */
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->getMockForAbstractClass();
        $sessionMock->expects($this->once())->method('query')->willReturn($queryResultArray);

        $statement = 'SELECT * FROM foo:bar';
        $queryStatement = new QueryStatement($sessionMock, $statement);

        $this->assertSame(
            $queryResultArray,
            $queryStatement->query(false)
        );
    }
}
