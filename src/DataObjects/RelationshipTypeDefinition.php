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

use Dkd\PhpCmis\Definitions\MutableRelationshipTypeDefinitionInterface;
use Dkd\PhpCmis\Definitions\RelationshipTypeDefinitionInterface;
use Dkd\PhpCmis\Definitions\TypeDefinitionInterface;
use Dkd\PhpCmis\Exception\CmisInvalidArgumentException;

/**
 * Relationship type definition.
 */
class RelationshipTypeDefinition extends AbstractTypeDefinition implements MutableRelationshipTypeDefinitionInterface
{
    /**
     * @var array
     */
    protected $allowedSourceTypeIds = array();

    /**
     * @var array
     */
    protected $allowedTargetTypeIds = array();

    /**
     * @param RelationshipTypeDefinitionInterface $typeDefinition
     */
    public function initialize(TypeDefinitionInterface $typeDefinition)
    {
        if (!$typeDefinition instanceof RelationshipTypeDefinitionInterface) {
            throw new CmisInvalidArgumentException(
                sprintf(
                    'In instance of RelationshipTypeDefinition was expected but "%s" was given.',
                    get_class($typeDefinition)
                )
            );
        }
        parent::initialize($typeDefinition);
        if ($typeDefinition->getAllowedTargetTypeIds() !== null) {
            $this->setAllowedTargetTypeIds($typeDefinition->getAllowedTargetTypeIds());
        }
        if ($typeDefinition->getAllowedSourceTypeIds() !== null) {
            $this->setAllowedSourceTypeIds($typeDefinition->getAllowedSourceTypeIds());
        }
    }

    /**
     * Sets the list of type IDs that are allowed as source objects.
     *
     * @param string[] $allowedSourceTypeIds of type IDs or <code>array()</code> if all types are allowed
     */
    public function setAllowedSourceTypeIds(array $allowedSourceTypeIds)
    {
        $this->allowedSourceTypeIds = $allowedSourceTypeIds;
    }

    /**
     * Sets the list of type IDs that are allowed as target objects.
     *
     * @param string[] $allowedTargetTypeIds of type IDs or <code>array()</code> if all types are allowed
     */
    public function setAllowedTargetTypeIds(array $allowedTargetTypeIds)
    {
        $this->allowedTargetTypeIds = $allowedTargetTypeIds;
    }

    /**
     * Returns the list of type IDs that are allowed as source objects.
     *
     * @return string[] of type IDs or <code>null</code> if all types are allowed
     */
    public function getAllowedSourceTypeIds()
    {
        return $this->allowedSourceTypeIds;
    }

    /**
     * Returns the list of type IDs that are allowed as target objects.
     *
     * @return string[] of type IDs or <code>null</code> if all types are allowed
     */
    public function getAllowedTargetTypeIds()
    {
        return $this->allowedTargetTypeIds;
    }
}
