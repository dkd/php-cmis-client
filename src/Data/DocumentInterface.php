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

use Dkd\PhpCmis\Enum\VersioningState;
use Dkd\PhpCmis\OperationContextInterface;
use GuzzleHttp\Stream\StreamInterface;

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
     * @param boolean $refresh when set to <code>false</code> this object will not be refreshed after the content
     *     stream has been appended.
     * @return ObjectIdInterface the updated object ID, or <code>null</code> if the repository did not return an
     *      object ID
     */
    public function appendContentStream(StreamInterface $contentStream, $isLastChunk, $refresh = true);

    /**
     * If this is a PWC (private working copy) the check out will be reversed.
     */
    public function cancelCheckOut();

    /**
     * If this is a PWC (private working copy) it performs a check in.
     * If this is not a PWC an exception will be thrown.
     * The stream in contentStream is consumed but not closed by this method.
     *
     * @param boolean $major <code>true</code> if the checked-in document object MUST be a major version.
     *     <code>false</code> if the checked-in document object MUST NOT be a major version but a minor version.
     * @param array $properties The property values that MUST be applied to the checked-in document object.
     * @param StreamInterface $contentStream The content stream that MUST be stored for the checked-in document object.
     *     The method of passing the contentStream to the server and the encoding mechanism will be specified by each
     *     specific binding. MUST be required if the type requires it.
     * @param string $checkinComment Textual comment associated with the given version. MAY be "not set".
     * @param PolicyInterface[] $policies A list of policy ids that MUST be applied to the newly-created document object
     * @param AceInterface[] $addAces A list of ACEs that MUST be added to the newly-created document object.
     * @param AceInterface[] $removeAces A list of ACEs that MUST be removed from the newly-created document object.
     * @return ObjectIdInterface The id of the checked-in document.
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
     * @param ObjectIdInterface|null $targetFolderId the ID of the target folder, <code>null</code> to create
     *      an unfiled document
     * @param array $properties The property values that MUST be applied to the object. This list of properties SHOULD
     *     only contain properties whose values differ from the source document.
     * @param VersioningState|null $versioningState An enumeration specifying what the versioning state of the
     *     newly-created object MUST be. Valid values are:
     *      <code>none</code>
     *          (default, if the object-type is not versionable) The document MUST be created as a non-versionable
     *          document.
     *     <code>checkedout</code>
     *          The document MUST be created in the checked-out state. The checked-out document MAY be
     *          visible to other users.
     *     <code>major</code>
     *          (default, if the object-type is versionable) The document MUST be created as a major version.
     *     <code>minor</code>
     *          The document MUST be created as a minor version.
     * @param PolicyInterface[] $policies A list of policy ids that MUST be applied to the newly-created document
     *     object.
     * @param AceInterface[] $addAces A list of ACEs that MUST be added to the newly-created document object, either
     *     using the ACL from folderId if specified, or being applied if no folderId is specified.
     * @param AceInterface[] $removeAces A list of ACEs that MUST be removed from the newly-created document object,
     *     either using the ACL from folderId if specified, or being ignored if no folderId is specified.
     * @param OperationContextInterface|null $context
     * @return DocumentInterface|null the new document object or <code>null</code> if the parameter <code>context</code>
     *     was set to <code>null</code>
     */
    public function copy(
        ObjectIdInterface $targetFolderId = null,
        array $properties = array(),
        VersioningState $versioningState = null,
        array $policies = array(),
        array $addAces = array(),
        array $removeAces = array(),
        OperationContextInterface $context = null
    );

    /**
     * Deletes this document and all its versions.
     */
    public function deleteAllVersions();

    /**
     * Removes the current content stream from the document and refreshes this object afterwards.
     *
     * @param boolean $refresh if this parameter is set to <code>true</code>, this object will be refreshed after the
     *     content stream has been deleted
     * @return DocumentInterface|null the updated document, or <code>null</code> if the repository did not return
     *      an object ID
     */
    public function deleteContentStream($refresh = true);

    /**
     * Fetches all versions of this document using the given OperationContext.
     * The behavior of this method is undefined if the document is not versionable
     * and can be different for each repository.
     *
     * @param OperationContextInterface|null $context
     * @return DocumentInterface[]
     */
    public function getAllVersions(OperationContextInterface $context = null);

    /**
     * Returns the content URL of the document or a rendition if the binding
     * supports content URLs.
     *
     * Depending on the repository and the binding, the server might not return
     * the content but an error message. Authentication data is not attached.
     * That is, a user may have to re-authenticate to get the content.
     *
     * @param string|null $streamId the ID of the rendition or <code>null</code> for the document
     *
     * @return string|null the content URL of the document or rendition or <code>null</code> if
     *         the binding does not support content URLs
     */
    public function getContentUrl($streamId = null);

    /**
     * Retrieves the content stream that is associated with the given stream ID.
     * This is usually a rendition of the document.
     *
     * @param string|null $streamId the stream ID
     * @param integer|null $offset the offset of the stream or <code>null</code> to read the stream from the beginning
     * @param integer|null $length the maximum length of the stream or <code>null</code> to read to the end of the
     *      stream
     * @return StreamInterface|null the content stream, or <code>null</code> if no content is associated with this
     *      stream ID
     */
    public function getContentStream($streamId = null, $offset = null, $length = null);

    /**
     * Fetches the latest major or minor version of this document using the given OperationContext.
     *
     * @param boolean $major if <code>true</code> the latest major version will be returned,
     * otherwise the very last version will be returned
     * @param OperationContextInterface|null $context
     * @return DocumentInterface the latest document object
     */
    public function getObjectOfLatestVersion($major, OperationContextInterface $context = null);

    /**
     * Sets a new content stream for the document. If the repository created a new version,
     * the object ID of this new version is returned. Otherwise the object ID of the current document is returned.
     * The stream in contentStream is consumed but not closed by this method.
     *
     * @param StreamInterface $contentStream the content stream
     * @param boolean $overwrite if this parameter is set to <code>false</code> and the document already has content,
     * the repository throws a CmisContentAlreadyExistsException
     * @param boolean $refresh if this parameter is set to <code>true</code>, this object will be refreshed
     * after the new content has been set
     * @return ObjectIdInterface|null the updated object ID, or <code>null</code> if the repository did not return
     *      an object ID
     */
    public function setContentStream(StreamInterface $contentStream, $overwrite, $refresh = true);
}
