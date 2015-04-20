<?php
namespace Dkd\PhpCmis\DataObjects;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Dimitri Ebert <dimitri.ebert@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Bindings\LinkAccessInterface;
use Dkd\PhpCmis\Data\DocumentInterface;
use Dkd\PhpCmis\Data\RenditionInterface;
use Dkd\PhpCmis\OperationContextInterface;
use Dkd\PhpCmis\SessionInterface;
use GuzzleHttp\Stream\StreamInterface;

/**
 * Cmis Rendition implementation
 */
class Rendition extends RenditionData implements RenditionInterface
{
    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var string
     */
    protected $objectId;

    /**
     * @param SessionInterface $session
     * @param $objectId
     */
    public function __construct(SessionInterface $session, $objectId)
    {
        $this->session = $session;
        $this->objectId = $objectId;
    }

    /**
     * Returns the height in pixels if the rendition is an image.
     *
     * @return integer the height in pixels or -1 if the height is not available or the rendition is not an image
     */
    public function getHeight()
    {
        return parent::getHeight() ?: -1;
    }

    /**
     * Returns the size of the rendition in bytes if available.
     *
     * @return integer the size of the rendition in bytes or -1 if the size is not available
     */
    public function getLength()
    {
        return parent::getLength() ?: -1;
    }

    /**
     * Returns the rendition document using the provides OperationContext if the rendition is a stand-alone document.
     *
     * @param OperationContextInterface|null $context
     * @return DocumentInterface|null the rendition document or <code>null</code> if there is no rendition document
     */
    public function getRenditionDocument(OperationContextInterface $context = null)
    {
        $renditionDocumentId = $this->getRenditionDocumentId();
        if (empty($renditionDocumentId)) {
            return null;
        }

        if ($context === null) {
            $context = $this->session->getDefaultContext();
        }

        $document = $this->session->getObject($this->session->createObjectId($renditionDocumentId), $context);

        return $document instanceof DocumentInterface ? $document : null;
    }

    /**
     * Returns the width in pixels if the rendition is an image.
     *
     * @return integer the width in pixels or -1 if the width is not available or the rendition is not an image
     */
    public function getWidth()
    {
        return parent::getWidth() ?: -1;
    }

    /**
     * Returns the content stream of the rendition.
     *
     * @return StreamInterface|null the content stream of the rendition
     *      or <code>null</code> if the rendition has no content
     */
    public function getContentStream()
    {
        if (!$this->objectId || !$this->getStreamId()) {
            return null;
        }

        return $this->session->getBinding()->getObjectService()->getContentStream(
            $this->session->getRepositoryInfo()->getId(),
            $this->objectId,
            $this->getStreamId()
        );
    }


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
    public function getContentUrl()
    {
        $objectService = $this->session->getBinding()->getObjectService();
        if ($objectService instanceof LinkAccessInterface) {
            return $objectService->loadRenditionContentLink(
                $this->session->getRepositoryInfo()->getId(),
                $this->objectId,
                $this->getStreamId()
            );
        }
        return null;
    }
}
