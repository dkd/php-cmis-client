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
            $this->checkObjectType('\\Dkd\\PhpCmis\\Data\\CmisExtensionElementInterface', $extension);
        }

        $this->extensions = $extensions;
    }

    /**
     * Check if the given value is the expected object type
     *
     * @param string $expectedType the expected object type (class name)
     * @param mixed $value The value that has to be checked
     */
    protected function checkObjectType($expectedType, $value)
    {
        $type = null;
        if (is_object($value)) {
            if (!is_a($value, $expectedType)) {
                $type = get_class($value);
            }
        } else {
            $type = gettype($value);
        }

        if ($type !== null) {
            throw new \InvalidArgumentException(
                sprintf('Argument of type "%s" given but argument of type "%s" was expected.', $type, $expectedType)
            );
        }
    }
}
