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

use Dkd\PhpCmis\Bindings\Authentication\AuthenticationProviderInterface;
use Dkd\PhpCmis\DataObjects\Repository;

/**
 * Entry point into the php cmis lib Client API.
 */
interface SessionFactoryInterface
{
    /**
     * Creates a new session.
     *
     * @param string[] $parameters a array of name/value pairs with parameters for the session, see
     *            {@link SessionParameter} for parameters supported by php cmis lib
     * @param ObjectFactoryInterface $objectFactory
     * @param AuthenticationProviderInterface $authenticationProvider
     * @param CacheInterface $cache
     * @param \Doctrine\Common\Cache\Cache $typeDefinitionCache
     * @return SessionInterface a {@link SessionInterface} connected to the CMIS repository
     *
     * @see SessionParameter
     */
    public function createSession(
        array $parameters,
        ObjectFactoryInterface $objectFactory = null,
        AuthenticationProviderInterface $authenticationProvider = null,
        CacheInterface $cache = null,
        \Doctrine\Common\Cache\Cache $typeDefinitionCache = null
    );

    /**
     * Returns all repositories that are available at the endpoint.
     *
     * @param array $parameters a array of name/value pairs with parameters for the session, see
     *            {@link SessionParameter} for parameters supported by php cmis lib, the parameter
     *            {@link SessionParameter::REPOSITORY_ID} should not be set
     *
     * @param ObjectFactoryInterface $objectFactory
     * @param AuthenticationProviderInterface $authenticationProvider
     * @param CacheInterface $cache
     * @param \Doctrine\Common\Cache\Cache $typeDefinitionCache
     * @return Repository[] a list of all available repositories
     *
     * @see org.apache.chemistry.opencmis.commons.SessionParameter
     */
    public function getRepositories(
        array $parameters,
        ObjectFactoryInterface $objectFactory = null,
        AuthenticationProviderInterface $authenticationProvider = null,
        CacheInterface $cache = null,
        \Doctrine\Common\Cache\Cache $typeDefinitionCache = null
    );
}
