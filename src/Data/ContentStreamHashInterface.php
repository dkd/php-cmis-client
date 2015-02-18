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
 * Content hash.
 */
interface ContentStreamHashInterface extends ExtensionDataInterface
{
    /**
     * Returns the hash algorithm.
     *
     * @return string|null the hash algorithm or <code>null</code> if the property value is invalid
     */
    public function getAlgorithm();

    /**
     * Returns the hash value.
     *
     * @return string|null the hash value or <code>null</code> if the property value is invalid
     */
    public function getHash();

    /**
     * Returns the content hash property value (cmis:contentStreamHash).
     *
     * @return string the content hash property value
     */
    public function getPropertyValue();
}
