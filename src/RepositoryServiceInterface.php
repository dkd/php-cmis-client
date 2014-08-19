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

use Dkd\PhpCmis\Data\ExtensionsDataInterface;
use Dkd\PhpCmis\Data\RepositoryInfoInterface;
use Dkd\PhpCmis\Definitions\TypeDefinitionContainerInterface;
use Dkd\PhpCmis\Definitions\TypeDefinitionListInterface;

/**
 * Repository Service interface.
 *
 * See the CMIS 1.0 and CMIS 1.1 specifications for details on the operations,
 * parameters, exceptions and the domain model.
 */
interface RepositoryServiceInterface
{
    /**
     * Creates a new type.
     *
     * @param string $repositoryId
     * @param TypeDefinitionInterface $type
     * @param ExtensionsDataInterface $extension
     * @return TypeDefinitionInterface
     */
    public function createType($repositoryId, TypeDefinitionInterface $type, ExtensionsDataInterface $extension = null);

    /**
     * Deletes a type.
     *
     * @param string $repositoryId
     * @param string $typeId
     * @param ExtensionsDataInterface $extension
     * @return void
     */
    public function deleteType($repositoryId, $typeId, ExtensionsDataInterface $extension = null);

    /**
     * Returns information about the CMIS repository, the optional capabilities it
     * supports and its access control information if applicable.
     *
     * @param string $repositoryId
     * @param ExtensionsDataInterface $extension
     * @return RepositoryInfoInterface
     */
    public function getRepositoryInfo($repositoryId, ExtensionsDataInterface $extension = null);

    /**
     * Returns a list of CMIS repository information available from this CMIS service endpoint.
     * In contrast to the CMIS specification this method returns repository infos not only repository ids.
     *
     * @param ExtensionsDataInterface $extension
     * @return RepositoryInfoInterface[]
     */
    public function getRepositoryInfos(ExtensionsDataInterface $extension = null);

    /**
     * Returns the list of object types defined for the repository that are children of the specified type.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $typeId the typeId of an object type specified in the repository
     * (if not specified the repository MUST return all base object types)
     * @param boolean $includePropertyDefinitions if true the repository MUST return the property
     * definitions for each object type returned (default is false)
     * @param int $maxItems the maximum number of items to return in a response (default is repository specific)
     * @param int $skipCount number of potential results that the repository MUST skip/page over
     * before returning any results (default is 0)
     * @param ExtensionsDataInterface $extension
     * @return TypeDefinitionListInterface
     */
    public function getTypeChildren(
        $repositoryId,
        $typeId = null,
        $includePropertyDefinitions = false,
        $maxItems = null,
        $skipCount = 0,
        ExtensionsDataInterface $extension = null
    );

    /**
     * Gets the definition of the specified object type.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $typeId he type definition
     * @param ExtensionsDataInterface $extension
     * @return TypeDefinitionInterface the newly created type
     */
    public function getTypeDefinition($repositoryId, $typeId, ExtensionsDataInterface $extension = null);

    /**
     * Returns the set of descendant object type defined for the repository under the specified type.
     *
     * @param string $repositoryId repositoryId - the identifier for the repository
     * @param string $typeId the typeId of an object type specified in the repository
     * (if not specified the repository MUST return all types and MUST ignore the value of the depth parameter)
     * @param int $depth the number of levels of depth in the type hierarchy from which
     * to return results (default is repository specific)
     * @param boolean $includePropertyDefinitions if true the repository MUST return the property
     * definitions for each object type returned (default is false)
     * @param ExtensionsDataInterface $extension
     * @return TypeDefinitionContainerInterface[]
     */
    public function getTypeDescendants(
        $repositoryId,
        $typeId = null,
        $depth = null,
        $includePropertyDefinitions = false,
        ExtensionsDataInterface $extension = null
    );

    /**
     * Updates a type.
     *
     * @param string $repositoryId the identifier for the repository
     * @param TypeDefinitionInterface $type the type definition
     * @param ExtensionsDataInterface $extension
     * @return TypeDefinitionInterface the updated type
     */
    public function updateType($repositoryId, TypeDefinitionInterface $type, ExtensionsDataInterface $extension = null);
}
