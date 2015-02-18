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
 * Accessors to CMIS document properties.
 */
interface DocumentPropertiesInterface
{
    /**
     * Returns the checkin comment (CMIS property cmis:checkinComment).
     *
     * @return string|null the checkin comment of this version or <code>null</code> if the property hasn't
     *      been requested, hasn't been provided by the repository, or the property value isn't set
     */
    public function getCheckinComment();

    /**
     * Returns the content stream filename or <code>null</code> if the document has no content
     * (CMIS property cmis:contentStreamFileName).
     *
     * @return string|null the content stream filename of this document or <code>null</code> if the property hasn't
     *      been requested, hasn't been provided by the repository, or the document has no content
     */
    public function getContentStreamFileName();

    /**
     * Returns the content hashes or <code>null</code> if the document has no content
     * (CMIS property cmis:contentStreamHash).
     *
     * @return ContentStreamHashInterface[]|null the list of content hashes or <code>null</code> if the property
     *      hasn't been requested, hasn't been provided by the repository, or the document has no content
     */
    public function getContentStreamHashes();

    /**
     * Returns the content stream ID or <code>null</code> if the document has no content
     * (CMIS property cmis:contentStreamId).
     *
     * @return string|null the content stream ID of this document or <code>null</code> if the property hasn't
     *      been requested, hasn't been provided by the repository, or the document has no content
     */
    public function getContentStreamId();

    /**
     * Returns the content stream length or -1 if the document has no content (CMIS property cmis:contentStreamLength).
     *
     * @return integer the content stream length of this document or -1 if the property hasn't been requested,
     *      hasn't been provided by the repository, or the document has no content
     */
    public function getContentStreamLength();

    /**
     * Returns the content stream MIME type or <code>null</code> if the document has no content
     * (CMIS property cmis:contentStreamMimeType).
     *
     * @return string the content stream MIME type of this document or <code>null</code> if the property hasn't
     *      been requested, hasn't been provided by the repository, or the document has no content
     */
    public function getContentStreamMimeType();

    /**
     * Returns the version label (CMIS property cmis:versionLabel).
     *
     * @return string|null the version label of the document or <code>null</code> if the property hasn't been requested,
     * hasn't been provided by the repository, or the property value isn't set
     */
    public function getVersionLabel();

    /**
     * Returns the user who checked out this version series (CMIS property cmis:versionSeriesCheckedOutBy).
     *
     * @return string|null the user who checked out this version series or <code>null</code> if the property hasn't
     *      been requested, hasn't been provided by the repository, or the property value isn't set
     */
    public function getVersionSeriesCheckedOutBy();

    /**
     * Returns the PWC ID of this version series (CMIS property cmis:versionSeriesCheckedOutId).
     * Some repositories provided this value only to the user who checked out the version series.
     *
     * @return string|null the PWC ID of this version series or <code>null</code> if the property hasn't been requested,
     * hasn't been provided by the repository, or the property value isn't set
     */
    public function getVersionSeriesCheckedOutId();

    /**
     * Returns the version series ID (CMIS property cmis:versionSeriesId).
     *
     * @return string|null the version series ID of the document or <code>null</code> if the property hasn't
     *      been requested, hasn't been provided by the repository, or the property value isn't set
     */
    public function getVersionSeriesId();

    /**
     * Returns <code>true</code> if this document is immutable (CMIS property cmis:isImmutable).
     *
     * @return boolean|null the immutable flag of the document or <code>null</code> if the property hasn't
     *      been requested, hasn't been provided by the repository, or the property value isn't set
     */
    public function isImmutable();

    /**
     * Returns <code>true</code> if this document is the latest version (CMIS property cmis:isLatestVersion).
     *
     * @return boolean the latest version flag of the document or <code>null</code> if the property hasn't
     *      been requested, hasn't been provided by the repository, or the property value isn't set
     */
    public function isLatestMajorVersion();

    /**
     * Returns <code>true</code> if this document is the latest version (CMIS property cmis:isLatestVersion).
     *
     * @return boolean|null the latest version flag of the document or <code>null</code> if the property hasn't
     *      been requested, hasn't been provided by the repository, or the property value isn't set
     */
    public function isLatestVersion();

    /**
     * Returns <code>true</code> if this document is a major version (CMIS property cmis:isMajorVersion).
     *
     * @return boolean|null the major version flag of the document or <code>null</code> if the property hasn't
     *      been requested, hasn't been provided by the repository, or the property value isn't set
     */
    public function isMajorVersion();

    /**
     * Returns <code>true</code> if this document is the PWC (CMIS property cmis:isPrivateWorkingCopy).
     *
     * @return boolean|null the PWC flag of the document or <code>null</code> if the property hasn't been requested,
     * hasn't been provided by the repository, or the property value isn't set
     */
    public function isPrivateWorkingCopy();

    /**
     * Returns <code>true</code> if this version series is checked out (CMIS property cmis:isVersionSeriesCheckedOut).
     *
     * @return boolean|null the version series checked out flag of the document or <code>null</code> if the property
     * hasn't been requested, hasn't been provided by the repository, or the property value isn't set
     */
    public function isVersionSeriesCheckedOut();
}
