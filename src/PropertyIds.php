<?php
namespace Dkd\PhpCmis;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * CMIS property id constants.
 */
final class PropertyIds
{
    /**
     * CMIS property <code>cmis:name</code> name of the object.
     *
     * CMIS data type: string
     * type: string
     */
    const NAME = 'cmis:name';

    /**
     * CMIS property <code>cmis:objectId</code> ID of the object.
     *
     * CMIS data type: id
     * type: string
     */
    const OBJECT_ID = 'cmis:objectId';

    /**
     * CMIS property <code>cmis:objectTypeId</code> ID of primary type of the
     * object.
     *
     * CMIS data type: id
     * type: string
     */
    const OBJECT_TYPE_ID = 'cmis:objectTypeId';

    /**
     * CMIS property <code>cmis:baseTypeId</code> ID of the base type of the object.
     *
     * CMIS data type: id
     * type: string
     */
    const BASE_TYPE_ID = 'cmis:baseTypeId';

    /**
     * CMIS property <code>cmis:createdBy</code> creator of the object.
     *
     * CMIS data type: string
     * type: string
     */
    const CREATED_BY = 'cmis:createdBy';

    /**
     * CMIS property <code>cmis:creationDate</code> creation date.
     *
     * CMIS data type: datetime
     * type: DateTime
     */
    const CREATION_DATE = 'cmis:creationDate';

    /**
     * CMIS property <code>cmis:lastModifiedBy</code> last modifier of the object.
     *
     * CMIS data type: string
     * type: string
     */
    const LAST_MODIFIED_BY = 'cmis:lastModifiedBy';

    /**
     * CMIS property <code>cmis:lastModificationDate</code> last modification date.
     *
     * CMIS data type: datetime
     * type: DateTime
     */
    const LAST_MODIFICATION_DATE = 'cmis:lastModificationDate';

    /**
     * CMIS property <code>cmis:changeToken</code> change token of the object.
     *
     * CMIS data type: string
     * type: string
     */
    const CHANGE_TOKEN = 'cmis:changeToken';

    /**
     * CMIS property <code>cmis:description</code> description of the object.
     *
     * CMIS data type: string
     * type: string
     */
    const DESCRIPTION = 'cmis:description';

    /**
     * CMIS property <code>cmis:secondaryObjectTypeIds} (multivalue): list of
     * IDs of the secondary types of the object.
     *
     * CMIS data type: id
     * type: string
     */
    const SECONDARY_OBJECT_TYPE_IDS = 'cmis:secondaryObjectTypeIds';

    protected static $BASE_PROPERTY_KEYS = array(
        self::NAME,
        self::OBJECT_ID,
        self::OBJECT_TYPE_ID,
        self::BASE_TYPE_ID,
        self::CREATED_BY,
        self::CREATION_DATE,
        self::LAST_MODIFIED_BY,
        self::LAST_MODIFICATION_DATE,
        self::CHANGE_TOKEN
    );

    /**
     * Returns all base properties that are required for every CMIS object.
     *
     * @return array
     */
    public static function getBasePropertyKeys()
    {
        return self::$BASE_PROPERTY_KEYS;
    }

    // ---- document ----
    /**
     * CMIS document property <code>cmis:isImmutable</code> flag the indicates if
     * the document is immutable.
     *
     * CMIS data type: boolean
     * type: boolean
     */
    const IS_IMMUTABLE = 'cmis:isImmutable';

    /**
     * CMIS document property <code>cmis:isLatestVersion</code> flag the indicates
     * if the document is the latest version.
     *
     * CMIS data type: boolean
     * type: boolean
     */
    const IS_LATEST_VERSION = 'cmis:isLatestVersion';

    /**
     * CMIS document property <code>cmis:isMajorVersion</code> flag the indicates if
     * the document is a major version.
     *
     * CMIS data type: boolean
     * type: boolean
     */
    const IS_MAJOR_VERSION = 'cmis:isMajorVersion';

    /**
     * CMIS document property <code>cmis:isLatestMajorVersion</code> flag the
     * indicates if the document is the latest major version.
     *
     * CMIS data type: boolean
     * type: boolean
     */
    const IS_LATEST_MAJOR_VERSION = 'cmis:isLatestMajorVersion';

    /**
     * CMIS document property <code>cmis:versionLabel</code> version label of the
     * document.
     *
     * CMIS data type: string
     * type: string
     */
    const VERSION_LABEL = 'cmis:versionLabel';

    /**
     * CMIS document property <code>cmis:versionSeriesId</code> ID of the version
     * series.
     *
     * CMIS data type: id
     * type: string
     */
    const VERSION_SERIES_ID = 'cmis:versionSeriesId';

    /**
     * CMIS document property <code>cmis:isVersionSeriesCheckedOut</code> flag the
     * indicates if the document is checked out.
     *
     * CMIS data type: boolean
     * type: boolean
     */
    const IS_VERSION_SERIES_CHECKED_OUT = 'cmis:isVersionSeriesCheckedOut';

