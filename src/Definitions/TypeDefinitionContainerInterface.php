<?php
namespace Dkd\PhpCmis\Definitions;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Data\ExtensionDataInterface;

/**
 * Type Definition Container. This class is used to build a tree of type definitions.
 */
interface TypeDefinitionContainerInterface extends ExtensionDataInterface
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
