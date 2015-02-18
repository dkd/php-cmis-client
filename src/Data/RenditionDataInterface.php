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
 * Represents a rendition.
 */
interface RenditionDataInterface extends ExtensionDataInterface
{
    /**
     * Returns the height in pixels, if the rendition is an image.
     *
     * @return integer|null
     */
    public function getHeight();

    /**
     * Returns the size of the rendition in bytes, if available.
     *
     * @return integer|null the size of the rendition in bytes, may be <code>null</code>
     */
    public function getLength();

    /**
     * Returns the width in pixels, if the rendition is an image.
     *
     * @return integer|null
     */
    public function getWidth();

    /**
     * Returns the kind of the rendition.
     * The CMIS specification only defines the kind cmis:thumbnail, but a repository can provide other kinds.
     *
     * @return string|null the rendition kind, may be <code>null</code>
     */
    public function getKind();

    /**
     * Returns the MIME type of the rendition.
     *
     * @return string the MIME type, should not be <code>null</code>
     */
    public function getMimeType();

    /**
     * Returns the object id of the rendition document if the rendition is a stand-alone document.
     *
     * @return string|null the rendition document ID, may be <code>null</code>
     */
    public function getRenditionDocumentId();

    /**
     * Returns the stream ID of the rendition.
     *
     * The stream ID is required to fetch the content of the rendition.
     *
     * @return string the stream ID, not <code>null</code>
     */
    public function getStreamId();

    /**
     * Returns the title of the rendition.
     *
     * @return string
     */
    public function getTitle();
}