    /**
     * CMIS document property <code>cmis:versionSeriesCheckedOutBy</code> user who
     * checked out the document, if the document is checked out.
     *
     * CMIS data type: string
     * type: string
     */
    const VERSION_SERIES_CHECKED_OUT_BY = 'cmis:versionSeriesCheckedOutBy';

    /**
     * CMIS document property <code>cmis:versionSeriesCheckedOutId</code> ID of the
     * PWC, if the document is checked out.
     *
     * CMIS data type: id
     * type: string
     */
    const VERSION_SERIES_CHECKED_OUT_ID = 'cmis:versionSeriesCheckedOutId';

    /**
     * CMIS document property <code>cmis:checkinComment</code> check-in comment for
     * the document version.
     *
     * CMIS data type: string
     * type: string
     */
    const CHECKIN_COMMENT = 'cmis:checkinComment';

    /**
     * CMIS document property <code>cmis:contentStreamLength</code> length of the
     * content stream, if the document has content.
     *
     * CMIS data type: integer
     * type: BigInteger
     */
    const CONTENT_STREAM_LENGTH = 'cmis:contentStreamLength';

    /**
     * CMIS document property <code>cmis:contentStreamMimeType</code> MIME type of
     * the content stream, if the document has content.
     *
     * CMIS data type: string
     * type: string
     */
    const CONTENT_STREAM_MIME_TYPE = 'cmis:contentStreamMimeType';

    /**
     * CMIS document property <code>cmis:contentStreamFileName</code> file name, if
     * the document has content.
     *
     * CMIS data type: string
     * type: string
     */
    const CONTENT_STREAM_FILE_NAME = 'cmis:contentStreamFileName';

    /**
     * CMIS document property <code>cmis:contentStreamId</code> content stream ID.
     *
     * CMIS data type: id
     * type: string
     */
    const CONTENT_STREAM_ID = 'cmis:contentStreamId';

    /**
     * CMIS document property <code>cmis:isPrivateWorkingCopy</code> flag the
     * indicates if the document is a PWC.
     *
     * CMIS data type: boolean
     * type: boolean
     */
    const IS_PRIVATE_WORKING_COPY = 'cmis:isPrivateWorkingCopy';


    // ---- folder ----
    /**
     * CMIS folder property <code>cmis:parentId</code> ID of the parent folder.
     *
     * CMIS data type: id
     * type: string
     */
    const PARENT_ID = 'cmis:parentId';

    /**
     * CMIS folder property <code>cmis:allowedChildObjectTypeIds} (multivalue):
     * IDs of the types that can be filed in the folder.
     *
     * CMIS data type: id
     * type: string
     */
    const ALLOWED_CHILD_OBJECT_TYPE_IDS = 'cmis:allowedChildObjectTypeIds';

    /**
     * CMIS folder property <code>cmis:path</code> folder path.
     *
     * CMIS data type: string
     * type: string
     */
    const PATH = 'cmis:path';


    // ---- relationship ----
    /**
     * CMIS relationship property <code>cmis:sourceId</code> ID of the source
     * object.
     *
     * CMIS data type: id
     * type: string
     */
    const SOURCE_ID = 'cmis:sourceId';

    /**
     * CMIS relationship property <code>cmis:targetId</code> ID of the target
     * object.
     *
     * CMIS data type: id
     * type: string
     */
    const TARGET_ID = 'cmis:targetId';


    // ---- policy ----
    /**
     * CMIS policy property <code>cmis:policyText</code> policy text.
     *
     * CMIS data type: string
     * type: string
     */
    const POLICY_TEXT = 'cmis:policyText';


    // ---- retention ----
    /**
     * CMIS retention property <code>cmis:rm_expirationDate</code> expiration date.
     *
     * CMIS data type: datetime
     * type: DateTime
     */
    const EXPIRATION_DATE = 'cmis:rm_expirationDate';

    /**
     * CMIS retention property <code>cmis:rm_startOfRetention</code> start date.
     *
     * CMIS data type: datetime
     * type: DateTime
     */
    const START_OF_RETENTION = 'cmis:rm_startOfRetention';

    /**
     * CMIS retention property <code>cmis:rm_destructionDate</code> destruction
     * date.
     *
     * CMIS data type: datetime
     * type: DateTime
     */
    const DESTRUCTION_DATE = 'cmis:rm_destructionDate';

    /**
     * CMIS retention property <code>cmis:rm_holdIds} (multivalue): IDs of the
     * holds that are applied.
     *
     * CMIS data type: id
     * type: string
     */
    const HOLD_IDS = 'cmis:rm_holdIds';


    // ---- extensions ----
    /**
     * Content Hash property <code>cmis:contentStreamHash} (multivalue): hashes
     * of the content stream
     *
     * CMIS data type: string
     * type: string
     *
     * @cmis Extension
     */
    const CONTENT_STREAM_HASH = 'cmis:contentStreamHash';
}
