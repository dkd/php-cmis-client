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

use Dkd\PhpCmis\Data\ExtensionsDataInterface;
use Dkd\PhpCmis\TypeDefinitionInterface;

/**
 * List of type definitions.
 */
interface TypeDefinitionListInterface extends ExtensionsDataInterface
{
    /**
     * Returns the list of type definitions.
     *
     * @return TypeDefinitionInterface[]
     */
    public function getList();

    /**
     * Returns the total number of type definitions.
     *
     * @return int|null total number of type definitions or null if the total number is unknown
     */
    public function getNumItems();

    /**
     * Returns whether there more type definitions or not.
     *
     * @return boolean|null true if there are more type definitions,
     * false if there are no more type definitions,
     * null if it's unknown
     */
    public function hasMoreItems();
}
