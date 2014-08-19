<?php
namespace Dkd\PhpCmis\Data;

/**
 * Represents a parent of object of a child object.
 */
interface ObjectParentDataInterface extends ExtensionsDataInterface
{
    /**
     * Returns the parent object.
     *
     * @return ObjectDataInterface the parent object, not null
     */
    public function getObject();

    /**
     * Returns the relative path segment of the child object relative to the parent object.
     *
     * @return string|null the relative path segment or null if the relative path segment has not been requested
     */
    public function getRelativePathSegment();
}
