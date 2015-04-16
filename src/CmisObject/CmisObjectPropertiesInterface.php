<?php
namespace Dkd\PhpCmis\CmisObject;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Data\ObjectTypeInterface;
use Dkd\PhpCmis\Data\SecondaryTypeInterface;
use Dkd\PhpCmis\Enum\BaseTypeId;
use Dkd\PhpCmis\Data\PropertyInterface;

/**
 * Accessors to CMIS object properties.
 *
 * A property might not be available because either the repository didn't provide
 * it or a property filter was used to retrieve this object.
 *
 * The property values represent a snapshot of the object when it was loaded.
 * The object and its properties may be out-of-date if the object has been modified in the repository.
 *
 */
interface CmisObjectPropertiesInterface
{
    /**
     * Returns a list of primary and secondary object types that define the given property.
     *
     * @param string $id the ID of the property
     * @return ObjectTypeInterface[]|null a list of object types that define the given property or <code>null</code>
     *         if the property couldn't be found in the object types that are attached to this object
     */
    public function findObjectType($id);

    /**
     * Returns the type of this CMIS object (object type identified by <code>cmis:objectTypeId</code>).
     *
     * @return ObjectTypeInterface the type of the object or <code>null</code> if the property
     *         <code>cmis:objectTypeId</code> hasn't been requested or hasn't been provided by the repository
     */
    public function getType();

    /**
     * Returns the base type of this CMIS object (object type identified by cmis:baseTypeId).
     *
     * @return ObjectTypeInterface the base type of the object or <code>null</code> if the property cmis:baseTypeId
     *         hasn't been requested or hasn't been provided by the repository
     */
    public function getBaseType();

    /**
     * Returns the base type of this CMIS object (object type identified by cmis:baseTypeId).
     *
     * @return BaseTypeId|null the base type of the object or <code>null</code> if the property
     *         cmis:baseTypeId hasn't been requested or hasn't been provided by the repository
     */
    public function getBaseTypeId();

    /**
     * Returns the change token (CMIS property cmis:changeToken).
     *
     * @return string the change token of the object or <code>null</code> if the property hasn't been requested or
     *         hasn't been provided or isn't supported by the repository
     */
    public function getChangeToken();

    /**
     * Returns the user who created this CMIS object (CMIS property cmis:createdBy).
     *
     * @return string the creator of the object or <code>null</code> if the property hasn't been requested or hasn't
     *         been provided by the repository
     */
    public function getCreatedBy();

    /**
     * Returns the timestamp when this CMIS object has been created (CMIS property cmis:creationDate).
     *
     * @return \DateTime|null the creation time of the object or <code>null</code> if the property hasn't been
     *         requested or hasn't been provided by the repository
     */
    public function getCreationDate();

    /**
     * Returns the description of this CMIS object (CMIS property cmis:description).
     *
     * @return string|null the description of the object or <code>null</code> if the property hasn't been requested,
     *         hasn't been provided by the repository, or the property value isn't set
     */
    public function getDescription();

    /**
     * Returns the timestamp when this CMIS object has been modified (CMIS property cmis:lastModificationDate).
     *
     * @return \DateTime|null the last modification date of the object or <code>null</code> if the property hasn't been
     *         requested or hasn't been provided by the repository
     */
    public function getLastModificationDate();

    /**
     * Returns the user who modified this CMIS object (CMIS property cmis:lastModifiedBy).
     *
     * @return string|null the last modifier of the object or <code>null</code> if the property hasn't
     *         been requested or hasn't been provided by the repository
     */
    public function getLastModifiedBy();

    /**
     * Returns the name of this CMIS object (CMIS property cmis:name).
     *
     * @return string|null the name of the object or <code>null</code> if the property hasn't been requested
     *         or hasn't been provided by the repository
     */
    public function getName();

    /**
     * Returns a list of all available CMIS properties.
     *
     * @return PropertyInterface[] all available CMIS properties
     */
    public function getProperties();

    /**
     * Returns a property.
     *
     * @param string $id the ID of the property
     * @return PropertyInterface|null the property or <code>null</code> if the property hasn't been requested or
     *         hasn't been provided by the repository
     */
    public function getProperty($id);

    /**
     * Returns the value of a property.
     *
     * @param string $id the ID of the property
     * @return mixed the property value or <code>null</code> if the property hasn't been requested,
     *         hasn't been provided by the repository, or the property value isn't set
     */
    public function getPropertyValue($id);

    /**
     * Returns the secondary types of this CMIS object (object types identified by cmis:secondaryObjectTypeIds).
     *
     * @return SecondaryTypeInterface[]|null the secondary types of the object or <code>null</code> if the property
     *         cmis:secondaryObjectTypeIds hasn't been requested or hasn't been provided by the repository
     */
    public function getSecondaryTypes();
}
