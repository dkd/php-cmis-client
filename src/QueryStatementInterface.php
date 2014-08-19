<?php
namespace Dkd\PhpCmis;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Definitions\PropertyDefinitionInterface;

/**
 * Query Statement.
 */
interface QueryStatementInterface
{
    /**
     * Executes the query.
     *
     * @param boolean $searchAllVersions  true if all document versions should be included in the search results,
     * false if only the latest document versions should be included in the search results
     * @param OperationContextInterface $context the operation context to use
     * @return QueryResultInterface[]
     */
    public function query($searchAllVersions, OperationContextInterface $context = null);

    /**
     * Sets the designated parameter to the given boolean.
     *
     * @param int $parameterIndex the parameter index (one-based)
     * @param boolean $bool the boolean
     * @return void
     */
    public function setBoolean($parameterIndex, $bool);

    /**
     * Sets the designated parameter to the given DateTime value.
     *
     * @param int $parameterIndex the parameter index (one-based)
     * @param \DateTime $dateTime the DateTime value as DateTime object
     * @return void
     */
    public function setDateTime($parameterIndex, $dateTime);

    /**
     * Sets the designated parameter to the given DateTime value with the prefix 'TIMESTAMP '.
     *
     * @param int $parameterIndex the parameter index (one-based)
     * @param \DateTime $dateTime the DateTime value as DateTime object
     * @return void
     */
    public function setDateTimeTimestamp($parameterIndex, $dateTime);

    /**
     * Sets the designated parameter to the given object ID.
     *
     * @param int $parameterIndex the parameter index (one-based)
     * @param ObjectIdInterface $id the object ID
     * @return void
     */
    public function setId($parameterIndex, ObjectIdInterface $id);

    /**
     * Sets the designated parameter to the given number.
     *
     * @param int $parameterIndex the parameter index (one-based)
     * @param int $number the number
     * @return void
     */
    public function setNumber($parameterIndex, $number);

    /**
     * Sets the designated parameter to the query name of the given property.
     *
     * @param int $parameterIndex the parameter index (one-based)
     * @param PropertyDefinitionInterface $propertyDefinition
     * @return void
     */
    public function setProperty($parameterIndex, PropertyDefinitionInterface $propertyDefinition);

    /**
     * Sets the designated parameter to the given string.
     *
     * @param int $parameterIndex the parameter index (one-based)
     * @param string $string the string
     * @return void
     */
    public function setString($parameterIndex, $string);

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
     * --> * --> *
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
     * @param int $parameterIndex the parameter index (one-based)
     * @param string $string the CONTAINS string
     * @return void
     */
    public function setStringContains($parameterIndex, $string);

    /**
     * Sets the designated parameter to the given string.
     * It does not escape backslashes ('\') in front of '%' and '_'.
     *
     * @param int $parameterIndex the parameter index (one-based)
     * @param $string
     * @return void
     */
    public function setStringLike($parameterIndex, $string);

    /**
     * Sets the designated parameter to the query name of the given type.
     *
     * @param int $parameterIndex the parameter index (one-based)
     * @param ObjectTypeInterface $type the object type
     * @return void
     */
    public function setType($parameterIndex, ObjectTypeInterface $type);

    /**
     * Sets the designated parameter to the given URI.
     *
     * @param int $parameterIndex the parameter index (one-based)
     * @param string $uri the URI
     * @return void
     */
    public function setUri($parameterIndex, $uri);

    /**
     * Sets the designated parameter to the given URL.
     *
     * @param int $parameterIndex the parameter index (one-based)
     * @param string $url the URL
     * @return mixed
     */
    public function setUrl($parameterIndex, $url);

    /**
     * Returns the query statement.
     *
     * @return string the query statement, not null
     */
    public function toQueryString();
}
