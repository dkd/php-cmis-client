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

use Dkd\Enumeration\Exception\InvalidEnumerationValueException;
use Dkd\PhpCmis\Data\AclInterface;
use Dkd\PhpCmis\Data\AllowableActionsInterface;
use Dkd\PhpCmis\Data\ChangeEventInfoInterface;
use Dkd\PhpCmis\Data\ObjectDataInterface;
use Dkd\PhpCmis\Data\PolicyIdListInterface;
use Dkd\PhpCmis\Data\PropertiesInterface;
use Dkd\PhpCmis\Data\RenditionDataInterface;
use Dkd\PhpCmis\Enum\BaseTypeId;
use Dkd\PhpCmis\PropertyIds;

/**
 * ObjectData implementation.
 */
class ObjectData extends AbstractExtensionData implements ObjectDataInterface
{
    /**
     * @var PropertiesInterface
     */
    protected $properties;

    /**
     * @var ChangeEventInfoInterface
     */
    protected $changeEventInfo;

    /**
     * @var ObjectDataInterface[]
     */
    protected $relationships = array();

    /**
     * @var RenditionDataInterface[]
     */
    protected $renditions = array();

    /**
     * @var PolicyIdListInterface
     */
    protected $policyIds;

    /**
     * @var AllowableActionsInterface
     */
    protected $allowableActions;

    /**
     * @var AclInterface
     */
    protected $acl;

    /**
     * @var boolean|null
     */
    protected $isExactAcl;

    /**
     * @return AclInterface
     */
    public function getAcl()
    {
        return $this->acl;
    }

    /**
     * @param AclInterface $acl
     */
    public function setAcl(AclInterface $acl)
    {
        $this->acl = $acl;
    }

    /**
     * @return AllowableActionsInterface
     */
    public function getAllowableActions()
    {
        return $this->allowableActions;
    }

    /**
     * @param AllowableActionsInterface $allowableActions
     */
    public function setAllowableActions(AllowableActionsInterface $allowableActions)
    {
        $this->allowableActions = $allowableActions;
    }

    /**
     * @return ChangeEventInfoInterface
     */
    public function getChangeEventInfo()
    {
        return $this->changeEventInfo;
    }

    /**
     * @param ChangeEventInfoInterface $changeEventInfo
     */
    public function setChangeEventInfo(ChangeEventInfoInterface $changeEventInfo)
    {
        $this->changeEventInfo = $changeEventInfo;
    }

    /**
     * @param boolean $isExactAcl
     */
    public function setIsExactAcl($isExactAcl)
    {
        $this->isExactAcl = $this->castValueToSimpleType('boolean', $isExactAcl, true);
    }

    /**
     * Returns if the access control list reflects the exact permission set in the repository.
     *
     * @return boolean|null <code>true</code> - exact; <code>false</code> - not exact, other permission constraints
     *      exist; <code>null</code> - unknown
     */
    public function isExactAcl()
    {
        return $this->isExactAcl;
    }

    /**
     * @return PolicyIdListInterface
     */
    public function getPolicyIds()
    {
        return $this->policyIds;
    }

    /**
     * @param PolicyIdListInterface $policyIds
     */
    public function setPolicyIds(PolicyIdListInterface $policyIds)
    {
        $this->policyIds = $policyIds;
    }

    /**
     * @return PropertiesInterface|null
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param PropertiesInterface $properties
     */
    public function setProperties(PropertiesInterface $properties)
    {
        $this->properties = $properties;
    }

    /**
     * @return ObjectDataInterface[]
     */
    public function getRelationships()
    {
        return $this->relationships;
    }

    /**
     * @param ObjectDataInterface[] $relationships
     */
    public function setRelationships(array $relationships)
    {
        foreach ($relationships as $relationship) {
            $this->checkType('\\Dkd\\PhpCmis\\Data\\ObjectDataInterface', $relationship);
        }
        $this->relationships = $relationships;
    }

    /**
     * @return RenditionDataInterface[]
     */
    public function getRenditions()
    {
        return $this->renditions;
    }

    /**
     * @param RenditionDataInterface[] $renditions
     */
    public function setRenditions(array $renditions)
    {
        $this->renditions = $renditions;
    }

    /**
     * Returns the base object type.
     *
     * @return BaseTypeId|null the base object type or <code>null</code> if the base object type is unknown
     */
    public function getBaseTypeId()
    {
        $value = $this->getFirstValue(PropertyIds::BASE_TYPE_ID);
        if (is_string($value)) {
            try {
                return BaseTypeId::cast($value);
            } catch (InvalidEnumerationValueException $e) {
                // invalid base type -> return null
            }
        }

        return null;
    }

    /**
     * Returns the object ID.
     *
     * @return string|null the object ID or <code>null</code> if the object ID is unknown
     */
    public function getId()
    {
        $value = $this->getFirstValue(PropertyIds::OBJECT_ID);
        if (is_string($value)) {
            return $value;
        }
        return null;
    }

    /**
     * Returns the first value of a property or <code>null</code> if the
     * property is not set.
     *
     * @param string $id
     * @return mixed
     */
    private function getFirstValue($id)
    {
        if ($this->properties === null) {
            return null;
        }
        $properties = $this->properties->getProperties();

        if (isset($properties[$id])) {
            return $properties[$id]->getFirstValue();
        }

        return null;
    }
}
