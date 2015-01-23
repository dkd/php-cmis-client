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

use Dkd\PhpCmis\Bindings\BindingSessionInterface;
use Dkd\PhpCmis\Bindings\CmisBindingsHelper;
use Dkd\PhpCmis\Constants;
use Dkd\PhpCmis\Data\PropertiesInterface;
use Dkd\PhpCmis\Data\AclInterface;
use Dkd\PhpCmis\DataObjects\RepositoryInfo;
use Dkd\PhpCmis\DataObjects\RepositoryInfoBrowserBinding;
use Dkd\PhpCmis\Definitions\TypeDefinitionInterface;
use Dkd\PhpCmis\Exception\CmisBaseException;
use Dkd\PhpCmis\Exception\CmisConnectionException;
use Dkd\PhpCmis\Exception\CmisConstraintException;
use Dkd\PhpCmis\Exception\CmisInvalidArgumentException;
use Dkd\PhpCmis\Exception\CmisNotSupportedException;
use Dkd\PhpCmis\Exception\CmisObjectNotFoundException;
use Dkd\PhpCmis\Exception\CmisPermissionDeniedException;
use Dkd\PhpCmis\Exception\CmisProxyAuthenticationException;
use Dkd\PhpCmis\Exception\CmisRuntimeException;
use Dkd\PhpCmis\Exception\CmisUnauthorizedException;
use Dkd\PhpCmis\SessionParameter;
use GuzzleHttp\Exception\RequestException;
use League\Url\Url;

/**
 * Base class for all Browser Binding client services.
 */
abstract class AbstractBrowserBindingService
{
    /**
     * @var BindingSessionInterface
     */
    protected $session;

    /**
     * @var boolean
     */
    protected $succinct;

    /**
     * @var CmisBindingsHelper
     */
    protected $cmisBindingsHelper;

    /**
     * @param BindingSessionInterface $session
     * @param CmisBindingsHelper $cmisBindingsHelper
     */
    public function __construct(BindingSessionInterface $session, $cmisBindingsHelper = null)
    {
        $this->setCmisBindingsHelper($cmisBindingsHelper);
        $this->setSession($session);
    }

    /**
     * Set cmis binding helper property
     *
     * @param CmisBindingsHelper $cmisBindingsHelper The cmis binding helper that should be defined.
     * If <code>null</code> is given a new instance of CmisBindingsHelper will be created.
     */
    protected function setCmisBindingsHelper($cmisBindingsHelper = null)
    {
        $this->cmisBindingsHelper = ($cmisBindingsHelper === null) ? new CmisBindingsHelper() : $cmisBindingsHelper;
    }

    /**
     * Get the url for an object
     *
     * @param string $repositoryId
     * @param string $objectId
     * @param string $selector
     * @throws CmisConnectionException
     * @throws CmisObjectNotFoundException
     * @return Url
     */
    protected function getObjectUrl($repositoryId, $objectId, $selector = null)
    {
        $result = $this->getRepositoryUrlCache()->getObjectUrl($repositoryId, $objectId, $selector);

        if ($result === null) {
            $this->getRepositoriesInternal($repositoryId);
            $result = $this->getRepositoryUrlCache()->getObjectUrl($repositoryId, $objectId, $selector);
        }

        if ($result === null) {
            throw new CmisObjectNotFoundException("Unknown repository!");
        }

        return $result;
    }

    /**
     * Returns the repository URL cache or creates a new cache if it doesn't
     * exist.
     *
     * @return RepositoryUrlCache
     */
    protected function getRepositoryUrlCache()
    {
        $repositoryUrlCache = $this->getSession()->get(SessionParameter::REPOSITORY_URL_CACHE);
        if ($repositoryUrlCache === null) {
            $repositoryUrlCache = new RepositoryUrlCache();
            $this->getSession()->put(SessionParameter::REPOSITORY_URL_CACHE, $repositoryUrlCache);
        }

        return $repositoryUrlCache;
    }

    /**
     * Get current session
     *
     * @return BindingSessionInterface
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Sets the current session.
     *
     * @param BindingSessionInterface $session
     */
    protected function setSession(BindingSessionInterface $session)
    {
        $this->session = $session;
        $succinct = $session->get(SessionParameter::BROWSER_SUCCINCT);
        $this->succinct = ($succinct === null ? true : (boolean) $succinct);
    }

