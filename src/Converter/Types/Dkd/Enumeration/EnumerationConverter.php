<?php
namespace Dkd\PhpCmis\Converter\Types\Dkd\Enumeration;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\Enumeration\Enumeration;
use Dkd\PhpCmis\Converter\Types\TypeConverterInterface;
use Dkd\PhpCmis\Exception\CmisInvalidArgumentException;

/**
 * Convert a Enumeration Object to a string representation
 */
class EnumerationConverter implements TypeConverterInterface
{
    /**
     * Convert given object to a scalar representation or an array of scalar values.
     *
     * @param Enumeration $object
     * @return string String representation of Enumeration value
     * @throws CmisInvalidArgumentException is thrown if given object does not implement expected Enumeration interface
     */
    public static function convertToSimpleType($object)
    {
        if (!$object instanceof Enumeration) {
            throw new CmisInvalidArgumentException('Given object must be of type Enumeration');
        }

        return (string) $object;
    }
}
