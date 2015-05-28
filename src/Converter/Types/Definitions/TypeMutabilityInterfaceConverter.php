<?php
namespace Dkd\PhpCmis\Converter\Types\Definitions;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Bindings\Browser\JSONConstants;
use Dkd\PhpCmis\Converter\Types\TypeConverterInterface;
use Dkd\PhpCmis\Definitions\TypeMutabilityInterface;
use Dkd\PhpCmis\Exception\CmisInvalidArgumentException;

/**
 * Convert a Type Mutability Object to a array representation
 */
class TypeMutabilityInterfaceConverter implements TypeConverterInterface
{
    /**
     * Convert given object to a scalar representation or an array of scalar values.
     *
     * @param TypeMutabilityInterface $object
     * @return boolean[] Array representation of object
     * @throws CmisInvalidArgumentException thrown if given object does not implement expected TypeMutabilityInterface
     */
    public static function convertToSimpleType($object)
    {
        if (!$object instanceof TypeMutabilityInterface) {
            throw new CmisInvalidArgumentException('Given object must be of type TypeMutabilityInterface');
        }

        $result = array();
        $result[JSONConstants::JSON_TYPE_TYPE_MUTABILITY_CREATE] = $object->canCreate();
        $result[JSONConstants::JSON_TYPE_TYPE_MUTABILITY_UPDATE] = $object->canUpdate();
        $result[JSONConstants::JSON_TYPE_TYPE_MUTABILITY_DELETE] = $object->canDelete();

        return $result;
    }
}
