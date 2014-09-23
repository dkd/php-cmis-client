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

/**
 * Session parameter constants.
 *
 * <table border="2" rules="all" cellpadding="4">
 * <tr>
 * <th>Constant</th>
 * <th>Description</th>
 * <th>Binding</th>
 * <th>Value</th>
 * <th>Required</th>
 * <th>Default</th>
 * </tr>
 * <tr>
 * <td colspan="6"><b>General settings</b></td>
 * </tr>
 * <tr>
 * <td>{@link #BINDING_TYPE}</td>
 * <td>Defines the binding to use for the session</td>
 * <td>all</td>
 * <td>"atompub", "webservices", "browser", "local", "custom"</td>
 * <td>yes</td>
 * <td>-</td>
 * </tr>
 * <tr>
 * <td>{@link #BINDING_SPI_CLASS}</td>
 * <td>Binding implementation class</td>
 * <td>all</td>
 * <td>class name</td>
 * <td>Custom binding: yes<br>
 * all other binding: no</td>
 * <td>depends on {@link #BINDING_TYPE}</td>
 * </tr>
 * <tr>
 * <td>{@link #REPOSITORY_ID}</td>
 * <td>Repository ID</td>
 * <td>all</td>
 * <td>repository id</td>
 * <td>SessionFactory.createSession(): yes<br>
 * SessionFactory.getRepositories(): no</td>
 * <td>-</td>
 * </tr>
 * <tr>
 * <td>{@link #LOCALE_ISO639_LANGUAGE}</td>
 * <td>Language code sent to server</td>
 * <td>all</td>
 * <td>ISO 639 code</td>
 * <td>no</td>
 * <td>-</td>
 * </tr>
 * <tr>
 * <td>{@link #LOCALE_ISO3166_COUNTRY}</td>
 * <td>Country code sent to server if language code is set</td>
 * <td>all</td>
 * <td>ISO 3166 code</td>
 * <td>no</td>
 * <td>-</td>
 * </tr>
 * <tr>
 * <tr>
 * <td>{@link #OBJECT_FACTORY_CLASS}</td>
 * <td>Object factory implementation class</td>
 * <td>all</td>
 * <td>class name</td>
 * <td>no</td>
 * <td>dkd.phpcmis.client.runtime.repository.ObjectFactoryImpl
 * </td>
 * </tr>
 * <tr>
 * <td colspan="6"><b>Authentication settings</b></td>
 * </tr>
 * <tr>
 * <td>{@link #USER}</td>
 * <td>User name (used by the standard authentication provider)</td>
 * <td>all</td>
 * <td>user name</td>
 * <td>depends on the server</td>
 * <td>-</td>
 * </tr>
 * <tr>
 * <td>{@link #PASSWORD}</td>
 * <td>Password (used by the standard authentication provider)</td>
 * <td>all</td>
 * <td>password</td>
 * <td>depends on the server</td>
 * <td>-</td>
 * </tr>
 * <tr>
 * <td>{@link #AUTHENTICATION_PROVIDER_CLASS}</td>
 * <td>Authentication Provider class</td>
 * <td>AtomPub, Web Services, Browser</td>
 * <td>class name</td>
 * <td>no</td>
 * <td>dkd.phpcmis.client.bindings.spi.
 * StandardAuthenticationProvider</td>
 * </tr>
 * <tr>
 * <td>{@link #AUTH_HTTP_BASIC}</td>
 * <td>Switch to turn HTTP basic authentication on or off</td>
 * <td>AtomPub, Web Services, Browser</td>
 * <td>"true", "false"</td>
 * <td>no</td>
 * <td>depends on {@link #BINDING_TYPE}</td>
 * </tr>
 * <tr>
 * <td>{@link #AUTH_HTTP_BASIC_CHARSET}</td>
 * <td>Charset to encode HTTP basic authentication username and password</td>
 * <td>AtomPub, Web Services, Browser</td>
 * <td>character set name</td>
 * <td>no</td>
 * <td>UTF-8</td>
 * </tr>
 * <tr>
 * <td>{@link #AUTH_SOAP_USERNAMETOKEN}</td>
 * <td>Switch to turn UsernameTokens on or off</td>
 * <td>Web Services</td>
 * <td>"true", "false"</td>
 * <td>no</td>
 * <td>true</td>
 * </tr>
 * <tr>
 * <td colspan="6"><b>HTTP and network settings</b></td>
 * </tr>
 * <tr>
 * <td>{@link #HTTP_INVOKER_CLASS}</td>
 * <td>HTTP invoker class</td>
 * <td>AtomPub, Web Services, Browser</td>
 * <td>class name</td>
 * <td>no</td>
 * <td>dkd.phpcmis.client.bindings.spi.http.DefaultHttpInvoker
 * </td>
 * </tr>
 * <tr>
 * <td>{@link #COMPRESSION}</td>
 * <td>Switch to turn HTTP response compression on or off</td>
 * <td>AtomPub, Web Services, Browser</td>
 * <td>"true", "false"</td>
 * <td>no</td>
 * <td>false</td>
 * </tr>
 * <tr>
 * <td>{@link #CLIENT_COMPRESSION}</td>
 * <td>Switch to turn HTTP request compression on or off</td>
 * <td>AtomPub, Web Services, Browser</td>
 * <td>"true", "false"</td>
 * <td>no</td>
 * <td>false</td>
 * </tr>
 * <tr>
 * <td>{@link #COOKIES}</td>
 * <td>Switch to turn cookie support on or off</td>
 * <td>AtomPub, Web Services, Browser</td>
 * <td>"true", "false"</td>
 * <td>no</td>
 * <td>false</td>
 * </tr>
 * <tr>
 * <td>{@link #HEADER}</td>
 * <td>HTTP header</td>
 * <td>AtomPub, Web Services, Browser</td>
 * <td>header header</td>
 * <td>no</td>
 * <td>-</td>
 * </tr>
 * <tr>
 * <td>{@link #PROXY_USER}</td>
 * <td>Proxy user (used by the standard authentication provider)</td>
 * <td>AtomPub, Web Services, Browser</td>
 * <td>user name</td>
 * <td>no</td>
 * <td>-</td>
 * </tr>
 * <tr>
 * <td>{@link #PROXY_PASSWORD}</td>
 * <td>Proxy password (used by the standard authentication provider)</td>
 * <td>AtomPub, Web Services, Browser</td>
 * <td>password</td>
 * <td>no</td>
 * <td>-</td>
 * </tr>
 * <tr>
 * <td>{@link #CONNECT_TIMEOUT}</td>
 * <td>HTTP connect timeout</td>
 * <td>AtomPub, Web Services, Browser</td>
 * <td>time in milliseconds</td>
 * <td>no</td>
 * <td>JVM default</td>
 * </tr>
 * <tr>
 * <td>{@link #READ_TIMEOUT}</td>
 * <td>HTTP read timeout</td>
 * <td>AtomPub, Web Services, Browser</td>
 * <td>time in milliseconds</td>
 * <td>no</td>
 * <td>JVM default</td>
 * </tr>
 * <tr>
 * <td colspan="6"><b>Cache settings</b></td>
 * </tr>
 * <tr>
 * <td>{@link #CACHE_CLASS}</td>
 * <td>Cache implementation class</td>
 * <td>all</td>
 * <td>class name</td>
 * <td>no</td>
 * <td>dkd.phpcmis.client.runtime.cache.CacheImpl</td>
 * </tr>
 * <tr>
 * <td>{@link #TYPE_DEFINITION_CACHE_CLASS}</td>
 * <td>Type definition cache implementation class</td>
 * <td>all</td>
 * <td>class name</td>
 * <td>no</td>
 * <td>
 * dkd.phpcmis.client.bindings.impl.TypeDefinitionCacheImpl</td>
 * </tr>
 * <tr>
 * <td>{@link #CACHE_SIZE_OBJECTS}</td>
 * <td>Object cache size</td>
 * <td>all</td>
 * <td>number of object entries</td>
 * <td>no</td>
 * <td>1000</td>
 * </tr>
 * <tr>
 * <td>{@link #CACHE_TTL_OBJECTS}</td>
 * <td>Object cache time-to-live</td>
 * <td>all</td>
 * <td>time in milliseconds</td>
 * <td>no</td>
 * <td>7200000 (2 hours)</td>
 * </tr>
 * <tr>
 * <td>{@link #CACHE_SIZE_PATHTOID}</td>
 * <td>Path-to-id cache size</td>
 * <td>all</td>
 * <td>number of path to object link entries</td>
 * <td>no</td>
 * <td>1000</td>
 * </tr>
 * <tr>
 * <td>{@link #CACHE_PATH_OMIT}</td>
 * <td>Turn off path-to-id cache</td>
 * <td>all</td>
 * <td>"true", "false"</td>
 * <td>no</td>
 * <td>false</td>
 * </tr>
 * <tr>
 * <td>{@link #CACHE_SIZE_REPOSITORIES}</td>
 * <td>Repository info cache size</td>
 * <td>all</td>
 * <td>number of repository info entries</td>
 * <td>no</td>
 * <td>10</td>
 * </tr>
 * <tr>
 * <td>{@link #CACHE_SIZE_TYPES}</td>
 * <td>Type definition cache size</td>
 * <td>all</td>
 * <td>number of type definition entries</td>
 * <td>no</td>
 * <td>100</td>
 * </tr>
 * <tr>
 * <td>{@link #CACHE_SIZE_LINKS}</td>
 * <td>AtomPub link cache size</td>
 * <td>AtomPub</td>
 * <td>number of link entries</td>
 * <td>no</td>
 * <td>400</td>
 * </tr>
 * <tr>
 * <td colspan="6"><b>AtomPub Binding settings</b></td>
 * </tr>
 * <tr>
 * <td>{@link #ATOMPUB_URL}</td>
 * <td>AtomPub service document URL</td>
 * <td>AtomPub</td>
 * <td>URL</td>
 * <td>yes</td>
 * <td>-</td>
 * </tr>
 * <tr>
 * <td colspan="6"><b>Web Services Binding settings</b></td>
 * </tr>
 * <tr>
 * <td>{@link #WEBSERVICES_REPOSITORY_SERVICE}</td>
 * <td>Repository Service WSDL URL</td>
 * <td>Web Services</td>
 * <td>WSDL URL</td>
 * <td>either {@link #WEBSERVICES_REPOSITORY_SERVICE} or
 * {@link #WEBSERVICES_REPOSITORY_SERVICE_ENDPOINT} must be set</td>
 * <td>-</td>
 * </tr>
 * <tr>
 * <td>{@link #WEBSERVICES_REPOSITORY_SERVICE_ENDPOINT}</td>
 * <td>Repository Service endpoint URL</td>
 * <td>Web Services</td>
 * <td>Endpoint URL</td>
 * <td>either {@link #WEBSERVICES_REPOSITORY_SERVICE} or
 * {@link #WEBSERVICES_REPOSITORY_SERVICE_ENDPOINT} must be set</td>
 * <td>-</td>
 * </tr>
 * <tr>
 * <td>{@link #WEBSERVICES_NAVIGATION_SERVICE}</td>
 * <td>Navigation Service WSDL URL</td>
 * <td>Web Services</td>
 * <td>WSDL URL</td>
 * <td>either {@link #WEBSERVICES_NAVIGATION_SERVICE} or
 * {@link #WEBSERVICES_NAVIGATION_SERVICE_ENDPOINT} must be set</td>
 * <td>-</td>
 * </tr>
 * <tr>
 * <td>{@link #WEBSERVICES_NAVIGATION_SERVICE_ENDPOINT}</td>
 * <td>Navigation Service endpoint URL</td>
 * <td>Web Services</td>
 * <td>Endpoint URL</td>
 * <td>either {@link #WEBSERVICES_NAVIGATION_SERVICE} or
 * {@link #WEBSERVICES_NAVIGATION_SERVICE_ENDPOINT} must be set</td>
 * <td>-</td>
 * </tr>
 * <tr>
 * <td>{@link #WEBSERVICES_OBJECT_SERVICE}</td>
 * <td>Object Service WSDL URL</td>
 * <td>Web Services</td>
 * <td>WSDL URL</td>
 * <td>either {@link #WEBSERVICES_OBJECT_SERVICE} or
 * {@link #WEBSERVICES_OBJECT_SERVICE_ENDPOINT} must be set</td>
 * <td>-</td>
 * </tr>
 * <tr>
 * <td>{@link #WEBSERVICES_OBJECT_SERVICE_ENDPOINT}</td>
 * <td>Object Service endpoint URL</td>
 * <td>Web Services</td>
 * <td>Endpoint URL</td>
 * <td>either {@link #WEBSERVICES_OBJECT_SERVICE} or
 * {@link #WEBSERVICES_OBJECT_SERVICE_ENDPOINT} must be set</td>
 * <td>-</td>
 * </tr>
 * <tr>
 * <td>{@link #WEBSERVICES_VERSIONING_SERVICE}</td>
 * <td>Versioning Service WSDL URL</td>
 * <td>Web Services</td>
 * <td>WSDL URL</td>
 * <td>either {@link #WEBSERVICES_VERSIONING_SERVICE} or
 * {@link #WEBSERVICES_VERSIONING_SERVICE_ENDPOINT} must be set</td>
 * <td>-</td>
 * </tr>
 * <tr>
 * <td>{@link #WEBSERVICES_VERSIONING_SERVICE_ENDPOINT}</td>
 * <td>Versioning Service endpoint URL</td>
 * <td>Web Services</td>
 * <td>Endpoint URL</td>
 * <td>either {@link #WEBSERVICES_VERSIONING_SERVICE} or
 * {@link #WEBSERVICES_VERSIONING_SERVICE_ENDPOINT} must be set</td>
 * <td>-</td>
 * </tr>
 * <tr>
 * <td>{@link #WEBSERVICES_DISCOVERY_SERVICE}</td>
 * <td>Discovery Service WSDL URL</td>
 * <td>Web Services</td>
 * <td>WSDL URL</td>
 * <td>either {@link #WEBSERVICES_DISCOVERY_SERVICE} or
 * {@link #WEBSERVICES_DISCOVERY_SERVICE_ENDPOINT} must be set</td>
 * <td>-</td>
 * </tr>
 * <tr>
 * <td>{@link #WEBSERVICES_DISCOVERY_SERVICE_ENDPOINT}</td>
 * <td>Discovery Service endpoint URL</td>
 * <td>Web Services</td>
 * <td>Endpoint URL</td>
 * <td>either {@link #WEBSERVICES_DISCOVERY_SERVICE} or
 * {@link #WEBSERVICES_DISCOVERY_SERVICE_ENDPOINT} must be set</td>
 * <td>-</td>
 * </tr>
 * <tr>
 * <td>{@link #WEBSERVICES_RELATIONSHIP_SERVICE}</td>
 * <td>Relationship Service WSDL URL</td>
 * <td>Web Services</td>
 * <td>WSDL URL</td>
 * <td>either {@link #WEBSERVICES_RELATIONSHIP_SERVICE} or
 * {@link #WEBSERVICES_RELATIONSHIP_SERVICE_ENDPOINT} must be set</td>
 * <td>-</td>
 * </tr>
 * <tr>
 * <td>{@link #WEBSERVICES_RELATIONSHIP_SERVICE_ENDPOINT}</td>
 * <td>Relationship Service endpoint URL</td>
 * <td>Web Services</td>
 * <td>Endpoint URL</td>
 * <td>either {@link #WEBSERVICES_DISCOVERY_SERVICE} or
 * {@link #WEBSERVICES_RELATIONSHIP_SERVICE_ENDPOINT} must be set</td>
 * <td>-</td>
 * </tr>
 * <tr>
 * <td>{@link #WEBSERVICES_MULTIFILING_SERVICE}</td>
 * <td>Multifiling Service WSDL URL</td>
 * <td>Web Services</td>
 * <td>WSDL URL</td>
 * <td>either {@link #WEBSERVICES_MULTIFILING_SERVICE} or
 * {@link #WEBSERVICES_MULTIFILING_SERVICE_ENDPOINT} must be set</td>
 * <td>-</td>
 * </tr>
 * <tr>
 * <td>{@link #WEBSERVICES_MULTIFILING_SERVICE_ENDPOINT}</td>
 * <td>Multifiling Service endpoint URL</td>
 * <td>Web Services</td>
 * <td>Endpoint URL</td>
 * <td>either {@link #WEBSERVICES_MULTIFILING_SERVICE} or
 * {@link #WEBSERVICES_MULTIFILING_SERVICE_ENDPOINT} must be set</td>
 * <td>-</td>
 * </tr>
 * </tr>
 * <tr>
 * <td>{@link #WEBSERVICES_POLICY_SERVICE}</td>
 * <td>Policy Service WSDL URL</td>
 * <td>Web Services</td>
 * <td>WSDL URL</td>
 * <td>either {@link #WEBSERVICES_POLICY_SERVICE} or
 * {@link #WEBSERVICES_POLICY_SERVICE_ENDPOINT} must be set</td>
 * <td>-</td>
 * </tr>
 * <tr>
 * <td>{@link #WEBSERVICES_POLICY_SERVICE_ENDPOINT}</td>
 * <td>Policy Service endpoint URL</td>
 * <td>Web Services</td>
 * <td>Endpoint URL</td>
 * <td>either {@link #WEBSERVICES_POLICY_SERVICE} or
 * {@link #WEBSERVICES_POLICY_SERVICE_ENDPOINT} must be set</td>
 * <td>-</td>
 * </tr>
 * <tr>
 * <td>{@link #WEBSERVICES_ACL_SERVICE}</td>
 * <td>ACL Service WSDL URL</td>
 * <td>Web Services</td>
 * <td>WSDL URL</td>
 * <td>either {@link #WEBSERVICES_ACL_SERVICE} or
 * {@link #WEBSERVICES_ACL_SERVICE_ENDPOINT} must be set</td>
 * <td>-</td>
 * </tr>
 * <tr>
 * <td>{@link #WEBSERVICES_ACL_SERVICE_ENDPOINT}</td>
 * <td>ACL Service endpoint URL</td>
 * <td>Web Services</td>
 * <td>Endpoint URL</td>
 * <td>either {@link #WEBSERVICES_ACL_SERVICE} or
 * {@link #WEBSERVICES_ACL_SERVICE_ENDPOINT} must be set</td>
 * <td>-</td>
 * </tr>
 * <tr>
 * <td>{@link #WEBSERVICES_MEMORY_THRESHOLD}</td>
 * <td>Documents smaller than the threshold are kept in main memory, larger
 * documents are written to a temporary file</td>
 * <td>Web Services</td>
 * <td>size in bytes</td>
 * <td>no</td>
 * <td>4194304 (4MB)</td>
 * </tr>
 * <tr>
 * <td colspan="6"><b>Browser Binding</b></td>
 * </tr>
 * <tr>
 * <td>{@link #BROWSER_URL}</td>
 * <td>Browser binding service document URL</td>
 * <td>Browser</td>
 * <td>URL</td>
 * <td>yes</td>
 * <td>-</td>
 * </tr>
 * <tr>
 * <td>{@link #BROWSER_SUCCINCT}</td>
 * <td>Defines if properties should be sent in the succinct format</td>
 * <td>Browser</td>
 * <td>"true", "false"</td>
 * <td>no</td>
 * <td>"true"</td>
 * </tr>
 * <tr>
 * <td colspan="6"><b>Local Binding</b></td>
 * </tr>
 * <tr>
 * <td>{@link #LOCAL_FACTORY}</td>
 * <td>Class name of the local service factory (if client and server reside in
 * the same JVM)</td>
 * <td>Local</td>
 * <td>class name</td>
 * <td>yes</td>
 * <td>-</td>
 * </tr>
 * <tr>
 * <td colspan="6"><b>Workarounds</b></td>
 * </tr>
 * <tr>
 * <td>{@link #INCLUDE_OBJECTID_URL_PARAM_ON_CHECKOUT}</td>
 * <td>Defines if the object ID should be added to the check out URL<br>
 * (Workaround for SharePoint 2010)</td>
 * <td>AtomPub</td>
 * <td>"true", "false"</td>
 * <td>no</td>
 * <td>"false"</td>
 * </tr>
 * <tr>
 * <td>{@link #OMIT_CHANGE_TOKENS}</td>
 * <td>Defines if the change token should be omitted for updating calls<br>
 * (Workaround for SharePoint 2010 and SharePoint 2013)</td>
 * <td>all</td>
 * <td>"true", "false"</td>
 * <td>no</td>
 * <td>"false"</td>
 * </tr>
 * </table>
 *
 * @TODO remove parameters that are not required
 *
 * @author Sascha Egerer <sascha.egerer@dkd.de>
 */
