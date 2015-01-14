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
use Dkd\PhpCmis\Definitions\TypeDefinitionInterface;
use Dkd\PhpCmis\Enum\BaseTypeId;
use Dkd\PhpCmis\SessionInterface;
use Dkd\PhpCmis\Tree;

/**
 * Helper for object types, containing session-related info.
 */
trait ObjectTypeHelperTrait
{
    /**
     * @var null|ObjectTypeInterface
     */
    protected $baseType;

    /**
     * @var null|ObjectTypeInterface
     */
    protected $parentType;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @param SessionInterface $session
     * @param TypeDefinitionInterface $typeDefinition
     */
    public function __construct(
        SessionInterface $session,
        TypeDefinitionInterface $typeDefinition
    ) {
        $this->session = $session;
        $this->initialize($typeDefinition);
    }

    /**
     * @return SessionInterface
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @return BaseTypeId
     */
    abstract public function getBaseTypeId();

    /**
     * @return string
     */
    abstract public function getId();

    /**
     * @return null|string
     */
    abstract public function getParentTypeId();

    /**
     * @param TypeDefinitionInterface $typeDefinition
     */
    abstract protected function initialize(TypeDefinitionInterface $typeDefinition);

    /**
     * @return boolean
     */
    public function isBaseType()
    {
        $baseTypeId = $this->getParentTypeId();
        return empty($baseTypeId);
    }

    /**
     * @return null|ObjectTypeInterface
     */
    public function getBaseType()
    {
        if ($this->isBaseType()) {
            // a base type can't have a base type
            return null;
        }
        if ($this->baseType !== null) {
            return $this->baseType;
        }
        $baseTypeId = $this->getBaseTypeId();
        if ($baseTypeId === null) {
            return null;
        }
        $this->baseType = $this->getSession()->getTypeDefinition((string) $baseTypeId);

        return $this->baseType;
    }

    /**
     * @return null|ObjectTypeInterface
     */
    public function getParentType()
    {
        if ($this->parentType !== null) {
            return $this->parentType;
        }
        $parentTypeId = $this->getParentTypeId();
        if (empty($parentTypeId)) {
            return null;
        }
        $this->parentType = $this->getSession()->getTypeDefinition($parentTypeId);

        return $this->parentType;
    }

    /**
     * @return ObjectTypeInterface[]
     */
    public function getChildren()
    {
        return $this->getSession()->getTypeChildren($this->getId(), true);
    }

    /**
     * @param integer $depth
     * @return Tree A tree that contains ObjectTypeInterface objects
     * @see ObjectTypeInterface ObjectTypeInterface contained in returned Tree
     */
    public function getDescendants($depth)
    {
        return $this->getSession()->getTypeDescendants($this->getId(), $depth, true);
    }
}
