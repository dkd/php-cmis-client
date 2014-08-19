<?php
namespace Dkd\PhpCmis;

/**
 * Basic tree structure.
 */
interface Tree
{
    /**
     * Returns the children.
     *
     * @return Tree[]
     */
    public function getChildren();

    /**
     * Returns the item on this level.
     *
     * @return mixed
     */
    public function getItem();
}
