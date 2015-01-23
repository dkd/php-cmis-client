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

use Dkd\PhpCmis\Data\ObjectTypeInterface;
use Dkd\PhpCmis\Data\RelationshipTypeInterface;
use Dkd\PhpCmis\Definitions\RelationshipTypeDefinitionInterface;
use Dkd\PhpCmis\SessionInterface;

/**
 * Folder Type implementation.
 */
class RelationshipType extends RelationshipTypeDefinition implements RelationshipTypeInterface
{
    use ObjectTypeHelperTrait {
        ObjectTypeHelperTrait::__construct as private objectTypeConstructor;
    }

    /**
     * @var ObjectTypeInterface[]|null
     */
    protected $allowedSourceTypes;

    /**
     * @var ObjectTypeInterface[]|null
     */
    protected $allowedTargetTypes;

    /**
     * @param SessionInterface $session
     * @param RelationshipTypeDefinitionInterface $typeDefinition
     */
    public function __construct(
        SessionInterface $session,
        RelationshipTypeDefinitionInterface $typeDefinition
    ) {
        $this->objectTypeConstructor($session, $typeDefinition);
        $this->setAllowedSourceTypeIds($typeDefinition->getAllowedSourceTypeIds());
        $this->setAllowedTargetTypeIds($typeDefinition->getAllowedTargetTypeIds());
    }

    /**
     * Reset the allowedSourceTypes to <code>null</code> so that the get recreated on the
     * new defined ids.
     *
     * @param string[] $allowedSourceTypeIds
     */
    public function setAllowedSourceTypeIds(array $allowedSourceTypeIds)
    {
        $this->allowedSourceTypes = null;
        parent::setAllowedSourceTypeIds($allowedSourceTypeIds);
    }

    /**
     * @return ObjectTypeInterface[]
     */
    public function getAllowedSourceTypes()
    {
        if ($this->allowedSourceTypes === null) {
            $this->allowedSourceTypes = array();
            foreach ($this->getAllowedSourceTypeIds() as $id) {
                $this->allowedSourceTypes[] = $this->getSession()->getTypeDefinition($id);
            }
        }

        return $this->allowedSourceTypes;
    }

    /**
     * Reset the allowedSourceTypes to <code>null</code> so that the get recreated on the
     * new defined ids.
     *
     * @param string[] $allowedTargetTypeIds
     */
    public function setAllowedTargetTypeIds(array $allowedTargetTypeIds)
    {
        $this->allowedTargetTypes = null;
        parent::setAllowedTargetTypeIds($allowedTargetTypeIds);
    }

    /**
     * @return ObjectTypeInterface[]
     */
    public function getAllowedTargetTypes()
    {
        if ($this->allowedTargetTypes === null) {
            $this->allowedTargetTypes = array();
            foreach ($this->getAllowedTargetTypeIds() as $id) {
                $this->allowedTargetTypes[] = $this->getSession()->getTypeDefinition($id);
            }
        }

        return $this->allowedTargetTypes;
    }
}
