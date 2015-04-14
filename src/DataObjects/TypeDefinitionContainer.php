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

use Dkd\PhpCmis\Definitions\TypeDefinitionContainerInterface;
use Dkd\PhpCmis\Definitions\TypeDefinitionInterface;

/**
 * Type Definition Container. This class is used to build a tree of type
 * definitions.
 */
class TypeDefinitionContainer extends AbstractExtensionData implements TypeDefinitionContainerInterface
{
    /**
     * @var TypeDefinitionContainerInterface[]
     */
    protected $children = array();

    /**
     * @var TypeDefinitionInterface
     */
    protected $typeDefinition;

    /**
     * Returns direct children of the type definition at this level.
     *
     * @return TypeDefinitionContainerInterface[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param TypeDefinitionContainerInterface[] $children
     */
    public function setChildren(array $children)
    {
        $this->children = $children;
    }

    /**
     * Returns the type definition at this level.
     *
     * @return TypeDefinitionInterface
     */
    public function getTypeDefinition()
    {
        return $this->typeDefinition;
    }

    public function setTypeDefinition(TypeDefinitionInterface $typeDefinition)
    {
        $this->typeDefinition = $typeDefinition;
    }
}
