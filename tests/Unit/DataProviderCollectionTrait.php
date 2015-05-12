<?php
namespace Dkd\PhpCmis\Test\Unit;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

trait DataProviderCollectionTrait
{
    /**
     * Data Provider that provides an expected boolean representation and a value
     *
     * @return array
     */
    public function booleanCastDataProvider()
    {
        return array(
            array(true, true),
            array(true, 1),
            array(true, '1'),
            array(true, 'string'),
            array(false, false),
            array(false, 0),
            array(false, '0'),
            array(false, null)
        );
    }

    /**
     * Data Provider that provides an expected integer representation and a value
     *
     * @return array
     */
    public function integerCastDataProvider()
    {
        return array(
            array(0, ''),
            array(2, '2'),
            array(0, null),
            array(3, 3),
            array(3, 3.2)
        );
    }

    /**
     * Data Provider that provides an expected string representation and a value
     *
     * @return array
     */
    public function stringCastDataProvider()
    {
        return array(
            array('', ''),
            array('foo', 'foo'),
            array('', null),
            array('3', 3),
            array('3.2', 3.2),
            array('1', true),
            array('', false)
        );
    }

    /**
     * Data provider that provides a value for all PHP types expect resource
     *
     * @param \Closure $filter
     * @return array
     */
    public function allTypesDataProvider(\Closure $filter = null)
    {
        $values = array(
            'string' => array('String'),
            'integer' => array(1),
            'float' => array(1.1),
            'boolean' => array(true),
            'object' => array(new \stdClass()),
            'array' => array(array()),
            'null' => array(null),
            'callable' => array(
                function () {
                    return true;
                }
            )
        );

        if ($filter !== null) {
            $values = array_filter($values, $filter);
        }

        return $values;
    }

    /**
     * Data provider that provides a value for all PHP types expect resource and integer
     *
     * @return array
     */
    public function nonIntegerDataProvider()
    {
        return $this->allTypesDataProvider(
            function ($value) {
                return !is_int(reset($value));
            }
        );
    }

    /**
     * Data provider that provides a value for all PHP types expect resource and string
     *
     * @return array
     */
    public function nonStringDataProvider()
    {
        return $this->allTypesDataProvider(
            function ($value) {
                return !is_string(reset($value));
            }
        );
    }

    /**
     * Data provider that provides a value for all PHP types expect resource and boolean
     *
     * @return array
     */
    public function nonBooleanDataProvider()
    {
        return $this->allTypesDataProvider(
            function ($value) {
                return !is_bool(reset($value));
            }
        );
    }

    /**
     * Data provider that provides a value for all PHP types expect resource and array
     *
     * @return array
     */
    public function nonArrayDataProvider()
    {
        return $this->allTypesDataProvider(
            function ($value) {
                return !is_array(reset($value));
            }
        );
    }

    /**
     * Data provider that provides a value for all PHP types expect resource and float
     *
     * @return array
     */
    public function nonFloatDataProvider()
    {
        return $this->allTypesDataProvider(
            function ($value) {
                return !is_float(reset($value));
            }
        );
    }

    /**
     * Data provider that provides a value for all PHP types expect resource and object
     *
     * @return array
     */
    public function nonObjectDataProvider()
    {
        return $this->allTypesDataProvider(
            function ($value) {
                return !is_object(reset($value));
            }
        );
    }

    /**
     * Data provider that provides a value for all PHP types expect resource and callable
     *
     * @return array
     */
    public function nonCallableDataProvider()
    {
        return $this->allTypesDataProvider(
            function ($value) {
                return !is_callable(reset($value));
            }
        );
    }
}
