<?php
namespace Dkd\PhpCmis\Data;

/**
 * Container used for trees that represent objects in a folder hierarchy.
 */
interface ObjectInFolderContainerInterface extends ExtensionsDataInterface
{
    /**
     * Returns the object containers of the next level.
     *
     * @return ObjectInFolderContainerInterface[] the child object, not null
     */
    public function getChildren();

    /**
     * Returns the object at this level.
     *
     * @return ObjectInFolderDataInterface the object, not null
     */
    public function getObject();
}
