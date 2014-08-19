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
 * Content stream
 */
interface ContentStreamInterface extends ExtensionsDataInterface
{
    /**
     * Returns the length of stream.
     *
     * @return int the length of the stream in bytes or -1 if the length is unknown
     */
    public function getLength();

    /**
     * Returns the length of stream.
     *
     * @return int the length of the stream in bytes or null if the length is unknown
     */
    public function getBigLength();

    /**
     * Returns the MIME type of the stream.
     *
     * @return string the MIME type of the stream or null if the MIME type is unknown
     */
    public function getMimeType();

    /**
     * Returns the file name of the stream.
     *
     * @return string the file name of the stream or null if the file name is unknown
     */
    public function getFileName();

    /**
     * Returns the stream.
     * It is important to close this stream properly!
     *
     * @return resource the stream
     */
    public function getStream();
}
