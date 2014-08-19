<?php
namespace Dkd\PhpCmis;

/**
 * Object Type.
 */
interface ObjectTypeInterface extends TypeDefinitionInterface
{
    /**
     * Gets the types base type, if the type is a derived (non-base) type.
     *
     * @return ObjectTypeInterface|null the base type this type is derived from, or null if it is a base type
     */
    public function getBaseType();

    /**
     * Gets the list of types directly derived from this type (which will return this type on getParent()).
     *
     * @return ObjectTypeInterface[]|null list of types which are directly derived from this type
     */
    public function getChildren();

    /**
     * Gets the list of all types somehow derived from this type.
     *
     * @param int $depth the tree depth, must be greater than 0 or -1 for infinite depth
     *
     * @return Tree<ObjectTypeInterface> a list of trees of types which are derived from
     * this type (direct and via their parents)
     */
    public function getDescendants($depth);

    /**
     * Gets the types parent type, if the type is a derived (non-base) type.
     *
     * @return ObjectTypeInterface|null the parent type from which this type is derived, or null if it is a base type
     */
    public function getParentType();

    /**
     * Indicates whether this is base object type or not.
     *
     * @return boolean true if this type is a base type, false if this type is a derived type
     */
    public function isBaseType();
}
