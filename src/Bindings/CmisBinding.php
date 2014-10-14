<?php
namespace Dkd\PhpCmis\Bindings;

use Dkd\PhpCmis\AclServiceInterface;
use Dkd\PhpCmis\Bindings\Authentication\AuthenticationProviderInterface;
use Dkd\PhpCmis\Bindings\Authentication\NullAuthenticationProvider;
use Dkd\PhpCmis\Bindings\Browser\RepositoryService;
use Dkd\PhpCmis\BindingsObjectFactoryInterface;
use Dkd\PhpCmis\VersioningServiceInterface;
use Dkd\PhpCmis\DiscoveryServiceInterface;
use Dkd\PhpCmis\Enum\BindingType;
use Dkd\PhpCmis\Exception\CmisInvalidArgumentException;
use Dkd\PhpCmis\Exception\CmisRuntimeException;
use Dkd\PhpCmis\MultiFilingServiceInterface;
use Dkd\PhpCmis\NavigationServiceInterface;
use Dkd\PhpCmis\ObjectServiceInterface;
use Dkd\PhpCmis\PolicyServiceInterface;
use Dkd\PhpCmis\RelationshipServiceInterface;
use Dkd\PhpCmis\RepositoryServiceInterface;
use Dkd\PhpCmis\SessionParameter;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class CmisBinding implements CmisBindingInterface
{
    /**
     * @var BindingSessionInterface
     */
    protected $session;

    /**
     * @var RepositoryService
     */
    protected $repositoryService;

    public function __construct(
        BindingSessionInterface $session,
        array $sessionParameters,
        AuthenticationProviderInterface $authenticationProvider = null,
        \Doctrine\Common\Cache\Cache $typeDefinitionCache = null
    ) {
        if (count($sessionParameters) === 0) {
            throw new CmisRuntimeException('Session parameters must be set!');
        }

        if (!isset($sessionParameters[SessionParameter::BINDING_CLASS])) {
            throw new CmisInvalidArgumentException('Session parameters do not contain a binding class name!');
        }

        $this->session = $session;

        foreach ($sessionParameters as $key => $value) {
            $this->session->put($key, $value);
        }

        $this->addAuthenticationProviderToSession($authenticationProvider, $sessionParameters);

        $this->repositoryService = new RepositoryService($this->session);
    }

    /**
     * Adds the given authentication provider to the session. If no authentication provider is given
     * a new instance is created based on the class name defined in the session parameters. If both
     * is not defined, the authentication provider will not be set.
     *
     * @param $authenticationProvider
     * @param $sessionParameters
     */
    protected function addAuthenticationProviderToSession($authenticationProvider, $sessionParameters)
    {
        if (
            $authenticationProvider === null
            && !empty($sessionParameters[SessionParameter::AUTHENTICATION_PROVIDER_CLASS])
        ) {
            $authenticationProviderClassName = $sessionParameters[SessionParameter::AUTHENTICATION_PROVIDER_CLASS];
            $authenticationProviderObject = null;

            try {
                $authenticationProviderObject = new $authenticationProviderClassName;
            } catch (\Exception $exception) {
                throw new \InvalidArgumentException(
                    sprintf('Could not load authentication provider: %s', $authenticationProviderClassName),
                    1412787752,
                    $exception
                );
            }

            if (!$authenticationProviderObject instanceof AuthenticationProviderInterface) {
                throw new \InvalidArgumentException(
                    'Authentication provider does not implement AuthenticationProviderInterface!',
                    1412787758
                );
            }

            $authenticationProvider = $authenticationProviderObject;
        }

        if ($authenticationProvider === null) {
            $authenticationProvider = new NullAuthenticationProvider();
        }

        // add authentication provider to session
        $this->session->put(SessionParameter::AUTHENTICATION_PROVIDER_OBJECT, $authenticationProvider);
    }

    /**
     * Clears all caches of the current CMIS binding session.
     *
     * @return void
     */
    public function clearAllCaches()
    {
        throw new \Exception('Not yet implemented!');
        // TODO: Implement clearAllCaches() method.
    }

    /**
     * Clears all caches of the current CMIS binding session that are related to the given repository.
     *
     * @param string $repositoryId
     * @return void
     */
    public function clearRepositoryCache($repositoryId)
    {
        throw new \Exception('Not yet implemented!');
        // TODO: Implement clearRepositoryCache() method.
    }

    /**
     * Releases all resources assigned to this binding instance.
     *
     * @return void
     */
    public function close()
    {
        throw new \Exception('Not yet implemented!');
        // TODO: Implement close() method.
    }

    /**
     * Gets an ACL Service interface object.
     *
     * @return AclServiceInterface
     */
    public function getAclService()
    {
        throw new \Exception('Not yet implemented!');
        // TODO: Implement getAclService() method.
    }

    /**
     * Gets the authentication provider.
     *
     * @return AuthenticationProviderInterface|null
     */
    public function getAuthenticationProvider()
    {
        return $this->session->get(SessionParameter::AUTHENTICATION_PROVIDER_OBJECT);
    }

    /**
     * Returns the binding type.
     *
     * @return BindingType
     */
    public function getBindingType()
    {
        throw new \Exception('Not yet implemented!');
        // TODO: Implement getBindingType() method.
    }

    /**
     * Gets a Discovery Service interface object.
     *
     * @return DiscoveryServiceInterface
     */
    public function getDiscoveryService()
    {
        throw new \Exception('Not yet implemented!');
        // TODO: Implement getDiscoveryService() method.
    }

    /**
     * Gets a Multifiling Service interface object.
     *
     * @return MultiFilingServiceInterface
     */
    public function getMultiFilingService()
    {
        throw new \Exception('Not yet implemented!');
        // TODO: Implement getMultiFilingService() method.
    }

    /**
     * Gets a Navigation Service interface object.
     *
     * @return NavigationServiceInterface
     */
    public function getNavigationService()
    {
        throw new \Exception('Not yet implemented!');
        // TODO: Implement getNavigationService() method.
    }

    /**
     * Gets a factory for CMIS binding specific objects.
     *
     * @return BindingsObjectFactoryInterface
     */
    public function getObjectFactory()
    {
        throw new \Exception('Not yet implemented!');
        // TODO: Implement getObjectFactory() method.
    }

    /**
     * Gets an Object Service interface object.
     *
     * @return ObjectServiceInterface
     */
    public function getObjectService()
    {
        return $this->getCmisBindingsHelper()->getSpi($this->session)->getObjectService();
    }

    /**
     * Gets a Policy Service interface object.
     *
     * @return PolicyServiceInterface
     */
    public function getPolicyService()
    {
        throw new \Exception('Not yet implemented!');
        // TODO: Implement getPolicyService() method.
    }

    /**
     * Gets a Relationship Service interface object.
     *
     * @return RelationshipServiceInterface
     */
    public function getRelationshipService()
    {
        throw new \Exception('Not yet implemented!');
        // TODO: Implement getRelationshipService() method.
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
     * Returns the client session id.
     *
     * @return string
     */
    public function getSessionId()
    {
        throw new \Exception('Not yet implemented!');
        // TODO: Implement getSessionId() method.
    }

    /**
     * Gets a Versioning Service interface object.
     *
     * @return VersioningServiceInterface
     */
    public function getVersioningService()
    {
        throw new \Exception('Not yet implemented!');
        // TODO: Implement getVersioningService() method.
    }

    /**
     * @return CmisBindingsHelper
     */
    public function getCmisBindingsHelper()
    {
        return new CmisBindingsHelper();
    }
}
