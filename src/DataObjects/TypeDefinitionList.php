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

use Dkd\PhpCmis\Definitions\TypeDefinitionInterface;
use Dkd\PhpCmis\Definitions\TypeDefinitionListInterface;

/**
 * TypeDefinitionList implementation.
 */
class TypeDefinitionList extends AbstractExtensionData implements TypeDefinitionListInterface
{
    /**
     * @var TypeDefinitionInterface[]
     */
    protected $list = array();

    /**
     * @var boolean
     */
    protected $hasMoreItems = false;

    /**
     * @var integer
     */
    protected $numItems;

    /**
     * Returns the list of type definitions.
     *
     * @return TypeDefinitionInterface[]
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * Set a list of type definitions
     *
     * @param TypeDefinitionInterface[] $list
     */
    public function setList(array $list)
    {
        foreach ($list as $item) {
            $this->checkType('\\Dkd\\PhpCmis\\Definitions\\TypeDefinitionInterface', $item);
        }
        $this->list = $list;
    }

    /**
     * Returns the total number of type definitions.
     *
     * @return integer|null total number of type definitions or <code>null</code> if the total number is unknown
     */
    public function getNumItems()
    {
        return $this->numItems;
    }

    /**
     * Sets the total number of type definitions
     *
     * @param $numItems
     */
    public function setNumItems($numItems)
    {
        $this->checkType('integer', $numItems);
        $this->numItems = $numItems;
    }

    /**
     * Returns whether there more type definitions or not.
     *
     * @return boolean|null <code>true</code> if there are more type definitions,
     * <code>false</code> if there are no more type definitions,
     * <code>null</code> if it's unknown
     */
    public function hasMoreItems()
    {
        return $this->hasMoreItems;
    }

    /**
     * Set if the list has more items or not.
     *
     * @param boolean $hasMoreItems
     */
    public function setHasMoreItems($hasMoreItems)
    {
        $this->checkType('boolean', $hasMoreItems);
        $this->hasMoreItems = $hasMoreItems;
    }
}