    /**
     * Retrieves the the repository info objects.
     *
     * @param string $repositoryId
     * @throws CmisConnectionException
     * @return RepositoryInfo[] Returns ALL Repository Infos that are available and not just the one requested by id.
     */
    protected function getRepositoriesInternal($repositoryId = null)
    {
        $repositoryUrlCache = $this->getRepositoryUrlCache();

        if ($repositoryId === null) {
            // no repository id provided -> get all
            $url = $repositoryUrlCache->buildUrl($this->getServiceUrl());
        } else {
            // use URL of the specified repository
            $url = $repositoryUrlCache->getRepositoryUrl($repositoryId, Constants::SELECTOR_REPOSITORY_INFO);
            if ($url === null) {
                // repository infos haven't been fetched yet -> get them all
                $url = $repositoryUrlCache->buildUrl($this->getServiceUrl());
            }
        }

        $repositoryInfos = array();
        $result = $this->read($url)->json();
        if (!is_array($result)) {
            throw new CmisConnectionException(
                'Could not fetch repository info! Response is not a valid JSON.',
                1416343166
            );
        }
        foreach ($result as $item) {
            if (is_array($item)) {
                $repositoryInfo = $this->getJsonConverter()->convertRepositoryInfo($item);

                if ($repositoryInfo instanceof RepositoryInfoBrowserBinding) {
                    $id = $repositoryInfo->getId();
                    $repositoryUrl = $repositoryInfo->getRepositoryUrl();
                    $rootUrl = $repositoryInfo->getRootUrl();

                    if (empty($id) || empty($repositoryUrl) || empty($rootUrl)) {
                        throw new CmisConnectionException(
                            sprintf('Found invalid Repository Info! (id: %s)', $id),
                            1415187765
                        );
                    }

                    $this->getRepositoryUrlCache()->addRepository($id, $repositoryUrl, $rootUrl);

                    $repositoryInfos[] = $repositoryInfo;
                }
            } else {
                throw new CmisConnectionException(
                    sprintf(
                        'Found invalid repository info! Value of type "array" was expected'
                        . 'but value of type "%s" was given.',
                        gettype($item)
                    ),
                    1415187764
                );
            }
        }

        return $repositoryInfos;
    }

    /**
     * Returns the service URL of this session.
     *
     * @return string|null
     */
    protected function getServiceUrl()
    {
        $browserUrl = $this->getSession()->get(SessionParameter::BROWSER_URL);
        if (is_string($browserUrl)) {
            return $browserUrl;
        }

        return null;
    }

    /**
     * Do a get request for the given url
     *
     * @param Url $url
     * @return \GuzzleHttp\Message\Response
     * @throws CmisBaseException an more specific exception of this type could be thrown. For more details see
     * @see AbstractBrowserBindingService::convertStatusCode()
     */
    protected function read(Url $url)
    {
        /** @var \GuzzleHttp\Message\Response $response */
        try {
            $response = $this->getHttpInvoker()->get((string) $url);
        } catch (RequestException $exception) {
            $code = 0;
            $message = null;
            if ($exception->getResponse()) {
                $code = $exception->getResponse()->getStatusCode();
                $message = $exception->getResponse()->getBody();
            }
            throw $this->convertStatusCode(
                $code,
                (string) $message,
                $exception
            );
        }

        return $response;
    }

    /**
     * Get a HTTP Invoker instance
     *
     * @return \GuzzleHttp\Client
     */
    protected function getHttpInvoker()
    {
        /** @var \GuzzleHttp\Client $invoker */
        $invoker = $this->cmisBindingsHelper->getHttpInvoker($this->getSession());

        return $invoker;
    }

