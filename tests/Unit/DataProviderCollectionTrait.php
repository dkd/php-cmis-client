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
        return [
            [true, true],
            [true, 1],
            [true, '1'],
            [true, 'string'],
            [false, false],
            [false, 0],
            [false, '0'],
            [false, null]
        ];
    }

    /**
     * Data Provider that provides an expected integer representation and a value
     *
     * @return array
     */
    public function integerCastDataProvider()
    {
        return [
            [0, ''],
            [2, '2'],
            [0, null],
            [3, 3],
            [3, 3.2]
        ];
    }

    /**
     * Data Provider that provides an expected string representation and a value
     *
     * @return array
     */
    public function stringCastDataProvider()
    {
        return [
            ['', ''],
            ['foo', 'foo'],
            ['', null],
            ['3', 3],
            ['3.2', 3.2],
            ['1', true],
            ['', false]
        ];
    }

    /**
     * Data provider that provides a value for all PHP types expect resource
     *
     * @param \Closure $filter
     * @return array
     */
    public function allTypesDataProvider(\Closure $filter = null)
    {
        $values = [
            'string' => ['String'],
            'integer' => [1],
            'float' => [1.1],
            'boolean' => [true],
            'object' => [new \stdClass()],
            'array' => [[]],
            'null' => [null],
            'callable' => [
                function () {
                    return true;
                }
            ]
        ];

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
