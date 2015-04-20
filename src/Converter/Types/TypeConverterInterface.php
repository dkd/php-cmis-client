<?php
namespace Dkd\PhpCmis\Converter\Types;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Type converter interface
 */
interface TypeConverterInterface
{
    /**
     * Convert given object to a scalar representation or an array of scalar values.
     *
     * @param $object
     * @return mixed Array / Scalar representation of object
     */
    public static function convertToSimpleType($object);
}
