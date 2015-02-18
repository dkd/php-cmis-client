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

/**
 * Represents a parent of object of a child object.
 */
interface ObjectParentDataInterface extends ExtensionDataInterface
{
    /**
     * Returns the parent object.
     *
     * @return ObjectDataInterface the parent object, not <code>null</code>
     */
    public function getObject();

    /**
     * Returns the relative path segment of the child object relative to the parent object.
     *
     * @return string|null the relative path segment or <code>null</code> if the relative path segment has not
     *      been requested
     */
    public function getRelativePathSegment();
}
