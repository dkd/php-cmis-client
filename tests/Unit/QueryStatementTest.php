<?php
namespace Dkd\PhpCmis\Test\Unit;

use Dkd\PhpCmis\Data\ObjectIdInterface;
use Dkd\PhpCmis\Data\ObjectTypeInterface;
use Dkd\PhpCmis\DataObjects\DocumentType;
use Dkd\PhpCmis\DataObjects\FolderType;
use Dkd\PhpCmis\DataObjects\ObjectId;
use Dkd\PhpCmis\DataObjects\PropertyIdDefinition;
use Dkd\PhpCmis\DataObjects\PropertyStringDefinition;
use Dkd\PhpCmis\DataObjects\SecondaryTypeDefinition;
use Dkd\PhpCmis\Definitions\PropertyDefinitionInterface;
use Dkd\PhpCmis\Exception\CmisObjectNotFoundException;
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
     * @param array $arguments
     * @param integer $expectedExceptionCode
     * @dataProvider getConstructorErrorArguments
     */
    public function testConstructorThrowsExceptionOnInvalidArguments(array $arguments, $expectedExceptionCode)
    {
        $this->setExpectedException(
            '\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException',
            '',
            $expectedExceptionCode
        );
        $p1 = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')->getMockForAbstractClass();
        list ($p2, $p3, $p4, $p5, $p6) = $arguments;
        new QueryStatement($p1, $p2, $p3, $p4, $p5, $p6);
    }

    /**
     * @return array
     */
    public function getConstructorErrorArguments()
    {
        return array(
            'Statement null' => array(
                array(null, array(), array(), null, array()),
                1441286811
            ),
            'Statement empty' => array(
                array('', array(), array(), null, array()),
                1441286811
            ),
            'Statement whitespace' => array(
                array(' ', array(), array(), null, array()),
                1441286811
            ),
            'Types empty' => array(
                array(null, array('foobar'), array(), null, array()),
                1441286812
            ),
            'Manual statement cannot be used when properties are used' => array(
                array('foobar', array('foobar'), array(), null, array()),
                1441286813
            ),
            'Manual statement cannot be used when types are used' => array(
                array('foobar', array(), array('foobar'), null, array()),
                1441286814
            ),
            'Manual statement cannot be used when clause is used' => array(
                array('foobar', array(), array(), 'foobar', array()),
                1441286815
            ),
            'Manual statement cannot be used when orderings are used' => array(
                array('foobar', array(), array(), null, array('foobar')),
                1441286816
            ),
        );
    }

    /**
     * @param array $arguments
     * @param string $expectedStatement
     * @dataProvider getConstructorStatementGenerationData
     */
    public function testConstructorGeneratesExpectedStatementFromArguments(array $arguments, $expectedStatement)
    {
        list ($p2, $p3, $p4, $p5, $p6) = $arguments;
        $type = $this->getMockBuilder('Dkd\\PhpCmis\\Definitions\\TypeDefinitionInterface')
            ->setMethods(array('getQueryName'))
            ->getMockForAbstractClass();
        $type->expects($this->any())->method('getQueryName')->willReturn('t');
        $p1 = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')
            ->setMethods(array('getTypeDefinition'))
            ->getMockForAbstractClass();
        $p1->expects($this->any())->method('getTypeDefinition')->willReturn($type);
        $queryStatement = new QueryStatement($p1, $p2, $p3, $p4, $p5, $p6);
        $this->assertAttributeEquals($expectedStatement, 'statement', $queryStatement);
    }

    /**
     * @return array
     */
    public function getConstructorStatementGenerationData()
    {
        $propertyDefinition1 = new PropertyStringDefinition('p1');
        $propertyDefinition1->setQueryName('p1-qn');
        $propertyDefinition2 = new PropertyStringDefinition('p2');
        $propertyDefinition2->setQueryName('p2-qn');
        $typeDefinition1 = new SecondaryTypeDefinition('t1');
        $typeDefinition1->setQueryName('t1-qn');
        $typeDefinition2 = new SecondaryTypeDefinition('t2');
        $typeDefinition2->setQueryName('t2-qn');
        return array(
            'Statement passed through if provided' => array(
                array('foobar-statement', array(), array(), null, array()),
                'foobar-statement'
            ),
            'Single property from single type without clause without ordering' => array(
                array(null, array('p1'), array('t1'), null, array()),
                'SELECT p1 FROM t primary'
            ),
            'Single property from single type with alias without clause without ordering' => array(
                array(null, array('p1'), array('t1 alias1'), null, array()),
                'SELECT p1 FROM t alias1'
            ),
            'Single property from single type with clause without ordering' => array(
                array(null, array('p1'), array('t1'), '1=1', array()),
                'SELECT p1 FROM t primary WHERE 1=1'
            ),
            'Single property from single type with clause with ordering' => array(
                array(null, array('p1'), array('t1'), '1=1', array('p1 ASC')),
                'SELECT p1 FROM t primary WHERE 1=1 ORDER BY p1 ASC'
            ),
            'Single property from two types without clause without ordering' => array(
                array(null, array('p1'), array('t1', 't2'), null, array()),
                'SELECT p1 FROM t primary JOIN t AS a ON primary.cmis:objectId = a.cmis:objectId'
            ),
            'Two properties from single type without clause without ordering' => array(
                array(null, array('p1', 'p2'), array('t1'), null, array()),
                'SELECT p1, p2 FROM t primary'
            ),
            'Two properties from two types without clause without ordering' => array(
                array(null, array('p1', 'p2'), array('t1', 't2'), null, array()),
                'SELECT p1, p2 FROM t primary JOIN t AS a ON primary.cmis:objectId = a.cmis:objectId'
            ),
            'Two properties from two types with clause without ordering' => array(
                array(null, array('p1', 'p2'), array('t1', 't2'), '1=1', array()),
                'SELECT p1, p2 FROM t primary JOIN t AS a ON primary.cmis:objectId = a.cmis:objectId WHERE 1=1'
            ),
            'Two properties from two types with clause with ordering' => array(
                array(null, array('p1', 'p2'), array('t1', 't2'), '1=1', array('p1 ASC')),
                'SELECT p1, p2 FROM t primary JOIN t AS a ON primary.cmis:objectId = a.cmis:objectId WHERE 1=1' .
                    ' ORDER BY p1 ASC'
            ),
            'Multiple orderings without clause' => array(
                array(null, array('p1', 'p2'), array('t1'), null, array('p1 ASC', 'p2 DESC')),
                'SELECT p1, p2 FROM t primary ORDER BY p1 ASC, p2 DESC'
            ),
            'Multiple orderings with clause' => array(
                array(null, array('p1', 'p2'), array('t1'), '1=1', array('p1 ASC', 'p2 DESC')),
                'SELECT p1, p2 FROM t primary WHERE 1=1 ORDER BY p1 ASC, p2 DESC'
            ),
            'Alias of tables provided for single table' => array(
                array(null, array('p1'), array(array('t1', 'alias')), null, array()),
                'SELECT p1 FROM t alias'
            ),
            'Alias of tables provided for multiple tables' => array(
                array(null, array('p1'), array(array('t1', 'alias1'), array('t2', 'alias2')), null, array()),
                'SELECT p1 FROM t alias1 JOIN t AS alias2 ON alias1.cmis:objectId = alias2.cmis:objectId'
            ),
            'TypeDefinition instance in property list' => array(
                array(null, array($propertyDefinition1), array('t1'), null, array()),
                'SELECT p1-qn FROM t primary'
            ),
            'Multiple TypeDefinition instances in property list' => array(
                array(null, array($propertyDefinition1, $propertyDefinition2), array('t1'), null, array()),
                'SELECT p1-qn, p2-qn FROM t primary'
            ),
            'TypeDefinition instance in types list' => array(
                array(null, array('p1'), array($typeDefinition1), null, array()),
                'SELECT p1 FROM t primary'
            ),
            'TypeDefinition instance with alias in types list' => array(
                array(null, array('p1'), array(array($typeDefinition1, 'alias1')), null, array()),
                'SELECT p1 FROM t alias1'
            ),
            'Multiple TypeDefinition instances in types list' => array(
                array(null, array('p1'), array($typeDefinition1, $typeDefinition2), null, array()),
                'SELECT p1 FROM t primary JOIN t AS a ON primary.cmis:objectId = a.cmis:objectId'
            ),
            'Multiple TypeDefinition instances with aliases in types list' => array(
                array(
                    null,
                    array('p1'),
                    array(
                        array($typeDefinition1, 'alias1'),
                        array($typeDefinition2, 'alias2')
                    ),
                    null,
                    array()
                ),
                'SELECT p1 FROM t alias1 JOIN t AS alias2 ON alias1.cmis:objectId = alias2.cmis:objectId'
            ),
            'TypeDefinition instance in orderings list' => array(
                array(null, array('p1'), array('t1'), null, array($propertyDefinition1)),
                'SELECT p1 FROM t primary ORDER BY p1-qn ASC'
            ),
            'Multiple TypeDefinition instances in orderings list' => array(
                array(null, array('p1'), array('t1'), null, array($propertyDefinition1, $propertyDefinition2)),
                'SELECT p1 FROM t primary ORDER BY p1-qn ASC, p2-qn ASC'
            ),
            'Multiple TypeDefinition instances with direction in orderings list' => array(
                array(
                    null,
                    array('p1'),
                    array('t1'),
                    null,
                    array(
                        array($propertyDefinition1, 'DESC'),
                        array($propertyDefinition2, 'DESC')
                    ),
                ),
                'SELECT p1 FROM t primary ORDER BY p1-qn DESC, p2-qn DESC'
            ),
        );
    }

    public function testGetQueryNameAndAliasForTypeReturnsInputIfObjectIsUnloadable()
    {
        $exception = new CmisObjectNotFoundException();
        $sessionMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\SessionInterface')
            ->setMethods(array('getTypeDefinition'))
            ->getMockForAbstractClass();
        $sessionMock->expects($this->once())->method('getTypeDefinition')->willThrowException($exception);
        $queryStatement = new QueryStatement($sessionMock, 'foobar');
        $method = new \ReflectionMethod($queryStatement, 'getQueryNameAndAliasForType');
        $method->setAccessible(true);
        $input = array('foobar-notfound', 'unused');
        $output = $method->invokeArgs($queryStatement, $input);
        $this->assertEquals($input, $output);
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
