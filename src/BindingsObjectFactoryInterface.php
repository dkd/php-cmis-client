<?php
namespace Dkd\PhpCmis;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use GuzzleHttp\Stream\StreamInterface;
use Dkd\PhpCmis\Data\PropertiesInterface;
use Dkd\PhpCmis\Data\PropertyBooleanInterface;
use Dkd\PhpCmis\Data\PropertyDataInterface;
use Dkd\PhpCmis\Data\PropertyDateTimeInterface;
use Dkd\PhpCmis\Data\PropertyDecimalInterface;
use Dkd\PhpCmis\Data\PropertyHtmlInterface;
use Dkd\PhpCmis\Data\PropertyIdInterface;
use Dkd\PhpCmis\Data\PropertyIntegerInterface;
use Dkd\PhpCmis\Data\PropertyStringInterface;
use Dkd\PhpCmis\Data\PropertyUriInterface;
use Dkd\PhpCmis\Definitions\PropertyDefinitionInterface;

/**
 * Factory for CMIS binding objects.
 */
interface BindingsObjectFactoryInterface
{
    /**
     * @param string $principal
     * @param string[] $permissions
     * @return AceInterface
     */
    public function createAccessControlEntry($principal, $permissions);

    /**
     * @param AceInterface[] $aces
     * @return AclInterface
     */
    public function createAccessControlList(array $aces);

    /**
     * @param string $filename
     * @param int $length
     * @param string $mimeType
     * @return StreamInterface
     */
    public function createContentStream($filename, $length, $mimeType);

    /**
     * @param PropertyDataInterface[] $properties
     * @return PropertiesInterface
     */
    public function createPropertiesData(array $properties);

    /**
     * @param string $id
     * @param boolean $value
     * @return PropertyBooleanInterface
     */
    public function createPropertyBooleanData($id, $value);

    /**
     * @param PropertyDefinitionInterface $pd
     * @param \stdClass $value
     * @return PropertyDataInterface
     */
    public function createPropertyData($pd, $value);

    /**
     * @param string $id
     * @param \DateTime $value
     * @return PropertyDateTimeInterface
     */
    public function createPropertyDateTimeData($id, $value);

    /**
     * @param string $id
     * @param int $value
     * @return PropertyDecimalInterface
     */
    public function createPropertyDecimalData($id, $value);

    /**
     * @param string $id
     * @param string $value
     * @return PropertyHtmlInterface
     */
    public function createPropertyHtmlData($id, $value);

    /**
     * @param string $id
     * @param string $value
     * @return PropertyIdInterface
     */
    public function createPropertyIdData($id, $value);

    /**
     * @param string $id
     * @param int $value
     * @return PropertyIntegerInterface
     */
    public function createPropertyIntegerData($id, $value);

    /**
     * @param string $id
     * @param string $value
     * @return PropertyStringInterface
     */
    public function createPropertyStringData($id, $value);

    /**
     * @param string $id
     * @param string $value
     * @return PropertyUriInterface
     */
    public function createPropertyUriData($id, $value);
}
