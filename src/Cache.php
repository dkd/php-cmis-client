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

class Cache implements CacheInterface
{
    /**
     * Initializes the cache.
     *
     * @param  string[] $cacheLevelConfig
     *            the level configuration strings from the root to the leafs
     * @return void
     */
    public function initialize($cacheLevelConfig)
    {
        // TODO: Implement initialize() method.
    }

    /**
     * Adds an object to the cache.
     *
     * @param mixed $value the value
     * @param string $key the key for this object
     * @return void
     */
    public function put($value, $key)
    {
        // TODO: Implement put() method.
    }

    /**
     * Retrieves an object from the cache.
     *
     * @param string $key the key
     * @return mixed the object or null if the branch or leaf doesn't exist
     */
    public function get($key)
    {
        // TODO: Implement get() method.
    }

    /**
     * Removes a branch or leaf from the cache.
     *
     * @param string $key the key of the branch or leaf
     * @return void
     */
    public function remove($key)
    {
        // TODO: Implement remove() method.
    }

    /**
     * Removes all entries from the cache.
     *
     * @return void
     */
    public function removeAll()
    {
        // TODO: Implement removeAll() method.
    }

    /**
     * Checks if a given key is in the cache.
     *
     * @param string $key the keys of the branch or leaf
     * @return boolean true if the object is in the cache
     */
    public function check($key)
    {
        // TODO: Implement check() method.
    }

    /**
     * Applies a write lock.
     *
     * @return void
     */
    public function writeLock()
    {
        // TODO: Implement writeLock() method.
    }

    /**
     * Releases a write lock.
     *
     * @return void
     */
    public function writeUnlock()
    {
        // TODO: Implement writeUnlock() method.
    }
}
