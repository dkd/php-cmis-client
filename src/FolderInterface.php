<?php
namespace Dkd\PhpCmis;

use Dkd\PhpCmis\CmisObject\CmisObjectInterface;
use Dkd\PhpCmis\Data\ContentStreamInterface;
use Dkd\PhpCmis\Enum\UnfileObject;
use Dkd\PhpCmis\Enum\VersioningState;

/**
 * CMIS folder interface.
 */
interface FolderInterface extends FileableCmisObjectInterface, FolderPropertiesInterface
{
    /**
     * Creates a new document in this folder.
     *
     * @param array $properties
     * @param ContentStreamInterface $contentStream
     * @param VersioningState $versioningState
     * @param PolicyInterface[] $policies
     * @param AceInterface[] $addAces
     * @param AceInterface[] $removeAces
     * @param OperationContextInterface $context
     * @return DocumentInterface the new document object
     */
    public function createDocument(
        $properties,
        ContentStreamInterface $contentStream,
        VersioningState $versioningState,
        array $policies = array(),
        array $addAces = array(),
        array $removeAces = array(),
        OperationContextInterface $context = null
    );

    /**
     * Creates a new document from a source document in this folder.
     *
     * @param ObjectIdInterface $source
     * @param array $properties
     * @param VersioningState $versioningState
     * @param PolicyInterface[] $policies
     * @param AceInterface[] $addAces
     * @param AceInterface[] $removeAces
     * @param OperationContextInterface $context
     * @return DocumentInterface Creates a new document from a source document in this folder.
     */
    public function createDocumentFromSource(
        ObjectIdInterface $source,
        array $properties,
        VersioningState $versioningState,
        array $policies = array(),
        array $addAces = array(),
        array $removeAces = array(),
        OperationContextInterface $context = null
    );

    /**
     * Creates a new subfolder in this folder.
     *
     * @param array $properties
     * @param PolicyInterface[] $policies
     * @param AceInterface[] $addAces
     * @param AceInterface[] $removeAces
     * @param OperationContextInterface $context
     * @return FolderInterface the new folder object
     */
    public function createFolder(
        array $properties,
        array $policies = array(),
        array $addAces = array(),
        array $removeAces = array(),
        OperationContextInterface $context = null
    );

    /**
     * Creates a new item in this folder.
     *
     * @param array $properties
     * @param PolicyInterface[] $policies
     * @param AceInterface[] $addAces
     * @param AceInterface[] $removeAces
     * @param OperationContextInterface $context
     * @return ItemInterface the new item object
     */
    public function createItem(
        array $properties,
        array $policies = array(),
        array $addAces = array(),
        array $removeAces = array(),
        OperationContextInterface $context = null
    );

    /**
     * Creates a new policy in this folder.
     *
     * @param array $properties
     * @param PolicyInterface[] $policies
     * @param AceInterface[] $addAces
     * @param AceInterface[] $removeAces
     * @param OperationContextInterface $context
     * @return PolicyInterface the new policy object
     */
    public function createPolicy(
        array $properties,
        array $policies = array(),
        array $addAces = array(),
        array $removeAces = array(),
        OperationContextInterface $context = null
    );

    /**
     * Deletes this folder and all subfolders.
     *
     * @param boolean $allVersions
     * @param UnfileObject $unfile
     * @param boolean $continueOnFailure
     * @return string[] a list of object IDs which failed to be deleted
     */
    public function deleteTree($allVersions, UnfileObject $unfile, $continueOnFailure);

    /**
     * Returns all checked out documents in this folder using the given OperationContext.
     *
     * @param OperationContextInterface $context
     * @return DocumentInterface[]
     */
    public function getCheckedOutDocs(OperationContextInterface $context = null);

    /**
     * Returns the children of this folder using the given OperationContext.
     *
     * @param OperationContextInterface $context
     * @return CmisObjectInterface[]
     */
    public function getChildren(OperationContextInterface $context = null);

    /**
     * Gets the folder descendants starting with this folder.
     *
     * @param int $depth
     * @param OperationContextInterface $context
     * @return Tree<FileableCmisObject>
     */
    public function getDescendants($depth, OperationContextInterface $context = null);

    /**
     * Gets the parent folder object.
     *
     * @return FolderInterface|null the parent folder object or null if the folder is the root folder.
     */
    public function getFolderParent();

    /**
     * Gets the folder tree starting with this folder using the given OperationContext.
     *
     * @param $depth
     * @param OperationContextInterface $context
     * @return Tree<FileableCmisObject>
     */
    public function getFolderTree($depth, OperationContextInterface $context = null);

    /**
     * Returns the path of the folder.
     *
     * @return string the absolute folder path
     */
    public function getPath();

    /**
     * Returns if the folder is the root folder.
     *
     * @return boolean true if the folder is the root folder, false otherwise
     */
    public function isRootFolder();
}
