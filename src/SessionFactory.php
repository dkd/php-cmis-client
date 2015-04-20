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

use Dkd\PhpCmis\Bindings\CmisBindingsHelper;
use Doctrine\Common\Cache\Cache;

/**
 * Class SessionFactory
 *
 * @author Sascha Egerer <sascha.egerer@dkd.de>
 */
class SessionFactory implements SessionFactoryInterface
{
    /**
     * @param array $parameters
     * @param ObjectFactoryInterface|null $objectFactory
     * @param Cache|null $cache
     * @param Cache|null $typeDefinitionCache
     * @return Session
     */
    public function createSession(
        array $parameters,
        ObjectFactoryInterface $objectFactory = null,
        Cache $cache = null,
        Cache $typeDefinitionCache = null
    ) {
        $session = new Session($parameters, $objectFactory, $cache, $typeDefinitionCache);
        return $session;
    }

    /**
     * @param array $parameters
     * @param ObjectFactoryInterface|null $objectFactory
     * @param Cache|null $cache
     * @param Cache|null $typeDefinitionCache
     * @return Data\RepositoryInfoInterface[]
     */
    public function getRepositories(
        array $parameters,
        ObjectFactoryInterface $objectFactory = null,
        Cache $cache = null,
        Cache $typeDefinitionCache = null
    ) {
        $cmisBindingsHelper = new CmisBindingsHelper();
        $binding = $cmisBindingsHelper->createBinding(
            $parameters,
            $typeDefinitionCache
        );

        return $binding->getRepositoryService()->getRepositoryInfos();
    }
}
