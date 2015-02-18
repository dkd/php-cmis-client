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

use Dkd\PhpCmis\Data\ExtensionDataInterface;
use Dkd\PhpCmis\Data\ObjectDataInterface;
use Dkd\PhpCmis\Data\ObjectInFolderContainerInterface;
use Dkd\PhpCmis\Data\ObjectInFolderListInterface;
use Dkd\PhpCmis\Data\ObjectListInterface;
use Dkd\PhpCmis\Data\ObjectParentDataInterface;
use Dkd\PhpCmis\Enum\IncludeRelationships;
use phpDocumentor\Descriptor\Builder\Reflector\ConstantAssemblerTest;

/**
 * Navigation Service interface.
 *
 * See the CMIS 1.0 and CMIS 1.1 specifications for details on the operations,
 * parameters, exceptions and the domain model.
 */
interface NavigationServiceInterface
{
    /**
     * Gets the list of documents that are checked out that the user has access to.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $folderId the identifier for the folder
     * @param string|null $filter a comma-separated list of query names that defines which properties must be
     *      returned by the repository (default is repository specific)
     * @param string|null $orderBy a comma-separated list of query names that define the order of the result set.
     *      Each query name must be followed by the ascending modifier "ASC" or the descending modifier "DESC"
     *      (default is repository specific)
     * @param boolean $includeAllowableActions if <code>true</code>, then the repository must return the available
     *      actions for each object in the result set (default is <code>false</code>)
     * @param IncludeRelationships|null $includeRelationships indicates what relationships in which the objects
     *      participate must be returned (default is IncludeRelationships::NONE)
     * @param string $renditionFilter indicates what set of renditions the repository must return whose kind
     * matches this filter (default is "cmis:none")
     * @param integer|null $maxItems the maximum number of items to return in a response
     *      (default is repository specific)
     * @param integer $skipCount number of potential results that the repository MUST skip/page over before
     *      returning any results (default is 0)
     * @param ExtensionDataInterface|null $extension
     * @return ObjectListInterface
     */
    public function getCheckedOutDocs(
        $repositoryId,
        $folderId,
        $filter = null,
        $orderBy = null,
        $includeAllowableActions = false,
        IncludeRelationships $includeRelationships = null,
        $renditionFilter = Constants::RENDITION_NONE,
        $maxItems = null,
        $skipCount = 0,
        ExtensionDataInterface $extension = null
    );

    /**
     * Gets the list of child objects contained in the specified folder.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $folderId the identifier for the folder
     * @param string|null $filter a comma-separated list of query names that defines which properties must be
     *      returned by the repository (default is repository specific)
     * @param string|null $orderBy a comma-separated list of query names that define the order of the result set.
     *      Each query name must be followed by the ascending modifier "ASC" or the descending modifier "DESC"
     *      (default is repository specific)
     * @param boolean $includeAllowableActions if <code>true</code>, then the repository must return the available
     *      actions for each object in the result set (default is <code>false</code>)
     * @param IncludeRelationships|null $includeRelationships indicates what relationships in which the objects
     *      participate must be returned (default is IncludeRelationships::NONE)
     * @param string $renditionFilter indicates what set of renditions the repository must return whose kind
     *      matches this filter (default is "cmis:none")
     * @param boolean $includePathSegment if <code>true</code>, returns a path segment for each child object for use in
     *      constructing that object's path (default is <code>false</code>)
     * @param integer|null $maxItems the maximum number of items to return in a response
     *      (default is repository specific)
     * @param integer $skipCount number of potential results that the repository MUST skip/page over before
     *      returning any results (default is 0)
     * @param ExtensionDataInterface|null $extension
     * @return ObjectInFolderListInterface
     */
    public function getChildren(
        $repositoryId,
        $folderId,
        $filter = null,
        $orderBy = null,
        $includeAllowableActions = false,
        IncludeRelationships $includeRelationships = null,
        $renditionFilter = Constants::RENDITION_NONE,
        $includePathSegment = false,
        $maxItems = null,
        $skipCount = 0,
        ExtensionDataInterface $extension = null
    );

