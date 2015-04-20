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

use Dkd\PhpCmis\Data\ObjectIdInterface;
use Dkd\PhpCmis\Data\ObjectTypeInterface;
use Dkd\PhpCmis\Definitions\PropertyDefinitionInterface;

/**
 * Query Statement.
 */
interface QueryStatementInterface
{
    /**
     * Executes the query.
     *
     * @param boolean $searchAllVersions <code>true</code> if all document versions should be included in the search
     *      results, <code>false</code> if only the latest document versions should be included in the search results
     * @param OperationContextInterface|null $context the operation context to use
     * @return QueryResultInterface[]
     */
    public function query($searchAllVersions, OperationContextInterface $context = null);

    /**
     * Sets the designated parameter to the given boolean.
     *
     * @param integer $parameterIndex the parameter index (one-based)
     * @param boolean $bool the boolean
     */
    public function setBoolean($parameterIndex, $bool);

    /**
     * Sets the designated parameter to the given DateTime value.
     *
     * @param integer $parameterIndex the parameter index (one-based)
     * @param \DateTime $dateTime the DateTime value as DateTime object
     */
    public function setDateTime($parameterIndex, \DateTime $dateTime);

    /**
     * Sets the designated parameter to the given DateTime value with the prefix 'TIMESTAMP '.
     *
     * @param integer $parameterIndex the parameter index (one-based)
     * @param \DateTime $dateTime the DateTime value as DateTime object
     */
    public function setDateTimeTimestamp($parameterIndex, \DateTime $dateTime);

    /**
     * Sets the designated parameter to the given object ID.
     *
     * @param integer $parameterIndex the parameter index (one-based)
     * @param ObjectIdInterface $id the object ID
     */
    public function setId($parameterIndex, ObjectIdInterface $id);

    /**
     * Sets the designated parameter to the given number.
     *
     * @param integer $parameterIndex the parameter index (one-based)
     * @param integer $number the value to be set as number
     */
    public function setNumber($parameterIndex, $number);

    /**
     * Sets the designated parameter to the query name of the given property.
     *
     * @param integer $parameterIndex the parameter index (one-based)
     * @param PropertyDefinitionInterface $propertyDefinition
     */
    public function setProperty($parameterIndex, PropertyDefinitionInterface $propertyDefinition);

    /**
     * Sets the designated parameter to the given string.
     *
     * @param integer $parameterIndex the parameter index (one-based)
     * @param string $string the string
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
     * @param integer $parameterIndex the parameter index (one-based)
     * @param string $string the CONTAINS string
     */
    public function setStringContains($parameterIndex, $string);

    /**
     * Sets the designated parameter to the given string.
     * It does not escape backslashes ('\') in front of '%' and '_'.
     *
     * @param integer $parameterIndex the parameter index (one-based)
     * @param $string
     */
    public function setStringLike($parameterIndex, $string);

    /**
     * Sets the designated parameter to the query name of the given type.
     *
     * @param integer $parameterIndex the parameter index (one-based)
     * @param ObjectTypeInterface $type the object type
     */
    public function setType($parameterIndex, ObjectTypeInterface $type);

    /**
     * Returns the query statement.
     *
     * @return string the query statement, not null
     */
    public function toQueryString();
}
