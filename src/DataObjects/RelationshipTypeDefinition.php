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
