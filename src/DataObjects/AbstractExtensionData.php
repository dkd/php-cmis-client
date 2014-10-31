<?php
namespace Dkd\PhpCmis\DataObjects;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Data\CmisExtensionElementInterface;
use Dkd\PhpCmis\Data\ExtensionDataInterface;
use Dkd\PhpCmis\Exception\CmisInvalidArgumentException;

/**
 * Holds extension data either set by the CMIS repository or the client.
 */
abstract class AbstractExtensionData implements ExtensionDataInterface
{
    /**
     * @var CmisExtensionElementInterface[]
     */
    protected $extensions;

    /**
     * Returns the list of top-level extension elements.
     *
     * @return CmisExtensionElementInterface[]
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Sets the list of top-level extension elements.
     *
     * @param CmisExtensionElementInterface[] $extensions
     * @return void
     */
    public function setExtensions(array $extensions)
    {
        foreach ($extensions as $extension) {
            $this->checkType('\\Dkd\\PhpCmis\\Data\\CmisExtensionElementInterface', $extension);
        }

        $this->extensions = $extensions;
    }

    /**
     * Check if the given value is the expected object type
     *
     * @param string $expectedType the expected object type (class name)
     * @param mixed $value The value that has to be checked
     * @param boolean $nullAllowed Is null allowed as value?
     * @return boolean returns true if the given value is instance of expected type
     */
    protected function checkType($expectedType, $value, $nullAllowed = false)
    {
        $invalidType = null;
        $valueType = gettype($value);

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
     * @param $expectedType
     * @param $value
     * @param boolean $nullIsValidValue defines if <code>null</code> is also a valid value
     * @return mixed
     */
    protected function castValueToSimpleType($expectedType, $value, $nullIsValidValue = false)
    {
        try {
            $this->checkType($expectedType, $value, $nullIsValidValue);
        } catch (CmisInvalidArgumentException $exception) {
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

        return $value;
    }
}
