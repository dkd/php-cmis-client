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
 * Constants for CMIS.
 */
class Constants
{
    // media types
    const MEDIATYPE_SERVICE = 'application/atomsvc+xml';
    const MEDIATYPE_FEED = 'application/atom+xml;type=feed';
    const MEDIATYPE_ENTRY = 'application/atom+xml;type=entry';
    const MEDIATYPE_CHILDREN = self::MEDIATYPE_FEED;
    const MEDIATYPE_DESCENDANTS = 'application/cmistree+xml';
    const MEDIATYPE_QUERY = 'application/cmisquery+xml';
    const MEDIATYPE_ALLOWABLEACTION = 'application/cmisallowableactions+xml';
    const MEDIATYPE_ACL = 'application/cmisacl+xml';
    const MEDIATYPE_CMISATOM = 'application/cmisatom+xml';
    const MEDIATYPE_OCTETSTREAM = 'application/octet-stream';

    // collections
    const COLLECTION_ROOT = 'root';
    const COLLECTION_TYPES = 'types';
    const COLLECTION_QUERY = 'query';
    const COLLECTION_CHECKEDOUT = 'checkedout';
    const COLLECTION_UNFILED = 'unfiled';
    const COLLECTION_BULK_UPDATE = 'update';

    // URI templates
    const TEMPLATE_OBJECT_BY_ID = 'objectbyid';
    const TEMPLATE_OBJECT_BY_PATH = 'objectbypath';
    const TEMPLATE_TYPE_BY_ID = 'typebyid';
    const TEMPLATE_QUERY = 'query';

    // Link rel
    const REL_SELF = 'self';
    const REL_ENCLOSURE = 'enclosure';
    const REL_SERVICE = 'service';
    const REL_DESCRIBEDBY = 'describedby';
    const REL_ALTERNATE = 'alternate';
    const REL_DOWN = 'down';
    const REL_UP = 'up';
    const REL_FIRST = 'first';
    const REL_LAST = 'last';
    const REL_PREV = 'previous';
    const REL_NEXT = 'next';
    const REL_VIA = 'via';
    const REL_EDIT = 'edit';
    const REL_EDITMEDIA = 'edit-media';
    const REL_VERSIONHISTORY = 'version-history';
    const REL_CURRENTVERSION = 'current-version';
    const REL_WORKINGCOPY = 'working-copy';
    const REL_FOLDERTREE = 'http://docs.oasis-open.org/ns/cmis/link/200908/foldertree';
    const REL_ALLOWABLEACTIONS = 'http://docs.oasis-open.org/ns/cmis/link/200908/allowableactions';
    const REL_ACL = 'http://docs.oasis-open.org/ns/cmis/link/200908/acl';
    const REL_SOURCE = 'http://docs.oasis-open.org/ns/cmis/link/200908/source';
    const REL_TARGET = 'http://docs.oasis-open.org/ns/cmis/link/200908/target';

    const REL_RELATIONSHIPS = 'http://docs.oasis-open.org/ns/cmis/link/200908/relationships';
    const REL_POLICIES = 'http://docs.oasis-open.org/ns/cmis/link/200908/policies';

    const REP_REL_TYPEDESC = 'http://docs.oasis-open.org/ns/cmis/link/200908/typedescendants';
    const REP_REL_FOLDERTREE = 'http://docs.oasis-open.org/ns/cmis/link/200908/foldertree';
    const REP_REL_ROOTDESC = 'http://docs.oasis-open.org/ns/cmis/link/200908/rootdescendants';
    const REP_REL_CHANGES = 'http://docs.oasis-open.org/ns/cmis/link/200908/changes';

