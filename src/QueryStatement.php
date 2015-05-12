<?php
namespace Dkd\PhpCmis;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Dimitri Ebert <dimitri.ebert@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Data\ObjectIdInterface;
use Dkd\PhpCmis\Data\ObjectTypeInterface;
use Dkd\PhpCmis\Definitions\PropertyDefinitionInterface;
use Dkd\PhpCmis\Exception\CmisInvalidArgumentException;

/**
 * Query Statement.
 */
class QueryStatement implements QueryStatementInterface
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var string
     */
    protected $statement;

    /**
     * @var array
     */
    protected $parametersMap = array();

    /**
     * @param SessionInterface $session
     * @param string $statement
     * @throws CmisInvalidArgumentException
     */
    public function __construct(SessionInterface $session, $statement)
    {
        $statement = trim($statement);
        if (empty($statement)) {
            throw new CmisInvalidArgumentException('Statement must not be empty!');
        }

        $this->session = $session;
        $this->statement = $statement;
    }

    /**
     * Executes the query.
     *
     * @param boolean $searchAllVersions <code>true</code> if all document versions should be included in the search
     *      results, <code>false</code> if only the latest document versions should be included in the search results
     * @param OperationContextInterface|null $context the operation context to use
     * @return QueryResultInterface[]
     */
    public function query($searchAllVersions, OperationContextInterface $context = null)
    {
        return $this->session->query($this->toQueryString(), $searchAllVersions, $context);
    }

    /**
     * Sets the designated parameter to the given boolean.
     *
     * @param integer $parameterIndex the parameter index (one-based)
     * @param boolean $bool the boolean
     */
    public function setBoolean($parameterIndex, $bool)
    {
        $this->setParameter($parameterIndex, $bool === true ? 'TRUE' : 'FALSE');
    }

    /**
     * Sets the designated parameter to the given DateTime value.
     *
     * @param integer $parameterIndex the parameter index (one-based)
     * @param \DateTime $dateTime the DateTime value as DateTime object
     */
    public function setDateTime($parameterIndex, \DateTime $dateTime)
    {
        $this->setParameter($parameterIndex, $dateTime->format(Constants::QUERY_DATETIMEFORMAT));
    }

    /**
     * Sets the designated parameter to the given DateTime value with the prefix 'TIMESTAMP '.
     *
     * @param integer $parameterIndex the parameter index (one-based)
     * @param \DateTime $dateTime the DateTime value as DateTime object
     */
    public function setDateTimeTimestamp($parameterIndex, \DateTime $dateTime)
    {
        $this->setParameter($parameterIndex, 'TIMESTAMP ' . $dateTime->format(Constants::QUERY_DATETIMEFORMAT));
    }

    /**
     * Sets the designated parameter to the given object ID.
     *
     * @param integer $parameterIndex the parameter index (one-based)
     * @param ObjectIdInterface $id the object ID
     */
    public function setId($parameterIndex, ObjectIdInterface $id)
    {
        $this->setParameter($parameterIndex, $this->escape($id->getId()));
    }

    /**
     * Sets the designated parameter to the given number.
     *
     * @param integer $parameterIndex the parameter index (one-based)
     * @param integer $number the value to be set as number
     * @throws CmisInvalidArgumentException If number not of type integer
     */
    public function setNumber($parameterIndex, $number)
    {
        if (!is_int($number)) {
            throw new CmisInvalidArgumentException('Number must be of type integer!');
        }

        $this->setParameter($parameterIndex, $number);
    }

    /**
     * Sets the designated parameter to the query name of the given property.
     *
     * @param integer $parameterIndex the parameter index (one-based)
     * @param PropertyDefinitionInterface $propertyDefinition
     * @throws CmisInvalidArgumentException If property has no query name
     */
    public function setProperty($parameterIndex, PropertyDefinitionInterface $propertyDefinition)
    {
        $queryName = $propertyDefinition->getQueryName();
        if (empty($queryName)) {
            throw new CmisInvalidArgumentException('Property has no query name!');
        }

        $this->setParameter($parameterIndex, $this->escape($queryName));
    }

    /**
     * Sets the designated parameter to the given string.
     *
     * @param integer $parameterIndex the parameter index (one-based)
     * @param string $string the string
     * @throws CmisInvalidArgumentException If given value is not a string
     */
    public function setString($parameterIndex, $string)
    {
        if (!is_string($string)) {
            throw new CmisInvalidArgumentException('Parameter string must be of type string!');
        }

        $this->setParameter($parameterIndex, $this->escape($string));
    }

    /**
     * Sets the designated parameter to the given string in a CMIS contains statement.
     *
     * Note that the CMIS specification requires two levels of escaping. The first level escapes ', ", \ characters
     * to \', \" and \\. The characters *, ? and - are interpreted as text search operators and are not escaped
     * on first level.
     * If *, ?, - shall be used as literals, they must be passed escaped with \*, \? and \- to this method.
     *
     * For all statements in a CONTAINS() clause it is required to isolate those from a query statement.
     * Therefore a second level escaping is performed. On the second level grammar ", ', - and \ are escaped with a \.
     * See the spec for further details.
     *
     * Summary (input --> first level escaping --> second level escaping and output):
     * * --> * --> *
     * ? --> ? --> ?
     * - --> - --> -
     * \ --> \\ --> \\\\
     * (for any other character following other than * ? -)
     * \* --> \* --> \\*
     * \? --> \? --> \\?
     * \- --> \- --> \\-
     * ' --> \' --> \\\'
     * " --> \" --> \\\"
     *
     * @param integer $parameterIndex the parameter index (one-based)
     * @param string $string the CONTAINS string
     * @throws CmisInvalidArgumentException If given value is not a string
     */
    public function setStringContains($parameterIndex, $string)
    {
        if (!is_string($string)) {
            throw new CmisInvalidArgumentException('Parameter string must be of type string!');
        }

        $this->setParameter($parameterIndex, $this->escapeContains($string));
    }

    /**
     * Sets the designated parameter to the given string.
     * It does not escape backslashes ('\') in front of '%' and '_'.
     *
     * @param integer $parameterIndex the parameter index (one-based)
     * @param $string
     * @throws CmisInvalidArgumentException If given value is not a string
     */
    public function setStringLike($parameterIndex, $string)
    {
        if (!is_string($string)) {
            throw new CmisInvalidArgumentException('Parameter string must be of type string!');
        }

        $this->setParameter($parameterIndex, $this->escapeLike($string));
    }

    /**
     * Sets the designated parameter to the query name of the given type.
     *
     * @param integer $parameterIndex the parameter index (one-based)
     * @param ObjectTypeInterface $type the object type
     */
    public function setType($parameterIndex, ObjectTypeInterface $type)
    {
        $this->setParameter($parameterIndex, $this->escape($type->getQueryName()));
    }

    /**
     * Sets the designated parameter to the given value
     *
     * @param integer $parameterIndex
     * @param mixed $value
     * @throws CmisInvalidArgumentException If parameter index is not of type integer
     */
    protected function setParameter($parameterIndex, $value)
    {
        if (!is_int($parameterIndex)) {
            throw new CmisInvalidArgumentException('Parameter index must be of type integer!');
        }

        $this->parametersMap[$parameterIndex] = $value;
    }

    /**
     * Returns the query statement.
     *
     * @return string the query statement, not null
     */
    public function toQueryString()
    {
        $queryString = '';
        $inString = false;
        $parameterIndex = 0;
        $length = strlen($this->statement);

        for ($i=0; $i < $length; $i++) {
            $char = $this->statement{$i};
            if ($char === '\'') {
                if ($inString && $this->statement{max(0, $i-1)} === '\\') {
                    $inString = true;
                } else {
                    $inString = !$inString;
                }
                $queryString .= $char;
            } elseif ($char === '?' && !$inString) {
                $parameterIndex ++;
                $queryString .= $this->parametersMap[$parameterIndex];
            } else {
                $queryString .= $char;
            }
        }

        return $queryString;
    }

    /**
     * Escapes string for query
     *
     * @param $string
     * @return string
     */
    protected function escape($string)
    {
        return "'" . addcslashes($string, '\'\\') . "'";
    }

    /**
     * Escapes string, but not escapes backslashes ('\') in front of '%' and '_'.
     *
     * @param $string
     * @return string
     */
    protected function escapeLike($string)
    {
        $escapedString = addcslashes($string, '\'\\');
        $replace = array(
            '\\\\%' => '\\%',
            '\\\\_' => '\\_',
        );
        $escapedString = str_replace(array_keys($replace), array_values($replace), $escapedString);
        return "'" . $escapedString . "'";
    }

    /**
     * Escapes string, but not escapes backslashes ('\') in front of '*' and '?'.
     *
     * @param $string
     * @return string
     */
    protected function escapeContains($string)
    {
        $escapedString = addcslashes($string, '"\'\\');
        $replace = array(
            '\\\\*' => '\*',
            '\\\\?' => '\?',
        );
        $escapedString = str_replace(array_keys($replace), array_values($replace), $escapedString);
        return "'" . $escapedString . "'";
    }
}
