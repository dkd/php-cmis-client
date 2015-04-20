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
use Dkd\PhpCmis\Data\RepositoryInfoInterface;
use Dkd\PhpCmis\Definitions\TypeDefinitionContainerInterface;
use Dkd\PhpCmis\Definitions\TypeDefinitionInterface;
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
     * @param string $repositoryId The identifier for the repository.
     * @param TypeDefinitionInterface $type A fully populated type definition including all new property definitions.
     * @param ExtensionDataInterface|null $extension
     * @return TypeDefinitionInterface
     */
    public function createType($repositoryId, TypeDefinitionInterface $type, ExtensionDataInterface $extension = null);

    /**
     * Deletes a type.
     *
     * @param string $repositoryId The identifier for the repository.
     * @param string $typeId The typeId of an object-type specified in the repository.
     * @param ExtensionDataInterface|null $extension
     */
    public function deleteType($repositoryId, $typeId, ExtensionDataInterface $extension = null);

    /**
     * Returns information about the CMIS repository, the optional capabilities it
     * supports and its access control information if applicable.
     *
     * @param string $repositoryId The identifier for the repository.
     * @param ExtensionDataInterface|null $extension
     * @return RepositoryInfoInterface
     */
    public function getRepositoryInfo($repositoryId, ExtensionDataInterface $extension = null);

    /**
     * Returns a list of CMIS repository information available from this CMIS service endpoint.
     * In contrast to the CMIS specification this method returns repository infos not only repository ids.
     *
     * @param ExtensionDataInterface|null $extension
     * @return RepositoryInfoInterface[]
     */
    public function getRepositoryInfos(ExtensionDataInterface $extension = null);

    /**
     * Returns the list of object types defined for the repository that are children of the specified type.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string|null $typeId the typeId of an object type specified in the repository
     *      (if not specified the repository MUST return all base object types)
     * @param boolean $includePropertyDefinitions if <code>true</code> the repository MUST return the property
     *      definitions for each object type returned (default is <code>false</code>)
     * @param integer|null $maxItems the maximum number of items to return in a response
     *      (default is repository specific)
     * @param integer $skipCount number of potential results that the repository MUST skip/page over before
     *      returning any results (default is 0)
     * @param ExtensionDataInterface|null $extension
     * @return TypeDefinitionListInterface
     */
    public function getTypeChildren(
        $repositoryId,
        $typeId = null,
        $includePropertyDefinitions = false,
        $maxItems = null,
        $skipCount = 0,
        ExtensionDataInterface $extension = null
    );

    /**
     * Gets the definition of the specified object type.
     *
     * @param string $repositoryId the identifier for the repository
     * @param string $typeId he type definition
     * @param ExtensionDataInterface|null $extension
     * @return TypeDefinitionInterface the newly created type
     */
    public function getTypeDefinition($repositoryId, $typeId, ExtensionDataInterface $extension = null);

    /**
     * Returns the set of descendant object type defined for the repository under the specified type.
     *
     * @param string $repositoryId repositoryId - the identifier for the repository
     * @param string|null $typeId the typeId of an object type specified in the repository
     * (if not specified the repository MUST return all types and MUST ignore the value of the depth parameter)
     * @param integer|null $depth the number of levels of depth in the type hierarchy from which
     * to return results (default is repository specific)
     * @param boolean $includePropertyDefinitions if <code>true</code> the repository MUST return the property
     * definitions for each object type returned (default is <code>false</code>)
     * @param ExtensionDataInterface|null $extension
     * @return TypeDefinitionContainerInterface[]
     */
    public function getTypeDescendants(
        $repositoryId,
        $typeId = null,
        $depth = null,
        $includePropertyDefinitions = false,
        ExtensionDataInterface $extension = null
    );

    /**
     * Updates a type.
     *
     * @param string $repositoryId the identifier for the repository
     * @param TypeDefinitionInterface $type the type definition
     * @param ExtensionDataInterface|null $extension
     * @return TypeDefinitionInterface the updated type
     */
    public function updateType($repositoryId, TypeDefinitionInterface $type, ExtensionDataInterface $extension = null);
}
