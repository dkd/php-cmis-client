<?php
namespace Dkd\PhpCmis\Bindings\Browser;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Data\ExtensionDataInterface;
use Dkd\PhpCmis\Data\RepositoryInfoInterface;
use Dkd\PhpCmis\Definitions\TypeDefinitionContainerInterface;
use Dkd\PhpCmis\Definitions\TypeDefinitionListInterface;
use Dkd\PhpCmis\Exception\CmisObjectNotFoundException;
use Dkd\PhpCmis\RepositoryServiceInterface;
use Dkd\PhpCmis\TypeDefinitionInterface;

/**
 * Repository Service Browser Binding client.
 */
class RepositoryService extends AbstractBrowserBindingService implements RepositoryServiceInterface
{
    /**
     * Creates a new type.
     *
     * @param string $repositoryId
     * @param TypeDefinitionInterface $type
     * @param ExtensionDataInterface $extension
     * @return TypeDefinitionInterface
     */
    public function createType($repositoryId, TypeDefinitionInterface $type, ExtensionDataInterface $extension = null)
    {
        // TODO: Implement createType() method.
    }

    /**
     * Deletes a type.
     *
     * @param string $repositoryId
     * @param string $typeId
     * @param ExtensionDataInterface $extension
     * @return void
     */
    public function deleteType($repositoryId, $typeId, ExtensionDataInterface $extension = null)
    {
        // TODO: Implement deleteType() method.
    }

    /**
     * Returns information about the CMIS repository, the optional capabilities it
     * supports and its access control information if applicable.
     *
     * @param string $repositoryId
     * @param ExtensionDataInterface $extension
     * @throws CmisObjectNotFoundException
     * @return RepositoryInfoInterface
     */
    public function getRepositoryInfo($repositoryId, ExtensionDataInterface $extension = null)
    {
        foreach ($this->getRepositoriesInternal($repositoryId) as $repositoryInfo) {
            if ($repositoryInfo->getId() === $repositoryId) {
                return $repositoryInfo;
            }
        }

        throw new CmisObjectNotFoundException(sprintf('Repository "%s" not found!', $repositoryId));
    }

    /**
     * Returns a list of CMIS repository information available from this CMIS service endpoint.
     * In contrast to the CMIS specification this method returns repository infos not only repository ids.
     *
     * @param ExtensionDataInterface $extension
     * @return RepositoryInfoInterface[]
     */
    public function getRepositoryInfos(ExtensionDataInterface $extension = null)
    {
        return $this->getRepositoriesInternal();
    }

    /**
     * Returns the list of object types defined for the repository that are children of the specified type.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $typeId the typeId of an object type specified in the repository
     * (if not specified the repository MUST return all base object types)
     * @param boolean $includePropertyDefinitions if true the repository MUST return the property
     * definitions for each object type returned (default is false)
     * @param integer $maxItems the maximum number of items to return in a response (default is repository specific)
     * @param integer $skipCount number of potential results that the repository MUST skip/page over
     * before returning any results (default is 0)
     * @param ExtensionDataInterface $extension
     * @return TypeDefinitionListInterface
     */
    public function getTypeChildren(
        $repositoryId,
        $typeId = null,
        $includePropertyDefinitions = false,
        $maxItems = null,
        $skipCount = 0,
        ExtensionDataInterface $extension = null
    ) {
        // TODO: Implement getTypeChildren() method.
    }

    /**
     * Gets the definition of the specified object type.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $typeId he type definition
     * @param ExtensionDataInterface $extension
     * @return TypeDefinitionInterface the newly created type
     */
    public function getTypeDefinition($repositoryId, $typeId, ExtensionDataInterface $extension = null)
    {
        // TODO: Implement getTypeDefinition() method.
    }

    /**
     * Returns the set of descendant object type defined for the repository under the specified type.
     *
     * @param string $repositoryId repositoryId - the identifier for the repository
     * @param string $typeId the typeId of an object type specified in the repository
     * (if not specified the repository MUST return all types and MUST ignore the value of the depth parameter)
     * @param integer $depth the number of levels of depth in the type hierarchy from which
     * to return results (default is repository specific)
     * @param boolean $includePropertyDefinitions if true the repository MUST return the property
     * definitions for each object type returned (default is false)
     * @param ExtensionDataInterface $extension
     * @return TypeDefinitionContainerInterface[]
     */
    public function getTypeDescendants(
        $repositoryId,
        $typeId = null,
        $depth = null,
        $includePropertyDefinitions = false,
        ExtensionDataInterface $extension = null
    ) {
        // TODO: Implement getTypeDescendants() method.
    }

    /**
     * Updates a type.
     *
     * @param string $repositoryId the identifier for the repository
     * @param TypeDefinitionInterface $type the type definition
     * @param ExtensionDataInterface $extension
     * @return TypeDefinitionInterface the updated type
     */
    public function updateType($repositoryId, TypeDefinitionInterface $type, ExtensionDataInterface $extension = null)
    {
        // TODO: Implement updateType() method.
    }
}