class SessionParameter
{

    // ---- general parameter ----
    const USER = "dkd.phpcmis.user";
    const PASSWORD = "dkd.phpcmis.password";

    // --- binding parameter ----
    /** Predefined binding types (see {@code BindingType}). */
    const BINDING_TYPE = "dkd.phpcmis.binding.type";

    /** Class name of the binding class. */
    const BINDING_CLASS = "dkd.phpcmis.binding.classname";
//
//    /**
//     * Forces OpenCMIS to use the specified CMIS version and ignore the CMIS
//     * version reported by the repository.
//     */
//    const FORCE_CMIS_VERSION = "dkd.phpcmis.cmisversion";
//
//    /** URL of the AtomPub service document. */
//    const ATOMPUB_URL = "dkd.phpcmis.binding.atompub.url";
//
//    /** WSDL URLs for Web Services. */
//    const WEBSERVICES_REPOSITORY_SERVICE = "dkd.phpcmis.binding.webservices.RepositoryService";
//    const WEBSERVICES_NAVIGATION_SERVICE = "dkd.phpcmis.binding.webservices.NavigationService";
//    const WEBSERVICES_OBJECT_SERVICE = "dkd.phpcmis.binding.webservices.ObjectService";
//    const WEBSERVICES_VERSIONING_SERVICE = "dkd.phpcmis.binding.webservices.VersioningService";
//    const WEBSERVICES_DISCOVERY_SERVICE = "dkd.phpcmis.binding.webservices.DiscoveryService";
//    const WEBSERVICES_RELATIONSHIP_SERVICE = "dkd.phpcmis.binding.webservices.RelationshipService";
//    const WEBSERVICES_MULTIFILING_SERVICE = "dkd.phpcmis.binding.webservices.MultiFilingService";
//    const WEBSERVICES_POLICY_SERVICE = "dkd.phpcmis.binding.webservices.PolicyService";
//    const WEBSERVICES_ACL_SERVICE = "dkd.phpcmis.binding.webservices.ACLService";
//
//    /** Endpoint URLs for Web Services. */
//    const WEBSERVICES_REPOSITORY_SERVICE_ENDPOINT = "dkd.phpcmis.binding.webservices.RepositoryService.endpoint";
//    const WEBSERVICES_NAVIGATION_SERVICE_ENDPOINT = "dkd.phpcmis.binding.webservices.NavigationService.endpoint";
//    const WEBSERVICES_OBJECT_SERVICE_ENDPOINT = "dkd.phpcmis.binding.webservices.ObjectService.endpoint";
//    const WEBSERVICES_VERSIONING_SERVICE_ENDPOINT = "dkd.phpcmis.binding.webservices.VersioningService.endpoint";
//    const WEBSERVICES_DISCOVERY_SERVICE_ENDPOINT = "dkd.phpcmis.binding.webservices.DiscoveryService.endpoint";
//    const WEBSERVICES_RELATIONSHIP_SERVICE_ENDPOINT = "dkd.phpcmis.binding.webservices.RelationshipService.endpoint";
//    const WEBSERVICES_MULTIFILING_SERVICE_ENDPOINT = "dkd.phpcmis.binding.webservices.MultiFilingService.endpoint";
//    const WEBSERVICES_POLICY_SERVICE_ENDPOINT = "dkd.phpcmis.binding.webservices.PolicyService.endpoint";
//    const WEBSERVICES_ACL_SERVICE_ENDPOINT = "dkd.phpcmis.binding.webservices.ACLService.endpoint";
//
//    const WEBSERVICES_MEMORY_THRESHOLD = "dkd.phpcmis.binding.webservices.memoryThreshold";
//
//    const WEBSERVICES_PORT_PROVIDER_CLASS = "dkd.phpcmis.binding.webservices.portprovider.classname";
//
    /** URL of the Browser Binding entry point. */
    const BROWSER_URL = "dkd.phpcmis.binding.browser.url";
    const BROWSER_SUCCINCT = "dkd.phpcmis.binding.browser.succinct";
    const BROWSER_DATETIME_FORMAT = "dkd.phpcmis.binding.browser.datetimeformat";

