<?php
namespace Dkd\PhpCmis\Bindings\Browser;

/*
 * This file is part of php-cmis-client.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Bindings\BindingSessionInterface;
use Dkd\PhpCmis\Bindings\CmisBindingsHelper;
use Dkd\PhpCmis\Bindings\LinkAccessInterface;
use Dkd\PhpCmis\Constants;
use Dkd\PhpCmis\Data\AclInterface;
use Dkd\PhpCmis\Data\PropertiesInterface;
use Dkd\PhpCmis\DataObjects\RepositoryInfo;
use Dkd\PhpCmis\DataObjects\RepositoryInfoBrowserBinding;
use Dkd\PhpCmis\Definitions\TypeDefinitionInterface;
use Dkd\PhpCmis\Enum\DateTimeFormat;
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
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Stream\StreamInterface;
use League\Url\Url;
use Psr\Http\Message\ResponseInterface;
use function basename;
use function is_array;
use function is_object;
use function is_resource;
use function json_decode;

/**
 * Base class for all Browser Binding client services.
 */
abstract class AbstractBrowserBindingService implements LinkAccessInterface
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
     * @var DateTimeFormat
     */
    protected $dateTimeFormat;

    /**
     * @param BindingSessionInterface $session
     * @param CmisBindingsHelper|null $cmisBindingsHelper
     */
    public function __construct(BindingSessionInterface $session, $cmisBindingsHelper = null)
    {
        $this->setCmisBindingsHelper($cmisBindingsHelper);
        $this->setSession($session);
    }

    /**
     * Set cmis binding helper property
     *
     * @param CmisBindingsHelper|null $cmisBindingsHelper The cmis binding helper that should be defined.
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
     * @param string|null $selector
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
            throw new CmisObjectNotFoundException(
                sprintf(
                    'Unknown Object! Repository: "%s" | Object: "%s" | Selector: "%s"',
                    $repositoryId,
                    $objectId,
                    $selector
                )
            );
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
        $this->succinct = $succinct ?? true;

        $this->dateTimeFormat = DateTimeFormat::cast($session->get(SessionParameter::BROWSER_DATETIME_FORMAT));
    }

    /**
     * Retrieves the the repository info objects.
     *
     * @param string|null $repositoryId
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
            $url = $repositoryUrlCache->getRepositoryUrl($repositoryId, Constants::SELECTOR_REPOSITORY_INFO) ??
                $repositoryUrlCache->buildUrl($this->getServiceUrl());
        }

        $repositoryInfos = [];
        $result = $this->readJson($url);
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
        return $this->getSession()->get(SessionParameter::BROWSER_URL);
    }

    /**
     * Wrapper to read URL response as JSON as is the general use case.
     *
     * @param Url $url
     * @return mixed
     */
    protected function readJson(Url $url)
    {
        return json_decode($this->read($url)->getBody(), true);
    }

    /**
     * Do a get request for the given url
     *
     * @param Url $url
     * @return Response
     * @throws CmisBaseException an more specific exception of this type could be thrown. For more details see
     * @see AbstractBrowserBindingService::convertStatusCode()
     */
    protected function read(Url $url)
    {
        /** @var Response $response */
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
     * @return Client
     */
    protected function getHttpInvoker()
    {
        return $this->cmisBindingsHelper->getHttpInvoker($this->getSession());
    }

    /**
     * Converts an error message or a HTTP status code into an Exception.
     *
     * @see http://docs.oasis-open.org/cmis/CMIS/v1.1/os/CMIS-v1.1-os.html#x1-551021r549
     *
     * @param integer $code
     * @param string $message
     * @param null|\Exception $exception
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

        if (empty($message) && $exception !== null) {
            $message = $exception->getMessage();
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
     * @param string|null $selector
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
            throw new CmisObjectNotFoundException(
                sprintf(
                    'Unknown path! Repository: "%s" | Path: "%s" | Selector: "%s"',
                    $repositoryId,
                    $path,
                    $selector
                )
            );
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
     * Wrapper for calling post() and reading response as JSON, as is the general use case.
     *
     * @param Url $url
     * @param array $content
     * @param array $headers
     * @return mixed
     */
    protected function postJson(Url $url, $content = [], array $headers = [])
    {
        return \json_decode($this->post($url, $content, $headers)->getBody(), true);
    }

    /**
     * Performs a POST on an URL, checks the response code and returns the
     * result.
     *
     * @param Url $url Request url
     * @param resource|string|StreamInterface|array $content Entity body data or an array for POST fields and files
     * @param array $headers Additional header options
     * @return ResponseInterface
     * @throws CmisBaseException an more specific exception of this type could be thrown. For more details see
     * @see AbstractBrowserBindingService::convertStatusCode()
     */
    protected function post(Url $url, $content = [], array $headers = [])
    {
        if (is_resource($content) || is_object($content)) {
            $headers['body'] = $content;
        } elseif (is_array($content)) {
            $headers['multipart'] = $this->convertQueryArrayToMultiPart($content);
        }

        try {
            return $this->getHttpInvoker()->post((string) $url, $headers);
        } catch (RequestException $exception) {
            throw $this->convertStatusCode(
                $exception->getResponse()->getStatusCode(),
                (string) $exception->getResponse()->getBody(),
                $exception
            );
        }
    }

    /**
     * @param array $queryArray
     * @param null $prefix
     * @return array
     */
    protected function convertQueryArrayToMultiPart(array $queryArray, $prefix = null)
    {
        $multipart = [];
        foreach ($queryArray as $name => $value) {
            $prefixedName = $prefix ? $prefix . '[' . $name . ']' : $name;
            if (is_array($value)) {
                $multipart = array_merge($multipart, $this->convertQueryArrayToMultiPart($value, $prefixedName));
            } elseif ($value instanceof StreamInterface) {
                $streamPart = [
                    'name' => '',
                    'contents' => $value,
                    'filename' => basename( $value->getMetadata('uri'))
                ];
                $mimetype = $value->getMetadata('mimetype');
                if ($mimetype) {
                    $streamPart['headers'] = ['Content-type' => $mimetype];
                }
                $multipart[] = $streamPart;
            } else {
                $multipart[] = [
                    'name' => $prefix ? $prefixedName : $name,
                    'contents' => $value
                ];
            }
        }
        return $multipart;
    }

    /**
     * Retrieves a type definition.
     *
     * @param string $repositoryId
     * @param string $typeId
     * @return TypeDefinitionInterface|null
     * @throws CmisInvalidArgumentException if repository id or type id is <code>null</code>
     */
    protected function getTypeDefinitionInternal($repositoryId, $typeId)
    {
        if (empty($repositoryId)) {
            throw new CmisInvalidArgumentException('Repository id must not be empty!');
        }

        if (empty($typeId)) {
            throw new CmisInvalidArgumentException('Type id must not be empty!');
        }

        // build URL
        $url = $this->getRepositoryUrl($repositoryId, Constants::SELECTOR_TYPE_DEFINITION);
        $url->getQuery()->modify([Constants::PARAM_TYPE_ID => $typeId]);

        return $this->getJsonConverter()->convertTypeDefinition(
            (array) $this->readJson($url)
        );
    }

    /**
     * Get url for a repository
     *
     * @param string $repositoryId
     * @param string|null $selector
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
            throw new CmisObjectNotFoundException(
                sprintf(
                    'Unknown repository! Repository: "%s" | Selector: "%s"',
                    $repositoryId,
                    $selector
                )
            );
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
        $propertiesArray = [];

        $propertyCounter = 0;
        $propertiesArray[Constants::CONTROL_PROP_ID] = [];
        $propertiesArray[Constants::CONTROL_PROP_VALUE] = [];
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
                $propertiesArray[Constants::CONTROL_PROP_VALUE][$propertyCounter] = [];
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
        } elseif (is_bool($value)) {
			// Booleans must be represented in string form since request will fail if cast to integer
			$value = $value ? 'true' : 'false';
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
        $acesArray = [];
        $principalCounter = 0;

        foreach ($acl->getAces() as $ace) {
            $permissions = $ace->getPermissions();
            if ($ace->getPrincipal() !== null && $ace->getPrincipal()->getId() && !empty($permissions)) {
                $acesArray[$principalControl][$principalCounter] = $ace->getPrincipal()->getId();
                $permissionCounter = 0;
                $acesArray[$permissionControl][$principalCounter] = [];

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
     * @param string[] $policies A list of policy string representations
     * @return array
     */
    protected function convertPolicyIdArrayToQueryArray(array $policies)
    {
        $policiesArray = [];
        $policyCounter = 0;

        foreach ($policies as $policy) {
            $policiesArray[Constants::CONTROL_POLICY][$policyCounter] = (string) $policy;
            $policyCounter ++;
        }

        return $policiesArray;
    }

    /**
     * Returns the date time format
     *
     * @return DateTimeFormat
     */
    public function getDateTimeFormat()
    {
        return $this->dateTimeFormat;
    }

    /**
     * Sets the date time format
     *
     * @param DateTimeFormat $dateTimeFormat
     */
    public function setDateTimeFormat(DateTimeFormat $dateTimeFormat)
    {
        $this->dateTimeFormat = $dateTimeFormat;
    }

    /**
     * Appends policies parameters to url
     *
     * @param Url $url
     * @param string[] $policies A list of policy IDs that must be applied to the newly created document object
     */
    protected function appendPoliciesToUrl(Url $url, array $policies)
    {
        if (!empty($policies)) {
            $url->getQuery()->modify($this->convertPolicyIdArrayToQueryArray($policies));
        }
    }

    /**
     * Appends addAces parameters to url
     *
     * @param Url $url
     * @param AclInterface|null $addAces A list of ACEs
     */
    protected function appendAddAcesToUrl(Url $url, AclInterface $addAces = null)
    {
        if ($addAces !== null) {
            $url->getQuery()->modify(
                $this->convertAclToQueryArray(
                    $addAces,
                    Constants::CONTROL_ADD_ACE_PRINCIPAL,
                    Constants::CONTROL_ADD_ACE_PERMISSION
                )
            );
        }
    }

    /**
     * Appends removeAces parameters to url
     *
     * @param Url $url
     * @param AclInterface|null $removeAces A list of ACEs
     */
    protected function appendRemoveAcesToUrl(Url $url, AclInterface $removeAces = null)
    {
        if ($removeAces !== null) {
            $url->getQuery()->modify(
                $this->convertAclToQueryArray(
                    $removeAces,
                    Constants::CONTROL_REMOVE_ACE_PRINCIPAL,
                    Constants::CONTROL_REMOVE_ACE_PERMISSION
                )
            );
        }
    }

    /**
     * Gets the content link from the cache if it is there or loads it into the
     * cache if it is not there.
     *
     * @param string $repositoryId
     * @param string $documentId
     * @return string|null
     */
    public function loadContentLink($repositoryId, $documentId)
    {
        $result = $this->getRepositoryUrlCache()->getObjectUrl($repositoryId, $documentId, Constants::SELECTOR_CONTENT);
        return $result === null ? null : (string) $result;
    }

    /**
     * Gets a rendition content link from the cache if it is there or loads it
     * into the cache if it is not there.
     *
     * @param string $repositoryId
     * @param string $documentId
     * @param string $streamId
     * @return string|null
     */
    public function loadRenditionContentLink($repositoryId, $documentId, $streamId)
    {
        $result = $this->getRepositoryUrlCache()->getObjectUrl($repositoryId, $documentId, Constants::SELECTOR_CONTENT);
        if ($result !== null) {
            $result->getQuery()->modify([Constants::PARAM_STREAM_ID => $streamId]);
            $result = (string) $result;
        }
        return $result;
    }
}
