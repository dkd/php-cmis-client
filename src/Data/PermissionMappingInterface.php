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
 * Permission Mapping
 */
interface PermissionMappingInterface extends ExtensionDataInterface
{
    const CAN_GET_DESCENDENTS_FOLDER = 'canGetDescendents.Folder';
    const CAN_GET_CHILDREN_FOLDER = 'canGetChildren.Folder';
    const CAN_GET_PARENTS_FOLDER = 'canGetParents.Folder';
    const CAN_GET_FOLDER_PARENT_OBJECT = 'canGetFolderParent.Object';
    const CAN_CREATE_DOCUMENT_FOLDER = 'canCreateDocument.Folder';
    const CAN_CREATE_FOLDER_FOLDER = 'canCreateFolder.Folder';
    const CAN_CREATE_POLICY_FOLDER = 'canCreatePolicy.Folder';
    const CAN_CREATE_RELATIONSHIP_SOURCE = 'canCreateRelationship.Source';
    const CAN_CREATE_RELATIONSHIP_TARGET = 'canCreateRelationship.Target';
    const CAN_GET_PROPERTIES_OBJECT = 'canGetProperties.Object';
    const CAN_VIEW_CONTENT_OBJECT = 'canViewContent.Object';
    const CAN_UPDATE_PROPERTIES_OBJECT = 'canUpdateProperties.Object';
    const CAN_MOVE_OBJECT = 'canMove.Object';
    const CAN_MOVE_TARGET = 'canMove.Target';
    const CAN_MOVE_SOURCE = 'canMove.Source';
    const CAN_DELETE_OBJECT = 'canDelete.Object';
    const CAN_DELETE_TREE_FOLDER = 'canDeleteTree.Folder';
    const CAN_SET_CONTENT_DOCUMENT = 'canSetContent.Document';
    const CAN_DELETE_CONTENT_DOCUMENT = 'canDeleteContent.Document';
    const CAN_ADD_TO_FOLDER_OBJECT = 'canAddToFolder.Object';
    const CAN_ADD_TO_FOLDER_FOLDER = 'canAddToFolder.Folder';
    const CAN_REMOVE_FROM_FOLDER_OBJECT = 'canRemoveFromFolder.Object';
    const CAN_REMOVE_FROM_FOLDER_FOLDER = 'canRemoveFromFolder.Folder';
    const CAN_CHECKOUT_DOCUMENT = 'canCheckout.Document';
    const CAN_CANCEL_CHECKOUT_DOCUMENT = 'canCancelCheckout.Document';
    const CAN_CHECKIN_DOCUMENT = 'canCheckin.Document';
    const CAN_GET_ALL_VERSIONS_VERSION_SERIES = 'canGetAllVersions.VersionSeries';
    const CAN_GET_OBJECT_RELATIONSHIPS_OBJECT = 'canGetObjectRelationships.Object';
    const CAN_ADD_POLICY_OBJECT = 'canAddPolicy.Object';
    const CAN_ADD_POLICY_POLICY = 'canAddPolicy.Policy';
    const CAN_REMOVE_POLICY_OBJECT = 'canRemovePolicy.Object';
    const CAN_REMOVE_POLICY_POLICY = 'canRemovePolicy.Policy';
    const CAN_GET_APPLIED_POLICIES_OBJECT = 'canGetAppliedPolicies.Object';
    const CAN_GET_ACL_OBJECT = 'canGetACL.Object';
    const CAN_APPLY_ACL_OBJECT = 'canApplyACL.Object';

    /**
     * @return string
     */
    public function getKey();

    /**
     * @return string[]
     */
    public function getPermissions();
}
