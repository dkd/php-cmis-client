<?php
namespace Dkd\PhpCmis\DataObjects;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Data\RenditionDataInterface;

/**
 * Repository info data implementation.
 */
class RenditionData extends AbstractExtensionData implements RenditionDataInterface
{
    /**
     * @var string
     */
    protected $streamId = '';

    /**
     * string
     */
    protected $mimeType = '';

    /**
     * integer
     */
    protected $length = 0;

    /**
     * string
     */
    protected $kind = '';

    /**
     * string
     */
    protected $title = '';

    /**
     * integer
     */
    protected $width = 0;

    /**
     * integer
     */
    protected $height = 0;

    /**
     * string
     */
    protected $renditionDocumentId = '';

    /**
     * @return integer
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param integer $height
     */
    public function setHeight($height)
    {
        $this->height = (integer) $height;
    }

    /**
     * @return string
     */
    public function getKind()
    {
        return $this->kind;
    }

    /**
     * @param string $kind
     */
    public function setKind($kind)
    {
        $this->kind = (string) $kind;
    }

    /**
     * @return integer
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @param integer $length
     */
    public function setLength($length)
    {
        $this->length = (integer) $length;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @param string $mimeType
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = (string) $mimeType;
    }

    /**
     * @return string
     */
    public function getRenditionDocumentId()
    {
        return $this->renditionDocumentId;
    }

    /**
     * @param string $renditionDocumentId
     */
    public function setRenditionDocumentId($renditionDocumentId)
    {
        $this->renditionDocumentId = (string) $renditionDocumentId;
    }

    /**
     * @return string
     */
    public function getStreamId()
    {
        return $this->streamId;
    }

    /**
     * @param string $streamId
     */
    public function setStreamId($streamId)
    {
        $this->streamId = (string) $streamId;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = (string) $title;
    }

    /**
     * @return integer
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param integer $width
     */
    public function setWidth($width)
    {
        $this->width = (integer) $width;
    }
}
