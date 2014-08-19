<?php
namespace Dkd\PhpCmis\Definitions;

use Dkd\PhpCmis\Data\ExtensionsDataInterface;
use Dkd\PhpCmis\TypeDefinitionInterface;

/**
 * Type Definition Container. This class is used to build a tree of type definitions.
 */
interface TypeDefinitionContainerInterface extends ExtensionsDataInterface
{
    /**
     * Returns direct children of the type definition at this level.
     *
     * @return TypeDefinitionContainerInterface[]
     */
    public function getChildren();

    /**
     * Returns the type definition at this level.
     *
     * @return TypeDefinitionInterface
     */
    public function getTypeDefinition();
}
