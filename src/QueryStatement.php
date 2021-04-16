<?php
namespace Dkd\PhpCmis;

/*
 * This file is part of php-cmis-client.
 *
 * (c) Dimitri Ebert <dimitri.ebert@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Data\ObjectIdInterface;
use Dkd\PhpCmis\Data\ObjectTypeInterface;
use Dkd\PhpCmis\Definitions\PropertyDefinitionInterface;
use Dkd\PhpCmis\Definitions\TypeDefinitionInterface;
use Dkd\PhpCmis\Exception\CmisInvalidArgumentException;
use Dkd\PhpCmis\Exception\CmisObjectNotFoundException;

/**
 * Query Statement
 *
 * Prepares a query statement based on either a manually supplied
 * statement or one generated from supplied property list, type
 * list, clause and ordering.
 *
 * Used with a manual statement:
 *
 * $statement = new QueryStatement($session, 'SELECT ...');
 *
 * Used with property, type lists, clause and ordering:
 *
 * $statement = new QueryStatement(
 *     $session,
 *     NULL,
 *     array('prop1', 'prop2'),
 *     array('type1', 'type2'),
 *     'prop1 = type1.foobar',
 *     array('prop1 ASC')
 * );
 *
 * Note that this is an approximation of the OpenCMIS Java implementation:
 * Java allows multiple constructors but PHP does not; allowing additional
 * constructor arguments and making the manual statement optional makes it
 * possible to construct instances in nearly the same way as in Java. It's
 * close, but not exactly the same - however, when used through the public
 * APIs (Session->createQueryStatement) there is no difference in behavior.
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
    protected $parametersMap = [];

    /**
     * Creates a prepared statement for querying the CMIS repository. Requires
     * at least the Session as parameter, then accepts either a manual statement
     * or a list of property IDs, type IDs, a where clause and orderings which
     * will then generate a prepared statement based on those values.
     *
     * See also main class desciption.
     *
     * @param SessionInterface $session The initialized Session for communicating
     * @param string $statement Optional, manually prepared statement. If provided,
     *      excludes the use of property list, type list, where clause and ordering.
     * @param array $selectPropertyIds An array PropertyDefinitionInterface
     *      or strings, can be mixed. When strings are provided those can be
     *      either the actual ID of the property or the query name thereof.
     * @param array $fromTypes An array of TypeDefinitionInterface or strings,
     *      can be mixed. When strings are provided those can be either the
     *      actual ID of the type, or it can be the query name thereof. If
     *      an array of arrays is provided, each array is expected to contain
     *      a TypeDefinition or string as first member and an alias as second.
     * @param string|null $whereClause If searching by custom clause, provide here.
     * @param array $orderByPropertyIds List of property IDs by which to sort.
     *      Each value can be either a PropertyDefinitionInterface instance,
     *      a string (in which case, ID or queryName) or an array of a string
     *      or PropertyDefinition as first member and ASC or DESC as second.
     *      E.g. valid strings: "cm:title ASC", "cm:title", "P:cm:title".
     *      Valid arrays: [PropertyDefinitionInterface, "ASC"], ["cm:title", "ASC"]
     * @throws CmisInvalidArgumentException
     */
    public function __construct(
        SessionInterface $session,
        $statement = null,
        array $selectPropertyIds = [],
        array $fromTypes = [],
        $whereClause = null,
        array $orderByPropertyIds = []
    ) {
        $this->session = $session;
        $statementString = trim((string) $statement);

        if (empty($statementString)) {
            if (empty($selectPropertyIds)) {
                throw new CmisInvalidArgumentException(
                    'Statement was empty so property list must not be empty!',
                    1441286811
                );
            }
            if (empty($fromTypes)) {
                throw new CmisInvalidArgumentException(
                    'Statement was empty so types list must not be empty!',
                    1441286812
                );
            }
            $statementString = $this->generateStatementFromPropertiesAndTypesLists(
                $selectPropertyIds,
                $fromTypes,
                $whereClause,
                $orderByPropertyIds
            );
        } else {
            if (!empty($selectPropertyIds)) {
                throw new CmisInvalidArgumentException(
                    'Manual statement cannot be used when properties are used',
                    1441286813
                );
            }
            if (!empty($fromTypes)) {
                throw new CmisInvalidArgumentException(
                    'Manual statement cannot be used when types are used',
                    1441286814
                );
            }
            if (!empty($whereClause)) {
                throw new CmisInvalidArgumentException(
                    'Manual statement cannot be used when clause is used',
                    1441286815
                );
            }
            if (!empty($orderByPropertyIds)) {
                throw new CmisInvalidArgumentException(
                    'Manual statement cannot be used when orderings are used',
                    1441286816
                );
            }
        }

        $this->statement = $statementString;
    }

    /**
     * Generates a statement based on input criteria, with the necessary
     * JOINs in place for selecting attributes related to all provided types.
     *
     * @param array $selectPropertyIds An array PropertyDefinitionInterface
     *      or strings, can be mixed. When strings are provided those can be
     *      either the actual ID of the property or the query name thereof.
     * @param array $fromTypes An array of TypeDefinitionInterface or strings,
     *      can be mixed. When strings are provided those can be either the
     *      actual ID of the type, or it can be the query name thereof. If
     *      an array of arrays is provided, each array is expected to contain
     *      a TypeDefinition or string as first member and an alias as second.
     * @param string|null $whereClause If searching by custom clause, provide here.
     * @param array $orderByPropertyIds List of property IDs by which to sort.
     *      Each value can be either a PropertyDefinitionInterface instance,
     *      a string (in which case, ID or queryName) or an array of a string
     *      or PropertyDefinition as first member and ASC or DESC as second.
     *      E.g. valid strings: "cm:title ASC", "cm:title", "P:cm:title".
     *      Valid arrays: [PropertyDefinitionInterface, "ASC"], ["cm:title", "ASC"]
     * @return string
     */
    protected function generateStatementFromPropertiesAndTypesLists(
        array $selectPropertyIds,
        array $fromTypes,
        $whereClause,
        array $orderByPropertyIds
    ) {
        $statementString = 'SELECT ' . $this->generateStatementPropertyList($selectPropertyIds, false);

        $primaryTable = array_shift($fromTypes);
        list ($primaryTableQueryName, $primaryAlias) = $this->getQueryNameAndAliasForType($primaryTable, 'primary');

        $statementString .= ' FROM ' . $primaryTableQueryName . ' ' . $primaryAlias;

        while (count($fromTypes) > 0) {
            $secondaryTable = array_shift($fromTypes);
            /*
             * we build an automatic alias here, a simple one-byte ASCII value
             * generated based on remaining tables. If 26 tables remain, a "z"
             * is generated. If 1 table remains, an "a" is generated. The alias
             * is, unfortunately, required for the JOIN to work correctly. It
             * only gets used if the type string does not contain an alias.
             */
            $alias = chr(97 + count($fromTypes));
            list ($secondaryTableQueryName, $alias) = $this->getQueryNameAndAliasForType($secondaryTable, $alias);
            $statementString .= ' JOIN ' . $secondaryTableQueryName . ' AS ' . $alias .
                ' ON ' . $primaryAlias . '.cmis:objectId = ' . $alias . '.cmis:objectId';
        }

        if (trim((string) $whereClause)) {
            $statementString .= ' WHERE ' . trim($whereClause);
        }

        if (!empty($orderByPropertyIds)) {
            $statementString .= ' ORDER BY ' . $this->generateStatementPropertyList($orderByPropertyIds, true);
        }
        return trim($statementString);
    }

    /**
     * Translates a TypeDefinition or string into a query name for
     * that TypeDefinition. Returns the input string as fallback if
     * the type could not be resolved. Input may contain an alias,
     * if so, we split and preserve the alias but attempt to translate
     * the type ID part.
     *
     * @param mixed $typeDefinitionMixed Input describing the type
     * @param string $autoAlias If alias is not provided
     * @return array
     */
    protected function getQueryNameAndAliasForType($typeDefinitionMixed, $autoAlias)
    {
        $alias = null;
        if (is_array($typeDefinitionMixed)) {
            list ($typeDefinitionMixed, $alias) = $typeDefinitionMixed;
        }
        if ($typeDefinitionMixed instanceof TypeDefinitionInterface) {
            $queryName = $typeDefinitionMixed->getQueryName();
        } elseif (is_string($typeDefinitionMixed) && strpos($typeDefinitionMixed, ' ')) {
            list ($typeDefinitionMixed, $alias) = explode(' ', $typeDefinitionMixed, 2);
        }
        try {
            $queryName = $this->session->getTypeDefinition($typeDefinitionMixed)->getQueryName();
        } catch (CmisObjectNotFoundException $error) {
            $queryName = $typeDefinitionMixed;
        }
        return [$queryName, ($alias ? $alias : $autoAlias)];
    }

    /**
     * Renders a statement-compatible string of property selections,
     * with ordering support if $withOrdering is true. Input properties
     * can be an array of strings, an array of PropertyDefinition, or
     * when $withOrdering is true, an array of arrays each containing
     * a string or PropertyDefinition plus ASC or DESC as second value.
     *
     * @param array $properties
     * @param boolean $withOrdering
     * @return string
     */
    protected function generateStatementPropertyList(array $properties, $withOrdering)
    {
        $statement = [];
        foreach ($properties as $property) {
            $ordering = ($withOrdering ? 'ASC' : '');
            if ($withOrdering) {
                if (is_array($property)) {
                    list ($property, $ordering) = $property;
                } elseif (is_string($property) && strpos($property, ' ')) {
                    list ($property, $ordering) = explode(' ', $property, 2);
                }
            }
            if ($property instanceof PropertyDefinitionInterface) {
                $propertyQueryName = $property->getQueryName();
            } else {
                $propertyQueryName = $property;
            }
            $statement[] = rtrim($propertyQueryName . ' ' . $ordering);
        }
        return implode(', ', $statement);
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
            $char = $this->statement[$i];
            if ($char === '\'') {
                if ($inString && $this->statement[max(0, $i-1)] === '\\') {
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
        $replace = [
            '\\\\%' => '\\%',
            '\\\\_' => '\\_',
        ];
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
        $replace = [
            '\\\\*' => '\*',
            '\\\\?' => '\?',
        ];
        $escapedString = str_replace(array_keys($replace), array_values($replace), $escapedString);
        return "'" . $escapedString . "'";
    }
}
