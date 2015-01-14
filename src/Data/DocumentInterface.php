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
use Dkd\PhpCmis\PolicyInterface;
use GuzzleHttp\Stream\StreamInterface;
use Dkd\PhpCmis\Enum\VersioningState;

/**
 * CMIS document interface.
 */
interface DocumentInterface extends FileableCmisObjectInterface, DocumentPropertiesInterface
{
    /**
     * Appends a content stream to the content stream of the document and refreshes this object afterwards.
     * If the repository created a new version, this new document is returned.
     * Otherwise the current document is returned.
     * The stream in contentStream is consumed but not closed by this method.
     *
     * @param StreamInterface $contentStream the content stream
     * @param boolean $isLastChunk indicates if this stream is the last chunk of the content
     * @param boolean $refresh if this parameter is set to true, this object will be refreshed after the
     * content stream has been appended
     * @return ObjectIdInterface the updated object ID, or null if the repository did not return an object ID
     */
    public function appendContentStream(StreamInterface $contentStream, $isLastChunk, $refresh = false);

    /**
     * If this is a PWC (private working copy) the check out will be reversed.
     *
     * @return void
     */
    public function cancelCheckOut();

    /**
     * If this is a PWC (private working copy) it performs a check in.
     * If this is not a PWC an exception will be thrown.
     * The stream in contentStream is consumed but not closed by this method.
     *
     * @param boolean $major
     * @param array $properties
     * @param StreamInterface $contentStream
     * @param string $checkinComment
     * @param PolicyInterface[] $policies
     * @param AceInterface[] $addAces
     * @param AceInterface[] $removeAces
     * @return ObjectIdInterface
     */
    public function checkIn(
        $major,
        array $properties,
        StreamInterface $contentStream,
        $checkinComment,
        array $policies = array(),
        array $addAces = array(),
        array $removeAces = array()
    );

    /**
     * Checks out the document and returns the object ID of the PWC (private working copy).
     *
     * @return ObjectIdInterface PWC object ID
     */
    public function checkOut();

    /**
     * Creates a copy of this document, including content.
     *
     * @param ObjectIdInterface $targetFolderId the ID of the target folder, null to create an unfiled document
     * @param array $properties
     * @param VersioningState $versioningState
     * @param PolicyInterface[] $policies
     * @param AceInterface[] $addAces
     * @param AceInterface[] $removeAces
     * @param OperationContextInterface $context
     * @return DocumentInterface the new document object
     */
    public function copy(
        ObjectIdInterface $targetFolderId,
        $properties = array(),
        VersioningState $versioningState = null,
        array $policies = array(),
        array $addAces = array(),
        array $removeAces = array(),
        OperationContextInterface $context = null
    );

    /**
     * Deletes this document and all its versions.
     *
     * @return void
     */
    public function deleteAllVersions();

    /**
     * Removes the current content stream from the document and refreshes this object afterwards.
     *
     * @param boolean $refresh
     * @return DocumentInterface|null the updated document, or null if the repository did not return an object ID
     */
    public function deleteContentStream($refresh = true);

    /**
     * Fetches all versions of this document using the given OperationContext.
     * The behavior of this method is undefined if the document is not versionable
     * and can be different for each repository.
     *
     * @param OperationContextInterface $context
     * @return DocumentInterface[]
     */
    public function getAllVersions(OperationContextInterface $context = null);

    /**
     * Retrieves the content stream that is associated with the given stream ID.
     * This is usually a rendition of the document.
     *
     * @param string $streamId the stream ID
     * @param int $offset the offset of the stream or null to read the stream from the beginning
     * @param int $length the maximum length of the stream or null to read to the end of the stream
     * @return StreamInterface|null the content stream, or null if no content is associated with this stream ID
     */
    public function getContentStream($streamId = null, $offset = null, $length = null);

    /**
     * Fetches the latest major or minor version of this document using the given OperationContext.
     *
     * @param boolean $major if true the latest major version will be returned,
     * otherwise the very last version will be returned
     * @param OperationContextInterface $context
     * @return DocumentInterface the latest document object
     */
    public function getObjectOfLatestVersion($major, OperationContextInterface $context = null);

    /**
     * Sets a new content stream for the document. If the repository created a new version,
     * the object ID of this new version is returned. Otherwise the object ID of the current document is returned.
     * The stream in contentStream is consumed but not closed by this method.
     *
     * @param StreamInterface $contentStream the content stream
     * @param boolean $overwrite  if this parameter is set to false and the document already has content,
     * the repository throws a CmisContentAlreadyExistsException
     * @param boolean $refresh if this parameter is set to true, this object will be refreshed
     * after the new content has been set
     * @return ObjectIdInterface|null the updated object ID, or null if the repository did not return an object ID
     */
    public function setContentStream(StreamInterface $contentStream, $overwrite, $refresh = true);
}
