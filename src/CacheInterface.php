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
 * An interface for an hierarchical cache.
 *
 * Each level of the hierarchy could use a different caching strategy. The cache
 * is initialize by defining the classes that handle the caching for one level.
 * These classes must implement the {@link CacheLevelInterface} interface.
 */
interface CacheInterface
{
    /**
     * Initializes the cache.
     *
     * @param  string[] $cacheLevelConfig
     *            the level configuration strings from the root to the leafs
     * @return void
     */
    public function initialize($cacheLevelConfig);

    /**
     * Adds an object to the cache.
     *
     * @param mixed $value the value
     * @param string $key the key for this object
     * @return void
     */
    public function put($value, $key);

    /**
     * Retrieves an object from the cache.
     *
     * @param string $key the key
     * @return mixed the object or null if the branch or leaf doesn't exist
     */
    public function get($key);

    /**
     * Removes a branch or leaf from the cache.
     *
     * @param string $key the key of the branch or leaf
     * @return void
     */
    public function remove($key);

    /**
     * Removes all entries from the cache.
     *
     * @return void
     */
    public function removeAll();

    /**
     * Checks if a given key is in the cache.
     *
     * @param string $key the keys of the branch or leaf
     * @return boolean true if the object is in the cache
     */
    public function check($key);

    /**
     * Applies a write lock.
     *
     * @return void
     */
    public function writeLock();

    /**
     * Releases a write lock.
     *
     * @return void
     */
    public function writeUnlock();
}
