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

use Dkd\Enumeration\Exception\InvalidEnumerationValueException;
use Dkd\PhpCmis\Converter\JsonConverter;
use Dkd\PhpCmis\Enum\BindingType;
use Dkd\PhpCmis\Exception\CmisInvalidArgumentException;
use Dkd\PhpCmis\Exception\CmisRuntimeException;
use Dkd\PhpCmis\SessionParameter;
use Doctrine\Common\Cache\Cache;

/**
 * A collection of methods that are used in multiple places within the
 * bindings implementation.
 */
class CmisBindingsHelper
{
    const SPI_OBJECT = 'dkd.phpcmis.binding.spi.object';
    const TYPE_DEFINITION_CACHE = 'dkd.phpcmis.binding.typeDefinitionCache';

    /**
     * @param array $parameters
     * @param Cache|null $typeDefinitionCache
     * @return CmisBindingInterface
     */
    public function createBinding(
        array $parameters,
        Cache $typeDefinitionCache = null
    ) {
        if (count($parameters) === 0) {
            throw new CmisRuntimeException('Session parameters must be set!');
        }

        if (!isset($parameters[SessionParameter::BINDING_TYPE])) {
            throw new CmisRuntimeException('Required binding type is not configured!');
        }

        try {
            $bindingType = BindingType::cast($parameters[SessionParameter::BINDING_TYPE]);

            $bindingFactory = $this->getCmisBindingFactory();

            switch (true) {
                case $bindingType->equals(BindingType::BROWSER):
                    $binding = $bindingFactory->createCmisBrowserBinding(
                        $parameters,
                        $typeDefinitionCache
                    );
                    break;
                case $bindingType->equals(BindingType::ATOMPUB):
                case $bindingType->equals(BindingType::WEBSERVICES):
                case $bindingType->equals(BindingType::CUSTOM):
                default:
                    $binding = null;
            }

            if (!is_object($binding) || !($binding instanceof CmisBinding)) {
                throw new CmisRuntimeException(
                    sprintf(
                        'The given binding "%s" is not yet implemented.',
                        $parameters[SessionParameter::BINDING_TYPE]
                    )
                );
            }

        } catch (InvalidEnumerationValueException $exception) {
            throw new CmisRuntimeException(
                'Invalid binding type given: ' . $parameters[SessionParameter::BINDING_TYPE]
            );
        }

        return $binding;
    }

    /**
     * Gets the SPI object for the given session. If there is already a SPI
     * object in the session it will be returned. If there is no SPI object it
     * will be created and put into the session.
     *
     * @param BindingSessionInterface $session
     * @return CmisInterface
     */
    public function getSpi(BindingSessionInterface $session)
    {
        $spi = $session->get(self::SPI_OBJECT);

        if ($spi !== null) {
            return $spi;
        }

        $spiClass = $session->get(SessionParameter::BINDING_CLASS);
        if (empty($spiClass) || !class_exists($spiClass)) {
            throw new CmisRuntimeException(
                sprintf('The given binding class "%s" is not valid!', $spiClass)
            );
        }

        if (!is_a($spiClass, '\\Dkd\\PhpCmis\\Bindings\\CmisInterface', true)) {
            throw new CmisRuntimeException(
                sprintf('The given binding class "%s" does not implement required CmisInterface!', $spiClass)
            );
        }

        try {
            $spi = new $spiClass($session);
        } catch (\Exception $exception) {
            throw new CmisRuntimeException(
                sprintf('Could not create object of type "%s"!', $spiClass),
                null,
                $exception
            );
        }

        $session->put(self::SPI_OBJECT, $spi);

        return $spi;
    }

    /**
     * @param BindingSessionInterface $session
     * @return mixed
     * @throws CmisRuntimeException
     */
    public function getHttpInvoker(BindingSessionInterface $session)
    {
        $invoker = $session->get(SessionParameter::HTTP_INVOKER_OBJECT);

        if (is_object($invoker) && is_a($invoker, '\\GuzzleHttp\\ClientInterface')) {
            return $invoker;
        } elseif (is_object($invoker) && !is_a($invoker, '\\GuzzleHttp\\ClientInterface')) {
            throw new CmisInvalidArgumentException(
                sprintf(
                    'Invalid HTTP invoker given. The given instance "%s" does not implement'
                    . ' \\GuzzleHttp\\ClientInterface!',
                    get_class($invoker)
                ),
                1415281262
            );
        }

        $invokerClass = $session->get(SessionParameter::HTTP_INVOKER_CLASS);
        if (!is_a($invokerClass, '\\GuzzleHttp\\ClientInterface', true)) {
            throw new CmisRuntimeException(
                sprintf('The given HTTP Invoker class "%s" is not valid!', $invokerClass)
            );
        }

        try {
            $invoker = new $invokerClass;
        } catch (\Exception $exception) {
            throw new CmisRuntimeException(
                sprintf('Could not create object of type "%s"!', $invokerClass),
                null,
                $exception
            );
        }

        $session->put(SessionParameter::HTTP_INVOKER_OBJECT, $invoker);

        return $invoker;
    }

    /**
     * @param BindingSessionInterface $session
     * @return JsonConverter
     */
    public function getJsonConverter(BindingSessionInterface $session)
    {
        $jsonConverter = $session->get(SessionParameter::JSON_CONVERTER);

        if ($jsonConverter !== null) {
            return $jsonConverter;
        }

        $jsonConverterClass = $session->get(SessionParameter::JSON_CONVERTER_CLASS);
        if (empty($jsonConverterClass) || !class_exists($jsonConverterClass)) {
            throw new CmisRuntimeException(
                sprintf('The given JSON Converter class "%s" is not valid!', $jsonConverterClass)
            );
        }

        try {
            $jsonConverter = new $jsonConverterClass();
        } catch (\Exception $exception) {
            throw new CmisRuntimeException(
                sprintf('Could not create object of type "%s"!', $jsonConverterClass),
                null,
                $exception
            );
        }

        // we have a json converter object -> put it into the session
        $session->put(SessionParameter::JSON_CONVERTER, $jsonConverter);

        return $jsonConverter;
    }

    /**
     * @return CmisBindingFactory
     */
    protected function getCmisBindingFactory()
    {
        return new CmisBindingFactory();
    }

    /**
     * Returns the type definition cache from the session.
     *
     * @param BindingSessionInterface $session
     * @return Cache
     * @throws CmisRuntimeException Exception is thrown if cache instance could not be initialized.
     */
    public function getTypeDefinitionCache(BindingSessionInterface $session)
    {
        $cache = $session->get(self::TYPE_DEFINITION_CACHE);
        if ($cache !== null) {
            return $cache;
        }

        $className = $session->get(SessionParameter::TYPE_DEFINITION_CACHE_CLASS);
        try {
            $cache = new $className();
        } catch (\Exception $exception) {
            throw new CmisRuntimeException(
                sprintf('Could not create object of type "%s"!', $className),
                null,
                $exception
            );
        }
        $session->put(self::TYPE_DEFINITION_CACHE, $cache);

        return $cache;
    }
}