    /**
     * Converts an error message or a HTTP status code into an Exception.
     *
     * @see http://docs.oasis-open.org/cmis/CMIS/v1.1/os/CMIS-v1.1-os.html#x1-551021r549
     *
     * @param integer $code
     * @param string $message
     * @param \Exception $exception
     * @return CmisBaseException
     */
    protected function convertStatusCode($code, $message, \Exception $exception = null)
    {
        $messageData = json_decode($message, true);

        if (is_array($messageData) && !empty($messageData[JSONConstants::ERROR_EXCEPTION])) {
            $jsonError = $messageData[JSONConstants::ERROR_EXCEPTION];

            if (!empty($messageData[JSONConstants::ERROR_MESSAGE])
                && is_string($messageData[JSONConstants::ERROR_MESSAGE])
            ) {
                $message = $messageData[JSONConstants::ERROR_MESSAGE];
            }

            $exceptionName = '\\Dkd\\PhpCmis\\Exception\\Cmis' . ucfirst($jsonError) . 'Exception';

            if (class_exists($exceptionName)) {
                return new $exceptionName($message, null, $exception);
            }
        }

        // fall back to status code
        switch ($code) {
            case 301:
            case 302:
            case 303:
            case 307:
                return new CmisConnectionException(
                    'Redirects are not supported (HTTP status code ' . $code . '): ' . $message,
                    null,
                    $exception
                );
            case 400:
                return new CmisInvalidArgumentException($message, null, $exception);
            case 401:
                return new CmisUnauthorizedException($message, null, $exception);
            case 403:
                return new CmisPermissionDeniedException($message, null, $exception);
            case 404:
                return new CmisObjectNotFoundException($message, null, $exception);
            case 405:
                return new CmisNotSupportedException($message, null, $exception);
            case 407:
                return new CmisProxyAuthenticationException($message, null, $exception);
            case 409:
                return new CmisConstraintException($message, null, $exception);
            default:
                return new CmisRuntimeException($message, null, $exception);
        }
    }

    // ---- helpers ----

    /**
     * Returns JSON Converter instance
     *
     * @return \Dkd\PhpCmis\Converter\JsonConverter
     */
    protected function getJsonConverter()
    {
        return $this->cmisBindingsHelper->getJsonConverter($this->getSession());
    }

    /**
     * Generate url for a given path of a given repository.
     *
     * @param string $repositoryId
     * @param string $path
     * @param string $selector
     * @throws CmisConnectionException
     * @throws CmisObjectNotFoundException
     * @return Url
     */
    protected function getPathUrl($repositoryId, $path, $selector = null)
    {
        $result = $this->getRepositoryUrlCache()->getPathUrl($repositoryId, $path, $selector);

        if ($result === null) {
            $this->getRepositoriesInternal($repositoryId);
            $result = $this->getRepositoryUrlCache()->getPathUrl($repositoryId, $path, $selector);
        }

        if ($result === null) {
            throw new CmisObjectNotFoundException("Unknown repository!");
        }

        return $result;
    }

// ---- URL ----

    /**
     * Get if succinct mode is used
     *
     * @return boolean
     */
    protected function getSuccinct()
    {
        return $this->succinct;
    }

    /**
     * Performs a POST on an URL, checks the response code and returns the
     * result.
     *
     * @param Url $url
     * @param mixed $content
     * @param array $headers
     * @return \GuzzleHttp\Message\Response
     * @throws CmisBaseException an more specific exception of this type could be thrown. For more details see
     * @see AbstractBrowserBindingService::convertStatusCode()
     */
    protected function post(Url $url, $content, array $headers = array())
    {
        $headers['body'] = $content;

        try {
            /** @var \GuzzleHttp\Message\Response $response */
            $response = $this->getHttpInvoker()->post((string) $url, $headers);
        } catch (RequestException $exception) {
            throw $this->convertStatusCode(
                $exception->getResponse()->getStatusCode(),
                (string) $exception->getResponse()->getBody(),
                $exception
            );
        }

        return $response;
    }

    /**
     * Retrieves a type definition.
     *
     * @param string $repositoryId
     * @param string $typeId
     * @return TypeDefinitionInterface|null
     */
    protected function getTypeDefinitionInternal($repositoryId, $typeId)
    {
        // build URL
        $url = $this->getRepositoryUrl($repositoryId, Constants::SELECTOR_TYPE_DEFINITION);
        $url->getQuery()->modify(array(Constants::PARAM_TYPE_ID => $typeId));

        return $this->getJsonConverter()->convertTypeDefinition($this->read($url)->json());
    }

