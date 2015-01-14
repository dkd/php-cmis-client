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

use Dkd\Enumeration\Exception\InvalidEnumerationValueException;
use Dkd\PhpCmis\Bindings\Browser\JSONConstants;
use Dkd\PhpCmis\Data\AclInterface;
use Dkd\PhpCmis\Data\AclCapabilitiesInterface;
use Dkd\PhpCmis\Data\AllowableActionsInterface;
use Dkd\PhpCmis\Data\BulkUpdateObjectIdAndChangeTokenInterface;
use Dkd\PhpCmis\Data\RepositoryInfoInterface;
use Dkd\PhpCmis\DataObjects\AccessControlEntry;
use Dkd\PhpCmis\DataObjects\AccessControlList;
use Dkd\PhpCmis\DataObjects\AclCapabilities;
use Dkd\PhpCmis\DataObjects\AllowableActions;
use Dkd\PhpCmis\DataObjects\ChangeEventInfo;
use Dkd\PhpCmis\DataObjects\CmisExtensionElement;
use Dkd\PhpCmis\DataObjects\CreatablePropertyTypes;
use Dkd\PhpCmis\DataObjects\DocumentTypeDefinition;
use Dkd\PhpCmis\DataObjects\ExtensionFeature;
use Dkd\PhpCmis\DataObjects\FolderTypeDefinition;
use Dkd\PhpCmis\DataObjects\ItemTypeDefinition;
use Dkd\PhpCmis\DataObjects\NewTypeSettableAttributes;
use Dkd\PhpCmis\DataObjects\ObjectData;
use Dkd\PhpCmis\DataObjects\PermissionDefinition;
use Dkd\PhpCmis\DataObjects\PermissionMapping;
use Dkd\PhpCmis\DataObjects\PolicyIdList;
use Dkd\PhpCmis\DataObjects\PolicyTypeDefinition;
use Dkd\PhpCmis\DataObjects\Principal;
use Dkd\PhpCmis\DataObjects\Properties;
use Dkd\PhpCmis\DataObjects\PropertyBoolean;
use Dkd\PhpCmis\DataObjects\PropertyBooleanDefinition;
use Dkd\PhpCmis\DataObjects\PropertyDateTime;
use Dkd\PhpCmis\DataObjects\PropertyDateTimeDefinition;
use Dkd\PhpCmis\DataObjects\PropertyDecimal;
use Dkd\PhpCmis\DataObjects\PropertyDecimalDefinition;
use Dkd\PhpCmis\DataObjects\PropertyHtml;
use Dkd\PhpCmis\DataObjects\PropertyHtmlDefinition;
use Dkd\PhpCmis\DataObjects\PropertyId;
use Dkd\PhpCmis\DataObjects\PropertyIdDefinition;
use Dkd\PhpCmis\DataObjects\PropertyInteger;
use Dkd\PhpCmis\DataObjects\PropertyIntegerDefinition;
use Dkd\PhpCmis\DataObjects\PropertyString;
use Dkd\PhpCmis\DataObjects\PropertyStringDefinition;
use Dkd\PhpCmis\DataObjects\PropertyUri;
use Dkd\PhpCmis\DataObjects\PropertyUriDefinition;
use Dkd\PhpCmis\DataObjects\RelationshipTypeDefinition;
use Dkd\PhpCmis\DataObjects\RenditionData;
use Dkd\PhpCmis\DataObjects\RepositoryCapabilities;
use Dkd\PhpCmis\DataObjects\RepositoryInfoBrowserBinding;
use Dkd\PhpCmis\DataObjects\SecondaryTypeDefinition;
use Dkd\PhpCmis\DataObjects\TypeMutability;
use Dkd\PhpCmis\Definitions\PropertyDefinitionInterface;
use Dkd\PhpCmis\Definitions\TypeDefinitionInterface;
use Dkd\PhpCmis\Enum\AclPropagation;
use Dkd\PhpCmis\Enum\Action;
use Dkd\PhpCmis\Enum\BaseTypeId;
use Dkd\PhpCmis\Enum\CapabilityAcl;
use Dkd\PhpCmis\Enum\CapabilityChanges;
use Dkd\PhpCmis\Enum\CapabilityContentStreamUpdates;
use Dkd\PhpCmis\Enum\CapabilityJoin;
use Dkd\PhpCmis\Enum\CapabilityOrderBy;
use Dkd\PhpCmis\Enum\CapabilityQuery;
use Dkd\PhpCmis\Enum\CapabilityRenditions;
use Dkd\PhpCmis\Enum\Cardinality;
use Dkd\PhpCmis\Enum\ChangeType;
use Dkd\PhpCmis\Enum\CmisVersion;
use Dkd\PhpCmis\Enum\ContentStreamAllowed;
use Dkd\PhpCmis\Enum\DateTimeResolution;
use Dkd\PhpCmis\Enum\DecimalPrecision;
use Dkd\PhpCmis\Enum\PropertyType;
use Dkd\PhpCmis\Enum\SupportedPermissions;
use Dkd\PhpCmis\Enum\Updatability;
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
        $result = array();
        // we can't use array_map with boolval here because boolval is only available in php >= 5.5
        foreach ($source as $item) {
            $result[] = (boolean) $item;
        }
        return $result;
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
        $result = array();

        if (is_array($source) && count($source) > 0) {
            foreach ($source as $item) {
                if (!empty($item)) {
                    $result[] = $this->convertDateTimeValue($item);
                }
            }
        }

        return $result;
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
