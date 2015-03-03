<?php
namespace Dkd\PhpCmis\Data;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\SessionInterface;
use Dkd\PhpCmis\TreeInterface;
use Dkd\PhpCmis\Definitions\TypeDefinitionInterface;

/**
 * Object Type.
 */
interface ObjectTypeInterface extends TypeDefinitionInterface
{
    /**
     * Constructor of the object type.
     *
     * @param SessionInterface $session
     * @param TypeDefinitionInterface $typeDefinition
     */
    public function __construct(SessionInterface $session, TypeDefinitionInterface $typeDefinition);

    /**
     * Gets the types base type, if the type is a derived (non-base) type.
     *
     * @return ObjectTypeInterface|null the base type this type is derived from, or <code>null</code> if it is
     *      a base type
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
     * @param integer $depth the tree depth, must be greater than 0 or -1 for infinite depth
     * @return TreeInterface[] a list of trees of types which are derived from this type (direct and via their parents)
     * @see ObjectTypeInterface ObjectTypeInterface contained in returned list of TreeInterface's
     */
    public function getDescendants($depth);

    /**
     * Gets the types parent type, if the type is a derived (non-base) type.
     *
     * @return ObjectTypeInterface|null the parent type from which this type is derived, or <code>null</code> if it is
     *      a base type
     */
    public function getParentType();

    /**
     * Indicates whether this is base object type or not.
     *
     * @return boolean <code>true</code> if this type is a base type, <code>false</code> if this type is a derived type
     */
    public function isBaseType();
}
