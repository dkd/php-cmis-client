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

use Dkd\PhpCmis\OperationContextInterface;
use GuzzleHttp\Stream\StreamInterface;

/**
 * Rendition.
 */
interface RenditionInterface extends RenditionDataInterface
{
    /**
     * Returns the content stream of the rendition.
     *
     * @return StreamInterface|null the content stream of the rendition
     *      or <code>null</code> if the rendition has no content
     */
    public function getContentStream();

    /**
     * Returns the height in pixels if the rendition is an image.
     *
     * @return integer the height in pixels or -1 if the height is not available or the rendition is not an image
     */
    public function getHeight();

    /**
     * Returns the size of the rendition in bytes if available.
     *
     * @return integer the size of the rendition in bytes or -1 if the size is not available
     */
    public function getLength();

    /**
     * Returns the rendition document using the provides OperationContext if the rendition is a stand-alone document.
     *
     * @param OperationContextInterface|null $context
     * @return DocumentInterface|null the rendition document or <code>null</code> if there is no rendition document
     */
    public function getRenditionDocument(OperationContextInterface $context = null);

    /**
     * Returns the width in pixels if the rendition is an image.
     *
     * @return integer the width in pixels or -1 if the width is not available or the rendition is not an image
     */
    public function getWidth();

    /**
     * Returns the content URL of the rendition if the binding supports content
     * URLs.
     *
     * Depending on the repository and the binding, the server might not return
     * the content but an error message. Authentication data is not attached.
     * That is, a user may have to re-authenticate to get the content.
     *
     * @return string|null the content URL of the rendition or <code>null</code> if the binding
     *         does not support content URLs
     */
    public function getContentUrl();
}
