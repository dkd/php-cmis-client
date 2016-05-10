<?php
namespace Dkd\PhpCmis\Traits;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Exception\CmisInvalidArgumentException;

/**
 * Trait with some type check related functions
 */
trait TypeHelperTrait
{
    /**
     * Check if the given value is the expected object type
     *
     * @param string $expectedType the expected object type (class name)
     * @param mixed $value The value that has to be checked
     * @param boolean $nullAllowed Is <code>null</code> allowed as value?
     * @return boolean returns <code>true</code> if the given value is instance of expected type
     * @throws CmisInvalidArgumentException Exception is thrown if the given value does not match to the expected type
     */
    protected function checkType($expectedType, $value, $nullAllowed = false)
    {
        $invalidType = null;
        $valueType = gettype($value);
        $nullAllowed = (boolean) $nullAllowed;

        if ($valueType === 'object') {
            if (!is_a($value, $expectedType)) {
                $invalidType = get_class($value);
            }
        } elseif ($expectedType !== $valueType) {
            $invalidType = $valueType;
        }

        if ($invalidType !== null && ($nullAllowed === false || ($nullAllowed === true && $value !== null))) {
            throw new CmisInvalidArgumentException(
                sprintf(
                    'Argument of type "%s" given but argument of type "%s" was expected.',
                    $invalidType,
                    $expectedType
                ),
                1413440336
            );
        }

        return true;
    }

    /**
     * Ensure that a value is an instance of the expected type. If not the value
     * is casted to the expected type and a log message is triggered.
     *
     * @param string $expectedType the expected object type (class name)
     * @param mixed $value The value that has to be checked
     * @param boolean $nullIsValidValue defines if <code>null</code> is also a valid value
     * @return mixed
     */
    protected function castValueToSimpleType($expectedType, $value, $nullIsValidValue = false)
    {
        try {
            $this->checkType($expectedType, $value, $nullIsValidValue);
        } catch (CmisInvalidArgumentException $exception) {
            if (PHP_INT_SIZE == 4 && $expectedType == 'integer' && is_double($value)) {
                //TODO: 32bit - handle this specially?
                settype($value, $expectedType);
            } else {
                trigger_error(
                    sprintf(
                        'Given value is of type "%s" but a value of type "%s" was expected.'
                        . ' Value has been casted to the expected type.',
                        gettype($value),
                        $expectedType
                    ),
                    E_USER_NOTICE
                );
                settype($value, $expectedType);
            }
        }

        return $value;
    }
}