    /**
     * Gets the set of descendant objects contained in the specified folder or any of its child folders.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $folderId the identifier for the folder
     * @param integer $depth the number of levels of depth in the folder hierarchy from which to return results
     * @param string|null $filter a comma-separated list of query names that defines which properties must be
     *      returned by the repository (default is repository specific)
     * @param boolean $includeAllowableActions if <code>true</code>, then the repository must return the available
     *      actions for each object in the result set (default is <code>false</code>)
     * @param IncludeRelationships|null $includeRelationships indicates what relationships in which the objects
     *      participate must be returned (default is IncludeRelationships::NONE)
     * @param string $renditionFilter indicates what set of renditions the repository must return whose kind
     *      matches this filter (default is "cmis:none")
     * @param boolean $includePathSegment if <code>true</code>, returns a path segment for each child object for use in
     *      constructing that object's path (default is <code>false</code>)
     * @param ExtensionDataInterface|null $extension
     * @return ObjectInFolderContainerInterface[]
     */
    public function getDescendants(
        $repositoryId,
        $folderId,
        $depth,
        $filter = null,
        $includeAllowableActions = false,
        IncludeRelationships $includeRelationships = null,
        $renditionFilter = Constants::RENDITION_NONE,
        $includePathSegment = false,
        ExtensionDataInterface $extension = null
    );

    /**
     * Gets the parent folder object for the specified folder object.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $folderId the identifier for the folder
     * @param string|null $filter a comma-separated list of query names that defines which properties must be
     *      returned by the repository (default is repository specific)
     * @param ExtensionDataInterface|null $extension
     * @return ObjectDataInterface
     */
    public function getFolderParent(
        $repositoryId,
        $folderId,
        $filter = null,
        ExtensionDataInterface $extension = null
    );

    /**
     * Gets the set of descendant folder objects contained in the specified folder.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $folderId the identifier for the folder
     * @param integer $depth the number of levels of depth in the folder hierarchy from which to return results
     * @param string|null $filter a comma-separated list of query names that defines which properties must be
     *      returned by the repository (default is repository specific)
     * @param boolean $includeAllowableActions if <code>true</code>, then the repository must return the available
     *      actions for each object in the result set (default is <code>false</code>)
     * @param IncludeRelationships|null $includeRelationships indicates what relationships in which the objects
     *      participate must be returned (default is IncludeRelationships::NONE)
     * @param string $renditionFilter indicates what set of renditions the repository must return whose kind
     *      matches this filter (default is "cmis:none")
     * @param boolean $includePathSegment if <code>true</code>, returns a path segment for each child object for use in
     *      constructing that object's path (default is <code>false</code>)
     * @param ExtensionDataInterface|null $extension
     * @return ObjectInFolderContainerInterface[]
     */
    public function getFolderTree(
        $repositoryId,
        $folderId,
        $depth,
        $filter = null,
        $includeAllowableActions = false,
        IncludeRelationships $includeRelationships = null,
        $renditionFilter = Constants::RENDITION_NONE,
        $includePathSegment = false,
        ExtensionDataInterface $extension = null
    );

    /**
     * Gets the parent folder(s) for the specified non-folder, fileable object
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $objectId the identifier for the object
     * @param string|null $filter a comma-separated list of query names that defines which properties must be
     *      returned by the repository (default is repository specific)
     * @param boolean $includeAllowableActions if <code>true</code>, then the repository must return the available
     *      actions for each object in the result set (default is <code>false</code>)
     * @param IncludeRelationships|null $includeRelationships indicates what relationships in which the objects
     *      participate must be returned (default is IncludeRelationships::NONE)
     * @param string $renditionFilter indicates what set of renditions the repository must return whose kind
     *      matches this filter (default is "cmis:none")
     * @param boolean $includeRelativePathSegment if <code>true</code>, returns a relative path segment for each parent
     *      object for use in constructing that object's path (default is <code>false</code>)
     * @param ExtensionDataInterface|null $extension
     * @return ObjectParentDataInterface[]
     */
    public function getObjectParents(
        $repositoryId,
        $objectId,
        $filter = null,
        $includeAllowableActions = false,
        IncludeRelationships $includeRelationships = null,
        $renditionFilter = Constants::RENDITION_NONE,
        $includeRelativePathSegment = false,
        ExtensionDataInterface $extension = null
    );
}
