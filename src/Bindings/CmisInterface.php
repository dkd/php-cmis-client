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
use Dkd\PhpCmis\DiscoveryServiceInterface;
use Dkd\PhpCmis\MultiFilingServiceInterface;
use Dkd\PhpCmis\NavigationServiceInterface;
use Dkd\PhpCmis\ObjectServiceInterface;
use Dkd\PhpCmis\PolicyServiceInterface;
use Dkd\PhpCmis\RelationshipServiceInterface;
use Dkd\PhpCmis\RepositoryServiceInterface;
use Dkd\PhpCmis\VersioningServiceInterface;

/**
 * CMIS SPI interface.
 */
interface CmisInterface
{
    /**
     * Gets a Repository Service interface object.
     *
     * @return RepositoryServiceInterface
     */
    public function getRepositoryService();

    /**
     * Gets a Navigation Service interface object.
     *
     * @return NavigationServiceInterface
     */
    public function getNavigationService();

    /**
     * Gets an Object Service interface object.
     *
     * @return ObjectServiceInterface
     */
    public function getObjectService();

    /**
     * Gets a Versioning Service interface object.
     *
     * @return VersioningServiceInterface
     */
    public function getVersioningService();

    /**
     * Gets a Relationship Service interface object.
     *
     * @return RelationshipServiceInterface
     */
    public function getRelationshipService();

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
     * Gets an ACL Service interface object.
     *
     * @return AclServiceInterface
     */
    public function getAclService();

    /**
     * Gets a Policy Service interface object.
     *
     * @return PolicyServiceInterface
     */
    public function getPolicyService();

    /**
     * Clears all caches of the current session.
     */
    public function clearAllCaches();

    /**
     * Clears all caches of the current session that are related to the given
     * repository.
     *
     * @param string $repositoryId the repository id
     */
    public function clearRepositoryCache($repositoryId);

    /**
     * Releases all resources assigned to this instance.
     */
    public function close();
}
