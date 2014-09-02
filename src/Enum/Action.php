<?php
namespace Dkd\PhpCmis\Enum;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\Enumeration\Enumeration;

/**
 * Action Enum.
 */
final class Action extends Enumeration
{
    const CAN_DELETE_OBJECT = 'canDeleteObject';
    const CAN_UPDATE_PROPERTIES = 'canUpdateProperties';
    const CAN_GET_FOLDER_TREE = 'canGetFolderTree';
    const CAN_GET_PROPERTIES = 'canGetProperties';
    const CAN_GET_OBJECT_RELATIONSHIPS = 'canGetObjectRelationships';
    const CAN_GET_OBJECT_PARENTS = 'canGetObjectParents';
    const CAN_GET_FOLDER_PARENT = 'canGetFolderParent';
    const CAN_GET_DESCENDANTS = 'canGetDescendants';
    const CAN_MOVE_OBJECT = 'canMoveObject';
    const CAN_DELETE_CONTENT_STREAM = 'canDeleteContentStream';
    const CAN_CHECK_OUT = 'canCheckOut';
    const CAN_CANCEL_CHECK_OUT = 'canCancelCheckOut';
    const CAN_CHECK_IN = 'canCheckIn';
    const CAN_SET_CONTENT_STREAM = 'canSetContentStream';
    const CAN_GET_ALL_VERSIONS = 'canGetAllVersions';
    const CAN_ADD_OBJECT_TO_FOLDER = 'canAddObjectToFolder';
    const CAN_REMOVE_OBJECT_FROM_FOLDER = 'canRemoveObjectFromFolder';
    const CAN_GET_CONTENT_STREAM = 'canGetContentStream';
    const CAN_APPLY_POLICY = 'canApplyPolicy';
    const CAN_GET_APPLIED_POLICIES = 'canGetAppliedPolicies';
    const CAN_REMOVE_POLICY = 'canRemovePolicy';
    const CAN_GET_CHILDREN = 'canGetChildren';
    const CAN_CREATE_DOCUMENT = 'canCreateDocument';
    const CAN_CREATE_FOLDER = 'canCreateFolder';
    const CAN_CREATE_RELATIONSHIP = 'canCreateRelationship';
    const CAN_CREATE_ITEM = 'canCreateItem';
    const CAN_DELETE_TREE = 'canDeleteTree';
    const CAN_GET_RENDITIONS = 'canGetRenditions';
    const CAN_GET_ACL = 'canGetACL';
    const CAN_APPLY_ACL = 'canApplyACL';
}
