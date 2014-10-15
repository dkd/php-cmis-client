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
            array(false, null),
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
            array(3, 3.2),
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
        );
    }
}
