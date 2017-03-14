<?php
namespace Dkd\PhpCmis\Converter;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use function array_filter;
use function array_map;
use Dkd\PhpCmis\Exception\CmisRuntimeException;

/**
 * An Abstract data converter that contains some basic converter methods
 */
abstract class AbstractDataConverter implements DataConverterInterface
{
    /**
     * Cast all array values to string
     *
     * @param array $source
     * @return array
     */
    protected function convertStringValues(array $source)
    {
        return array_map('strval', $source);
    }

    /**
     * Cast all array values to boolean
     *
     * @param array $source
     * @return array
     */
    protected function convertBooleanValues(array $source)
    {
        return array_map('boolval', $source);
    }

    /**
     * Cast all array values to integer
     *
     * @param array $source
     * @return array
     */
    protected function convertIntegerValues(array $source)
    {
        return array_map('intval', $source);
    }

    /**
     * Cast all array values to float
     *
     * @param array $source
     * @return array
     */
    protected function convertDecimalValues(array $source)
    {
        return array_map('floatval', $source);
    }

    /**
     * @param array $source
     * @return array
     */
    protected function convertDateTimeValues($source)
    {
        return array_map(
            [$this, 'convertDateTimeValue'],
            array_filter(
                (array) $source,
                function ($item) { return !empty($item); }
            )
        );
    }

    /**
     * @param mixed $source
     * @return \DateTime
     */
    protected function convertDateTimeValue($source)
    {
        if (is_int($source)) {
            $date = new \DateTime();
            // DateTimes are given in a Timestamp with milliseconds.
            // see http://docs.oasis-open.org/cmis/CMIS/v1.1/os/CMIS-v1.1-os.html#x1-5420004
            $date->setTimestamp($source / 1000);
        } elseif (PHP_INT_SIZE == 4 && is_double($source)) {
            //TODO: 32-bit - handle this specially?
            $date = new \DateTime();
            $date->setTimestamp($source / 1000);
        } elseif (is_string($source)) {
            try {
                $date = new \DateTime($source);
            } catch (\Exception $exception) {
                throw new CmisRuntimeException('Invalid property value: ' . $source, 1416296900, $exception);
            }
        } else {
            throw new CmisRuntimeException(
                'Invalid property value: ' . (is_scalar($source) ? $source : gettype($source)),
                1416296901
            );
        }

        return $date;
    }
}
