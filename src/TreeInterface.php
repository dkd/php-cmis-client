<?php
namespace Dkd\PhpCmis;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Basic tree structure.
 */
interface TreeInterface
{
    /**
     * Returns the children.
     *
     * @return TreeInterface[]
     */
    public function getChildren();

    /**
     * Returns the item on this level.
     *
     * @return mixed
     */
    public function getItem();
}