    const JSON_CONVERTER = "dkd.phpcmis.converter.jsonconverter";
    const JSON_CONVERTER_CLASS = "dkd.phpcmis.converter.jsonconverter.classname";
//
//    /** Factory class name for the local binding. */
//    const LOCAL_FACTORY = "dkd.phpcmis.binding.local.classname";
//
//    // --- authentication ---
//
//    /** Class name of the authentication provider. */
//    const AUTHENTICATION_PROVIDER_CLASS = "dkd.phpcmis.binding.auth.classname";
//
//    /**
//     * Toggle for HTTP basic authentication. Evaluated by the standard
//     * authentication provider.
//     */
//    const AUTH_HTTP_BASIC = "dkd.phpcmis.binding.auth.http.basic";
//    const AUTH_HTTP_BASIC_CHARSET = "dkd.phpcmis.binding.auth.http.basic.charset";
//
//    /**
//     * Toggle for OAuth Bearer token authentication. Evaluated by the standard
//     * authentication provider.
//     */
//    const AUTH_OAUTH_BEARER = "dkd.phpcmis.binding.auth.http.oauth.bearer";
//
//    /**
//     * Toggle for WS-Security UsernameToken authentication. Evaluated by the
//     * standard authentication provider.
//     */
//    const AUTH_SOAP_USERNAMETOKEN = "dkd.phpcmis.binding.auth.soap.usernametoken";
//
//    // --- OAuth ---
//
//    const OAUTH_CLIENT_ID = "dkd.phpcmis.oauth.clientId";
//    const OAUTH_CLIENT_SECRET = "dkd.phpcmis.oauth.clientSecret";
//    const OAUTH_CODE = "dkd.phpcmis.oauth.code";
//    const OAUTH_TOKEN_ENDPOINT = "dkd.phpcmis.oauth.tokenEndpoint";
//    const OAUTH_REDIRECT_URI = "dkd.phpcmis.oauth.redirectUri";
//
//    const OAUTH_ACCESS_TOKEN = "dkd.phpcmis.oauth.accessToken";
//    const OAUTH_REFRESH_TOKEN = "dkd.phpcmis.oauth.refreshToken";
//    const OAUTH_EXPIRATION_TIMESTAMP = "dkd.phpcmis.oauth.expirationTimestamp";
//    const OAUTH_DEFAULT_TOKEN_LIFETIME = "dkd.phpcmis.oauth.defaultTokenLifetime";
//
//    // --- connection ---
//
    const HTTP_INVOKER_CLASS = "dkd.phpcmis.binding.httpinvoker.classname";
//
//    const COMPRESSION = "dkd.phpcmis.binding.compression";
//    const CLIENT_COMPRESSION = "dkd.phpcmis.binding.clientcompression";
//
//    const COOKIES = "dkd.phpcmis.binding.cookies";
//
//    const HEADER = "dkd.phpcmis.binding.header";
//
//    const CONNECT_TIMEOUT = "dkd.phpcmis.binding.connecttimeout";
//    const READ_TIMEOUT = "dkd.phpcmis.binding.readtimeout";
//
//    const PROXY_USER = "dkd.phpcmis.binding.proxyuser";
//    const PROXY_PASSWORD = "dkd.phpcmis.binding.proxypassword";
//
//    // --- cache ---
//
//    const CACHE_SIZE_OBJECTS = "dkd.phpcmis.cache.objects.size";
//    const CACHE_TTL_OBJECTS = "dkd.phpcmis.cache.objects.ttl";
//    const CACHE_SIZE_PATHTOID = "dkd.phpcmis.cache.pathtoid.size";
//    const CACHE_TTL_PATHTOID = "dkd.phpcmis.cache.pathtoid.ttl";
//    const CACHE_PATH_OMIT = "dkd.phpcmis.cache.path.omit";
//
    const CACHE_SIZE_REPOSITORIES = "dkd.phpcmis.binding.cache.repositories.size";
    const CACHE_SIZE_TYPES = "dkd.phpcmis.binding.cache.types.size";
    const CACHE_SIZE_LINKS = "dkd.phpcmis.binding.cache.links.size";
//
    // --- session control ---
//
//    const LOCALE_ISO639_LANGUAGE = "dkd.phpcmis.locale.iso639";
//    const LOCALE_ISO3166_COUNTRY = "dkd.phpcmis.locale.iso3166";
//    const LOCALE_VARIANT = "dkd.phpcmis.locale.variant";
//
    const OBJECT_FACTORY_CLASS = "dkd.phpcmis.objectfactory.classname";
    const CACHE_CLASS = "dkd.phpcmis.cache.classname";
    const TYPE_DEFINITION_CACHE_CLASS = "dkd.phpcmis.cache.types.classname";
//
    const REPOSITORY_ID = "dkd.phpcmis.session.repository.id";
    const REPOSITORY_URL_CACHE = "dkd.phpcmis.binding.browser.repositoryurls";
//
//    // --- workarounds ---
//
//    const INCLUDE_OBJECTID_URL_PARAM_ON_CHECKOUT = "dkd.phpcmis.workaround.includeObjectIdOnCheckout";
//    const OMIT_CHANGE_TOKENS = "dkd.phpcmis.workaround.omitChangeTokens";
}
