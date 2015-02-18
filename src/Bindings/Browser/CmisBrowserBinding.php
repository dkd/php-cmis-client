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

use Dkd\PhpCmis\AclServiceInterface;
use Dkd\PhpCmis\Bindings\BindingSessionInterface;
use Dkd\PhpCmis\Bindings\CmisInterface;
use Dkd\PhpCmis\DiscoveryServiceInterface;
use Dkd\PhpCmis\MultiFilingServiceInterface;
use Dkd\PhpCmis\NavigationServiceInterface;
use Dkd\PhpCmis\ObjectServiceInterface;
use Dkd\PhpCmis\PolicyServiceInterface;
use Dkd\PhpCmis\RelationshipServiceInterface;
use Dkd\PhpCmis\RepositoryServiceInterface;
use Dkd\PhpCmis\VersioningServiceInterface;

/**
 * Base class for all Browser Binding client services.
 */
class CmisBrowserBinding implements CmisInterface
{
    /**
     * @var BindingSessionInterface
     */
    protected $session;

    /**
     * @var RepositoryService
     */
    protected $repositoryService;

    /**
     * @var NavigationService
     */
    protected $navigationService;

    /**
     * @var ObjectService
     */
    protected $objectService;

    /**
     * @var VersioningService
     */
    protected $versioningService;

    /**
     * @var DiscoveryService
     */
    protected $discoveryService;

    /**
     * @var MultiFilingService
     */
    protected $multiFilingService;

    /**
     * @var RelationshipService
     */
    protected $relationshipService;

    /**
     * @var PolicyService
     */
    protected $policyService;

    /**
     * @var AclService
     */
    protected $aclService;

    /**
     * @param BindingSessionInterface $session
     */
    public function __construct(BindingSessionInterface $session)
    {
        $this->session = $session;
        $this->repositoryService = new RepositoryService($session);
        $this->navigationService = new NavigationService($session);
        $this->objectService = new ObjectService($session);
        $this->versioningService = new VersioningService($session);
        $this->discoveryService = new DiscoveryService($session);
        $this->multiFilingService = new MultiFilingService($session);
        $this->relationshipService = new RelationshipService($session);
        $this->policyService = new PolicyService($session);
        $this->aclService = new AclService($session);
    }

    /**
     * Gets a Repository Service interface object.
     *
     * @return RepositoryServiceInterface
     */
    public function getRepositoryService()
    {
        return $this->repositoryService;
    }

    /**
     * Gets a Navigation Service interface object.
     *
     * @return NavigationServiceInterface
     */
    public function getNavigationService()
    {
        return $this->navigationService;
    }

    /**
     * Gets an Object Service interface object.
     *
     * @return ObjectServiceInterface
     */
    public function getObjectService()
    {
        return $this->objectService;
    }

    /**
     * Gets a Versioning Service interface object.
     *
     * @return VersioningServiceInterface
     */
    public function getVersioningService()
    {
        return $this->versioningService;
    }

    /**
     * Gets a Relationship Service interface object.
     *
     * @return RelationshipServiceInterface
     */
    public function getRelationshipService()
    {
        return $this->relationshipService;
    }

    /**
     * Gets a Discovery Service interface object.
     *
     * @return DiscoveryServiceInterface
     */
    public function getDiscoveryService()
    {
        return $this->discoveryService;
    }

    /**
     * Gets a Multifiling Service interface object.
     *
     * @return MultiFilingServiceInterface
     */
    public function getMultiFilingService()
    {
        return $this->multiFilingService;
    }

    /**
     * Gets an ACL Service interface object.
     *
     * @return AclServiceInterface
     */
    public function getAclService()
    {
        return $this->aclService;
    }

    /**
     * Gets a Policy Service interface object.
     *
     * @return PolicyServiceInterface
     */
    public function getPolicyService()
    {
        return $this->policyService;
    }

    /**
     * Clears all caches of the current session.
     */
    public function clearAllCaches()
    {
        // TODO: Implement clearAllCaches() method.
    }

    /**
     * Clears all caches of the current session that are related to the given
     * repository.
     *
     * @param string $repositoryId the repository id
     */
    public function clearRepositoryCache($repositoryId)
    {
        // TODO: Implement clearRepositoryCache() method.
    }

    /**
     * Releases all resources assigned to this instance.
     */
    public function close()
    {
        // TODO: Implement close() method.
    }
}
