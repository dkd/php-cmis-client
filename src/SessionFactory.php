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

/**
 * Class SessionFactory
 *
 * @author Sascha Egerer <sascha.egerer@dkd.de>
 */
class SessionFactory implements SessionFactoryInterface
{
    /**
     * Creates a new session.
     *
     * @param string[] $parameters a array of name/value pairs with parameters for the session, see
     * {@link SessionParameter} for parameters supported by php cmis lib
     * @param ObjectFactoryInterface $objectFactory
     * @param AuthenticationProviderInterface $authenticationProvider
     * @param CacheInterface $cache
     * @param TypeDefinitionCacheInterface $typeDefCache
     * @return SessionInterface a {@link SessionInterface} connected to the CMIS repository
     *
     * @see SessionParameter
     */
    public function createSession(
        array $parameters,
        ObjectFactoryInterface $objectFactory,
        AuthenticationProviderInterface $authenticationProvider,
        CacheInterface $cache,
        TypeDefinitionCacheInterface $typeDefCache
    ) {
        return new Session($parameters, $objectFactory, $authenticationProvider, $cache, $typeDefCache);
    }

    /**
     * Returns all repositories that are available at the endpoint.
     *
     * @param $parameters a array of name/value pairs with parameters for the session, see
     * {@link SessionParameter} for parameters supported by php cmis lib, the parameter
     * {@link SessionParameter::REPOSITORY_ID} should not be set
     *
     * @return Repository[] a list of all available repositories
     *
     * @see SessionParameter
     */
    public function getRepositories($parameters)
    {
        // TODO: Implement getRepositories() method.
    }
}