    // browser binding selectors
    const SELECTOR_LAST_RESULT = 'lastResult';
    const SELECTOR_REPOSITORY_INFO = 'repositoryInfo';
    const SELECTOR_TYPE_CHILDREN = 'typeChildren';
    const SELECTOR_TYPE_DESCENDANTS = 'typeDescendants';
    const SELECTOR_TYPE_DEFINITION = 'typeDefinition';
    const SELECTOR_CONTENT = 'content';
    const SELECTOR_OBJECT = 'object';
    const SELECTOR_PROPERTIES = 'properties';
    const SELECTOR_ALLOWABLEACTIONS = 'allowableActions';
    const SELECTOR_RENDITIONS = 'renditions';
    const SELECTOR_CHILDREN = 'children';
    const SELECTOR_DESCENDANTS = 'descendants';
    const SELECTOR_PARENTS = 'parents';
    const SELECTOR_PARENT = 'parent';
    const SELECTOR_FOLDER_TREE = 'folderTree';
    const SELECTOR_QUERY = 'query';
    const SELECTOR_VERSIONS = 'versions';
    const SELECTOR_RELATIONSHIPS = 'relationships';
    const SELECTOR_CHECKEDOUT = 'checkedout';
    const SELECTOR_POLICIES = 'policies';
    const SELECTOR_ACL = 'acl';
    const SELECTOR_CONTENT_CHANGES = 'contentChanges';

    // browser binding actions
    const CMISACTION_CREATE_TYPE = 'createType';
    const CMISACTION_UPDATE_TYPE = 'updateType';
    const CMISACTION_DELETE_TYPE = 'deleteType';
    const CMISACTION_CREATE_DOCUMENT = 'createDocument';
    const CMISACTION_CREATE_DOCUMENT_FROM_SOURCE = 'createDocumentFromSource';
    const CMISACTION_CREATE_FOLDER = 'createFolder';
    const CMISACTION_CREATE_RELATIONSHIP = 'createRelationship';
    const CMISACTION_CREATE_POLICY = 'createPolicy';
    const CMISACTION_CREATE_ITEM = 'createItem';
    const CMISACTION_UPDATE_PROPERTIES = 'update';
    const CMISACTION_BULK_UPDATE = 'bulkUpdate';
    const CMISACTION_DELETE_CONTENT = 'deleteContent';
    const CMISACTION_SET_CONTENT = 'setContent';
    const CMISACTION_APPEND_CONTENT = 'appendContent';
    const CMISACTION_DELETE = 'delete';
    const CMISACTION_DELETE_TREE = 'deleteTree';
    const CMISACTION_MOVE = 'move';
    const CMISACTION_ADD_OBJECT_TO_FOLDER = 'addObjectToFolder';
    const CMISACTION_REMOVE_OBJECT_FROM_FOLDER = 'removeObjectFromFolder';
    const CMISACTION_QUERY = 'query';
    const CMISACTION_CHECK_OUT = 'checkOut';
    const CMISACTION_CANCEL_CHECK_OUT = 'cancelCheckOut';
    const CMISACTION_CHECK_IN = 'checkIn';
    const CMISACTION_APPLY_POLICY = 'applyPolicy';
    const CMISACTION_REMOVE_POLICY = 'removePolicy';
    const CMISACTION_APPLY_ACL = 'applyACL';

    // browser binding control
    const CONTROL_CMISACTION = 'cmisaction';
    const CONTROL_SUCCINCT = 'succinct';
    const CONTROL_TOKEN = 'token';
    const CONTROL_OBJECT_ID = 'objectId';
    const CONTROL_PROP_ID = 'propertyId';
    const CONTROL_PROP_VALUE = 'propertyValue';
    const CONTROL_POLICY = 'policy';
    const CONTROL_ADD_ACE_PRINCIPAL = 'addACEPrincipal';
    const CONTROL_ADD_ACE_PERMISSION = 'addACEPermission';
    const CONTROL_REMOVE_ACE_PRINCIPAL = 'removeACEPrincipal';
    const CONTROL_REMOVE_ACE_PERMISSION = 'removeACEPermission';
    const CONTROL_CONTENT_TYPE = 'contenttype';
    const CONTROL_FILENAME = 'filename';
    const CONTROL_IS_LAST_CHUNK = 'isLastChunk';
    const CONTROL_TYPE = 'type';
    const CONTROL_TYPE_ID = 'typeId';
    const CONTROL_CHANGE_TOKEN = 'changeToken';
    const CONTROL_ADD_SECONDARY_TYPE = 'addSecondaryTypeId';
    const CONTROL_REMOVE_SECONDARY_TYPE = 'removeSecondaryTypeId';