    /**
     * Get url for a repository
     *
     * @param string $repositoryId
     * @param string $selector
     * @throws CmisConnectionException
     * @throws CmisObjectNotFoundException
     * @return Url
     */
    protected function getRepositoryUrl($repositoryId, $selector = null)
    {
        $result = $this->getRepositoryUrlCache()->getRepositoryUrl($repositoryId, $selector);

        if ($result === null) {
            $this->getRepositoriesInternal($repositoryId);
            $result = $this->getRepositoryUrlCache()->getRepositoryUrl($repositoryId, $selector);
        }

        if ($result === null) {
            throw new CmisObjectNotFoundException("Unknown repository!");
        }

        return $result;
    }

    /**
     * Converts a Properties list into an array that can be used for the CMIS request.
     *
     * @param PropertiesInterface $properties
     * @return array Example <code>
     * array('propertyId' => array(0 => 'myId'), 'propertyValue' => array(0 => 'valueOfMyId'))
     * </code>
     */
    protected function convertPropertiesToQueryArray(PropertiesInterface $properties)
    {
        $propertiesArray = array();

        $propertyCounter = 0;
        $propertiesArray[Constants::CONTROL_PROP_ID] = array();
        $propertiesArray[Constants::CONTROL_PROP_VALUE] = array();
        foreach ($properties->getProperties() as $property) {
            $propertiesArray[Constants::CONTROL_PROP_ID][$propertyCounter] = $property->getId();

            $propertyValues = $property->getValues();

            if (count($propertyValues) === 1) {
                $propertiesArray[Constants::CONTROL_PROP_VALUE][$propertyCounter] =
                    $this->convertPropertyValueToSimpleType(
                        $property->getFirstValue()
                    );
            } elseif (count($propertyValues) > 1) {
                $propertyValueCounter = 0;
                $propertiesArray[Constants::CONTROL_PROP_VALUE][$propertyCounter] = array();
                foreach ($propertyValues as $propertyValue) {
                    $propertiesArray[Constants::CONTROL_PROP_VALUE][$propertyCounter][$propertyValueCounter] =
                        $this->convertPropertyValueToSimpleType(
                            $propertyValue
                        );
                    $propertyValueCounter ++;
                }
            }

            $propertyCounter ++;
        }

        return $propertiesArray;
    }

    /**
     * Converts values to a format that can be used for the CMIS Browser binding request.
     *
     * @param mixed $value
     * @return mixed
     */
    protected function convertPropertyValueToSimpleType($value)
    {
        if ($value instanceof \DateTime) {
            // CMIS expects a timestamp in milliseconds
            $value = $value->getTimestamp() * 1000;
        }

        return $value;
    }

    /**
     * Converts a Access Control list into an array that can be used for the CMIS request
     *
     * @param AclInterface $acl
     * @param string $principalControl one of principal ace constants
     * CONTROL_ADD_ACE_PRINCIPAL or CONTROL_REMOVE_ACE_PRINCIPAL
     * @param string $permissionControl one of permission ace constants
     * CONTROL_REMOVE_ACE_PRINCIPAL or CONTROL_REMOVE_ACE_PERMISSION
     * @return array Example <code>
     * array('addACEPrincipal' => array(0 => 'principalId'),
     *       'addACEPermission' => array(0 => array(0 => 'permissonValue')))
     * </code>
     */
    protected function convertAclToQueryArray(AclInterface $acl, $principalControl, $permissionControl)
    {
        $acesArray = array();
        $principalCounter = 0;

        foreach ($acl->getAces() as $ace) {
            $permissions = $ace->getPermissions();
            if ($ace->getPrincipal() !== null && $ace->getPrincipal()->getId() && !empty($permissions)) {
                $acesArray[$principalControl][$principalCounter] = $ace->getPrincipal()->getId();
                $permissionCounter = 0;
                $acesArray[$permissionControl][$principalCounter] = array();

                foreach ($permissions as $permission) {
                    $acesArray[$permissionControl][$principalCounter][$permissionCounter] = $permission;
                    $permissionCounter ++;
                }

                $principalCounter ++;
            }
        }

        return $acesArray;
    }

    /**
     * Converts a policies array into an array that can be used for the CMIS request
     *
     * @param array $policies
     * @return array
     */
    protected function convertPoliciesToQueryArray(array $policies)
    {
        $policiesArray = array();
        $policyCounter = 0;

        foreach ($policies as $policy) {
            $policiesArray[Constants::CONTROL_POLICY][$policyCounter] = (string) $policy;
            $policyCounter ++;
        }

        return $policiesArray;
    }
}
