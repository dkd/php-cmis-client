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

use Doctrine\Common\Cache\Cache;

/**
 * Class SessionFactory
 *
 * @author Sascha Egerer <sascha.egerer@dkd.de>
 */
class SessionFactory implements SessionFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createSession(
        array $parameters,
        ObjectFactoryInterface $objectFactory = null,
        Cache $cache = null,
        Cache $typeDefinitionCache = null
    ) {
        return new Session($parameters, $objectFactory, $cache, $typeDefinitionCache);
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