    // parameter
    const PARAM_ACL = 'includeACL';
    const PARAM_ALLOWABLE_ACTIONS = 'includeAllowableActions';
    const PARAM_ALL_VERSIONS = 'allVersions';
    const PARAM_APPEND = 'append';
    const PARAM_CHANGE_LOG_TOKEN = 'changeLogToken';
    const PARAM_CHANGE_TOKEN = 'changeToken';
    const PARAM_CHECKIN_COMMENT = 'checkinComment';
    const PARAM_CHECK_IN = 'checkin';
    const PARAM_CHILD_TYPES = 'childTypes';
    const PARAM_CONTINUE_ON_FAILURE = 'continueOnFailure';
    const PARAM_DEPTH = 'depth';
    const PARAM_DOWNLOAD = 'download';
    const PARAM_FILTER = 'filter';
    const PARAM_SUCCINCT = 'succinct';
    const PARAM_DATETIME_FORMAT = 'dateTimeFormat';
    const PARAM_FOLDER_ID = 'folderId';
    const PARAM_ID = 'id';
    const PARAM_IS_LAST_CHUNK = 'isLastChunk';
    const PARAM_MAJOR = 'major';
    const PARAM_MAX_ITEMS = 'maxItems';
    const PARAM_OBJECT_ID = 'objectId';
    const PARAM_ONLY_BASIC_PERMISSIONS = 'onlyBasicPermissions';
    const PARAM_ORDER_BY = 'orderBy';
    const PARAM_OVERWRITE_FLAG = 'overwriteFlag';
    const PARAM_PATH = 'path';
    const PARAM_PATH_SEGMENT = 'includePathSegment';
    const PARAM_POLICY_ID = 'policyId';
    const PARAM_POLICY_IDS = 'includePolicyIds';
    const PARAM_PROPERTIES = 'includeProperties';
    const PARAM_PROPERTY_DEFINITIONS = 'includePropertyDefinitions';
    const PARAM_RELATIONSHIPS = 'includeRelationships';
    const PARAM_RELATIONSHIP_DIRECTION = 'relationshipDirection';
    const PARAM_RELATIVE_PATH_SEGMENT = 'includeRelativePathSegment';
    const PARAM_REMOVE_FROM = 'removeFrom';
    const PARAM_RENDITION_FILTER = 'renditionFilter';
    const PARAM_REPOSITORY_ID = 'repositoryId';
    const PARAM_RETURN_VERSION = 'returnVersion';
    const PARAM_ROPERTY_DEFINITIONS = 'includePropertyDefinitions';
    const PARAM_SKIP_COUNT = 'skipCount';
    const PARAM_SOURCE_FOLDER_ID = 'sourceFolderId';
    const PARAM_TARGET_FOLDER_ID = 'targetFolderId';
    const PARAM_STREAM_ID = 'streamId';
    const PARAM_SUB_RELATIONSHIP_TYPES = 'includeSubRelationshipTypes';
    const PARAM_TYPE_ID = 'typeId';
    const PARAM_UNFILE_OBJECTS = 'unfileObjects';
    const PARAM_VERSION_SERIES_ID = 'versionSeries';
    const PARAM_VERSIONING_STATE = 'versioningState';
    const PARAM_Q = 'q';
    const PARAM_STATEMENT = 'statement';
    const PARAM_SEARCH_ALL_VERSIONS = 'searchAllVersions';
    const PARAM_ACL_PROPAGATION = 'ACLPropagation';
    const PARAM_SOURCE_ID = 'sourceId';

    const PARAM_SELECTOR = 'cmisselector';
    const PARAM_CALLBACK = 'callback';
    const PARAM_SUPPRESS_RESPONSE_CODES = 'suppressResponseCodes';
    const PARAM_TOKEN = 'token';

    // rendition filter
    const RENDITION_NONE = 'cmis:none';

    // query datetime format
    const QUERY_DATETIMEFORMAT = 'Y-m-d\TH:i:s.uP';
}
