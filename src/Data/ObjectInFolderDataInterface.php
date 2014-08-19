<?php
namespace Dkd\PhpCmis\Data;

/**
 * Represents an object in a folder.
 */
interface ObjectInFolderDataInterface extends ExtensionsDataInterface
{
    /**
     * Returns the object at this level.
     *
     * @return ObjectDataInterface the object, not null
     */
    public function getObject();

    /**
     * Returns the path segment of the object in the folder.
     *
     * @return string|null the path segment or null if the path segment has not been requested
     */
    public function getPathSegment();
}
