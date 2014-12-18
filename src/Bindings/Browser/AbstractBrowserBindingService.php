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
use Dkd\PhpCmis\DataObjects\RepositoryInfo;
use Dkd\PhpCmis\DataObjects\RepositoryInfoBrowserBinding;
use Dkd\PhpCmis\Exception\CmisBaseException;
use Dkd\PhpCmis\Exception\CmisConnectionException;
use Dkd\PhpCmis\Exception\CmisConstraintException;
use Dkd\PhpCmis\Exception\CmisContentAlreadyExistsException;
use Dkd\PhpCmis\Exception\CmisFilterNotValidException;
use Dkd\PhpCmis\Exception\CmisInvalidArgumentException;
use Dkd\PhpCmis\Exception\CmisNameConstraintViolationException;
use Dkd\PhpCmis\Exception\CmisNotSupportedException;
use Dkd\PhpCmis\Exception\CmisObjectNotFoundException;
use Dkd\PhpCmis\Exception\CmisPermissionDeniedException;
use Dkd\PhpCmis\Exception\CmisProxyAuthenticationException;
use Dkd\PhpCmis\Exception\CmisRuntimeException;
use Dkd\PhpCmis\Exception\CmisStorageException;
use Dkd\PhpCmis\Exception\CmisStreamNotSupportedException;
use Dkd\PhpCmis\Exception\CmisUnauthorizedException;
use Dkd\PhpCmis\Exception\CmisUpdateConflictException;
use Dkd\PhpCmis\Exception\CmisVersioningException;
use Dkd\PhpCmis\SessionParameter;
use Dkd\PhpCmis\TypeDefinitionInterface;
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
     * @param CmisBindingsHelper $cmisBindingsHelper
     */
    protected function setCmisBindingsHelper($cmisBindingsHelper = null)
    {
        $this->cmisBindingsHelper = ($cmisBindingsHelper === null) ? new CmisBindingsHelper() : $cmisBindingsHelper;
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
     * @return BindingSessionInterface
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * @return \GuzzleHttp\Client
     */
    protected function getHttpInvoker()
    {
        /** @var \GuzzleHttp\Client $invoker */
        $invoker = $this->cmisBindingsHelper->getHttpInvoker($this->session);

        return $invoker;
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

    /**
     * @return boolean
     */
    protected function getSuccinct()
    {
        return $this->succinct;
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
     * @param Url $url
     * @return \GuzzleHttp\Message\Response
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
                $message,
                $exception
            );
        }

        return $response;
    }

    /**
     * Performs a POST on an URL, checks the response code and returns the
     * result.
     *
     * @param Url $url
     * @param string $contentType
     * @param mixed $content
     * @throws CmisBaseException
     * @return \GuzzleHttp\Message\Response
     */
    protected function post(Url $url, $contentType, $content)
    {
        $headers = array('Content-Type' => $contentType, 'body' => $content);

        try {
            /** @var \GuzzleHttp\Message\Response $response */
            $response = $this->getHttpInvoker()->post((string) $url, $headers);
        } catch (RequestException $exception) {
            throw $this->convertStatusCode(
                $exception->getResponse()->getStatusCode(),
                $exception->getResponse()->getBody(),
                $exception
            );
        }

        return $response;
    }

// ---- URL ----

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
                $repositoryInfo = $this->cmisBindingsHelper->getJsonConverter(
                    $this->getSession()
                )->convertRepositoryInfo($item);

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
     * Retrieves a type definition.
     *
     * @param string $repositoryId
     * @param string $typeId
     * @return TypeDefinitionInterface
     */
    protected function getTypeDefinitionInternal($repositoryId, $typeId)
    {
        // build URL
        $url = $this->getRepositoryUrl($repositoryId, Constants::SELECTOR_TYPE_DEFINITION);
        $url->getQuery()->modify(array(Constants::PARAM_TYPE_ID => $typeId));

        // read and parse
        $response = $this->read($url);

        return $this->cmisBindingsHelper->getJsonConverter(
            $this->getSession()
        )->convertTypeDefinition($response->json());
    }
}
