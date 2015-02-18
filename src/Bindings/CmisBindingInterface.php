<?php
namespace Dkd\PhpCmis\Bindings;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\AclServiceInterface;
use Dkd\PhpCmis\Data\BindingsObjectFactoryInterface;
use Dkd\PhpCmis\DiscoveryServiceInterface;
use Dkd\PhpCmis\Enum\BindingType;
use Dkd\PhpCmis\MultiFilingServiceInterface;
use Dkd\PhpCmis\NavigationServiceInterface;
use Dkd\PhpCmis\ObjectServiceInterface;
use Dkd\PhpCmis\PolicyServiceInterface;
use Dkd\PhpCmis\RelationshipServiceInterface;
use Dkd\PhpCmis\RepositoryServiceInterface;
use Dkd\PhpCmis\VersioningServiceInterface;

/**
 * Entry point for all CMIS binding related operations.
 * It provides access to the service interface objects which are very similar to the CMIS 1.0 domain model.
 *
 * Each instance of this class represents a session.
 * A session comprises of a connection to one CMIS endpoint over one binding for one particular user and a
 * set of caches. All repositories that are exposed by this CMIS endpoint are accessible in this session.
 * All CMIS operations and extension points are provided if they are supported by the underlying binding.
 */
interface CmisBindingInterface
{
    /**
     * Clears all caches of the current CMIS binding session.
     */
    public function clearAllCaches();

    /**
     * Clears all caches of the current CMIS binding session that are related to the given repository.
     *
     * @param string $repositoryId
     */
    public function clearRepositoryCache($repositoryId);

    /**
     * Releases all resources assigned to this binding instance.
     */
    public function close();

    /**
     * Gets an ACL Service interface object.
     *
     * @return AclServiceInterface
     */
    public function getAclService();

    /**
     * Returns the binding type.
     *
     * @return BindingType
     */
    public function getBindingType();

    /**
     * Gets a Discovery Service interface object.
     *
     * @return DiscoveryServiceInterface
     */
    public function getDiscoveryService();

    /**
     * Gets a Multifiling Service interface object.
     *
     * @return MultiFilingServiceInterface
     */
    public function getMultiFilingService();

    /**
     * Gets a Navigation Service interface object.
     *
     * @return NavigationServiceInterface
     */
    public function getNavigationService();

    /**
     * Gets a factory for CMIS binding specific objects.
     * @return BindingsObjectFactoryInterface
     */
    public function getObjectFactory();

    /**
     * Gets an Object Service interface object.
     *
     * @return ObjectServiceInterface
     */
    public function getObjectService();

    /**
     * Gets a Policy Service interface object.
     *
     * @return PolicyServiceInterface
     */
    public function getPolicyService();

    /**
     * Gets a Relationship Service interface object.
     *
     * @return RelationshipServiceInterface
     */
    public function getRelationshipService();

    /**
     * Gets a Repository Service interface object.
     *
     * @return RepositoryServiceInterface
     */
    public function getRepositoryService();

    /**
     * Returns the client session id.
     *
     * @return string
     */
    public function getSessionId();

    /**
     * Gets a Versioning Service interface object.
     *
     * @return VersioningServiceInterface
     */
    public function getVersioningService();
}
