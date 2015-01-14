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
 * List of type definitions.
 */
interface TypeDefinitionListInterface extends ExtensionDataInterface
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
     * @return integer|null total number of type definitions or <code>null</code> if the total number is unknown
     */
    public function getNumItems();

    /**
     * Returns whether there more type definitions or not.
     *
     * @return boolean|null <code>true</code> if there are more type definitions,
     * <code>false</code> if there are no more type definitions,
     * <code>null</code> if it's unknown
     */
    public function hasMoreItems();
}
