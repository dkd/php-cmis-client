<?php
namespace Dkd\PhpCmis\Data;

/**
 * Represents a rendition.
 */
interface RenditionDataInterface extends ExtensionsDataInterface
{
    /**
     * Returns the height in pixels, if the rendition is an image.
     *
     * @return int|null
     */
    public function getBigHeight();

    /**
     * Returns the size of the rendition in bytes, if available.
     *
     * @return int|null the size of the rendition in bytes, may be null
     */
    public function getBigLength();

    /**
     * Returns the width in pixels, if the rendition is an image.
     *
     * @return int|null
     */
    public function getBigWidth();

    /**
     * Returns the kind of the rendition.
     * The CMIS specification only defines the kind cmis:thumbnail, but a repository can provide other kinds.
     *
     * @return string|null the rendition kind, may be null
     */
    public function getKind();

    /**
     * Returns the MIME type of the rendition.
     *
     * @return string the MIME type, should not be null
     */
    public function getMimeType();

    /**
     * Returns the object id of the rendition document if the rendition is a stand-alone document.
     *
     * @return string|null the rendition document ID, may be null
     */
    public function getRenditionDocumentId();

    /**
     * Returns the stream ID of the rendition.
     *
     * The stream ID is required to fetch the content of the rendition.
     *
     * @return string the stream ID, not null
     */
    public function getStreamId();

    /**
     * Returns the title of the rendition.
     *
     * @return string
     */
    public function getTitle();
}
