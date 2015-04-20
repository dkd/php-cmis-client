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

use Dkd\PhpCmis\Exception\CmisInvalidArgumentException;
use Dkd\PhpCmis\SessionParameter;
use Doctrine\Common\Cache\Cache;

/**
 * Default factory for a CMIS binding instance.
 */
class CmisBindingFactory
{
    /**
     * Create a browser binding
     *
     * @param array $sessionParameters
     * @param Cache|null $typeDefinitionCache
     * @return CmisBinding
     */
    public function createCmisBrowserBinding(
        array $sessionParameters,
        Cache $typeDefinitionCache = null
    ) {
        $this->validateCmisBrowserBindingParameters($sessionParameters);

        return new CmisBinding(new Session(), $sessionParameters, $typeDefinitionCache);
    }

    protected function validateCmisBrowserBindingParameters(array &$sessionParameters)
    {
        if (!isset($sessionParameters[SessionParameter::BINDING_CLASS])) {
            $sessionParameters[SessionParameter::BINDING_CLASS] =
                '\\Dkd\\PhpCmis\\Bindings\\Browser\\CmisBrowserBinding';
        }
        if (!isset($sessionParameters[SessionParameter::BROWSER_SUCCINCT])) {
            $sessionParameters[SessionParameter::BROWSER_SUCCINCT] = true;
        }
        $this->addDefaultSessionParameters($sessionParameters);
        $this->check($sessionParameters, SessionParameter::BROWSER_URL);
    }

    /**
     * Sets some parameters to a default value if they are not already set
     *
     * @param array $sessionParameters
     */
    protected function addDefaultSessionParameters(array &$sessionParameters)
    {
        if (!isset($sessionParameters[SessionParameter::CACHE_SIZE_REPOSITORIES])) {
            $sessionParameters[SessionParameter::CACHE_SIZE_REPOSITORIES] = 10;
        }
        if (!isset($sessionParameters[SessionParameter::CACHE_SIZE_TYPES])) {
            $sessionParameters[SessionParameter::CACHE_SIZE_TYPES] = 100;
        }
        if (!isset($sessionParameters[SessionParameter::CACHE_SIZE_LINKS])) {
            $sessionParameters[SessionParameter::CACHE_SIZE_LINKS] = 400;
        }
        if (!isset($sessionParameters[SessionParameter::HTTP_INVOKER_CLASS])) {
            $sessionParameters[SessionParameter::HTTP_INVOKER_CLASS] = '\\GuzzleHttp\\Client';
        }
        if (!isset($sessionParameters[SessionParameter::JSON_CONVERTER_CLASS])) {
            $sessionParameters[SessionParameter::JSON_CONVERTER_CLASS] = '\\Dkd\\PhpCmis\\Converter\\JsonConverter';
        }
        if (!isset($sessionParameters[SessionParameter::TYPE_DEFINITION_CACHE_CLASS])) {
            $sessionParameters[SessionParameter::TYPE_DEFINITION_CACHE_CLASS] = '\\Doctrine\\Common\\Cache\\ArrayCache';
        }
    }

    /**
     * Checks if the given parameter is present. If not, throw an
     * <code>IllegalArgumentException</code>.
     *
     * @param array $sessionParameters
     * @param string $parameter
     * @throws CmisInvalidArgumentException
     * @return boolean
     */
    protected function check(array $sessionParameters, $parameter)
    {
        if (empty($sessionParameters[$parameter])) {
            throw new CmisInvalidArgumentException(sprintf('Parameter "%s" is missing!', $parameter));
        }

        return true;
    }
}
