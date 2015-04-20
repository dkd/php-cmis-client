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
 * @TODO Add description for all session parameters
 *
 * @author Sascha Egerer <sascha.egerer@dkd.de>
 */
class SessionParameter
{
    // ---- general parameter ----
    const USER = 'dkd.phpcmis.user';
    const PASSWORD = 'dkd.phpcmis.password';

    // --- binding parameter ----
    /** Predefined binding types (see {@code BindingType}). */
    const BINDING_TYPE = 'dkd.phpcmis.binding.type';

    /** Class name of the binding class. */
    const BINDING_CLASS = 'dkd.phpcmis.binding.classname';

//    /**
//     * Forces OpenCMIS to use the specified CMIS version and ignore the CMIS
//     * version reported by the repository.
//     */
//    const FORCE_CMIS_VERSION = 'dkd.phpcmis.cmisversion';

//    /** URL of the AtomPub service document. */
//    const ATOMPUB_URL = 'dkd.phpcmis.binding.atompub.url';

//    /** WSDL URLs for Web Services. */
//    const WEBSERVICES_REPOSITORY_SERVICE = 'dkd.phpcmis.binding.webservices.RepositoryService';
//    const WEBSERVICES_NAVIGATION_SERVICE = 'dkd.phpcmis.binding.webservices.NavigationService';
//    const WEBSERVICES_OBJECT_SERVICE = 'dkd.phpcmis.binding.webservices.ObjectService';
//    const WEBSERVICES_VERSIONING_SERVICE = 'dkd.phpcmis.binding.webservices.VersioningService';
//    const WEBSERVICES_DISCOVERY_SERVICE = 'dkd.phpcmis.binding.webservices.DiscoveryService';
//    const WEBSERVICES_RELATIONSHIP_SERVICE = 'dkd.phpcmis.binding.webservices.RelationshipService';
//    const WEBSERVICES_MULTIFILING_SERVICE = 'dkd.phpcmis.binding.webservices.MultiFilingService';
//    const WEBSERVICES_POLICY_SERVICE = 'dkd.phpcmis.binding.webservices.PolicyService';
//    const WEBSERVICES_ACL_SERVICE = 'dkd.phpcmis.binding.webservices.ACLService';

//    /** Endpoint URLs for Web Services. */
//    const WEBSERVICES_REPOSITORY_SERVICE_ENDPOINT = 'dkd.phpcmis.binding.webservices.RepositoryService.endpoint';
//    const WEBSERVICES_NAVIGATION_SERVICE_ENDPOINT = 'dkd.phpcmis.binding.webservices.NavigationService.endpoint';
//    const WEBSERVICES_OBJECT_SERVICE_ENDPOINT = 'dkd.phpcmis.binding.webservices.ObjectService.endpoint';
//    const WEBSERVICES_VERSIONING_SERVICE_ENDPOINT = 'dkd.phpcmis.binding.webservices.VersioningService.endpoint';
//    const WEBSERVICES_DISCOVERY_SERVICE_ENDPOINT = 'dkd.phpcmis.binding.webservices.DiscoveryService.endpoint';
//    const WEBSERVICES_RELATIONSHIP_SERVICE_ENDPOINT = 'dkd.phpcmis.binding.webservices.RelationshipService.endpoint';
//    const WEBSERVICES_MULTIFILING_SERVICE_ENDPOINT = 'dkd.phpcmis.binding.webservices.MultiFilingService.endpoint';
//    const WEBSERVICES_POLICY_SERVICE_ENDPOINT = 'dkd.phpcmis.binding.webservices.PolicyService.endpoint';
//    const WEBSERVICES_ACL_SERVICE_ENDPOINT = 'dkd.phpcmis.binding.webservices.ACLService.endpoint';

//    const WEBSERVICES_MEMORY_THRESHOLD = 'dkd.phpcmis.binding.webservices.memoryThreshold';

//    const WEBSERVICES_PORT_PROVIDER_CLASS = 'dkd.phpcmis.binding.webservices.portprovider.classname';

    /** URL of the Browser Binding entry point. */
    const BROWSER_URL = 'dkd.phpcmis.binding.browser.url';
    const BROWSER_SUCCINCT = 'dkd.phpcmis.binding.browser.succinct';
    const BROWSER_DATETIME_FORMAT = 'dkd.phpcmis.binding.browser.datetimeformat';

    const JSON_CONVERTER = 'dkd.phpcmis.converter.jsonconverter';
    const JSON_CONVERTER_CLASS = 'dkd.phpcmis.converter.jsonconverter.classname';

//    /** Factory class name for the local binding. */
//    const LOCAL_FACTORY = 'dkd.phpcmis.binding.local.classname';

    // --- connection ---

    const HTTP_INVOKER_CLASS = 'dkd.phpcmis.binding.httpinvoker.classname';
    const HTTP_INVOKER_OBJECT = 'dkd.phpcmis.binding.httpinvoker.object';


//    const COMPRESSION = 'dkd.phpcmis.binding.compression';
//    const CLIENT_COMPRESSION = 'dkd.phpcmis.binding.clientcompression';

//    const COOKIES = 'dkd.phpcmis.binding.cookies';

//    const HEADER = 'dkd.phpcmis.binding.header';

//    const CONNECT_TIMEOUT = 'dkd.phpcmis.binding.connecttimeout';
//    const READ_TIMEOUT = 'dkd.phpcmis.binding.readtimeout';

//    const PROXY_USER = 'dkd.phpcmis.binding.proxyuser';
//    const PROXY_PASSWORD = 'dkd.phpcmis.binding.proxypassword';

    // --- cache ---

//    const CACHE_SIZE_OBJECTS = 'dkd.phpcmis.cache.objects.size';
//    const CACHE_TTL_OBJECTS = 'dkd.phpcmis.cache.objects.ttl';
//    const CACHE_SIZE_PATHTOID = 'dkd.phpcmis.cache.pathtoid.size';
//    const CACHE_TTL_PATHTOID = 'dkd.phpcmis.cache.pathtoid.ttl';
//    const CACHE_PATH_OMIT = 'dkd.phpcmis.cache.path.omit';

    const CACHE_SIZE_REPOSITORIES = 'dkd.phpcmis.binding.cache.repositories.size';
    const CACHE_SIZE_TYPES = 'dkd.phpcmis.binding.cache.types.size';
    const CACHE_SIZE_LINKS = 'dkd.phpcmis.binding.cache.links.size';

    // --- session control ---

//    const LOCALE_ISO639_LANGUAGE = 'dkd.phpcmis.locale.iso639';
//    const LOCALE_ISO3166_COUNTRY = 'dkd.phpcmis.locale.iso3166';
//    const LOCALE_VARIANT = 'dkd.phpcmis.locale.variant';

    const OBJECT_FACTORY_CLASS = 'dkd.phpcmis.objectfactory.classname';
    const CACHE_CLASS = 'dkd.phpcmis.cache.classname';
    const TYPE_DEFINITION_CACHE_CLASS = 'dkd.phpcmis.cache.types.classname';

    const REPOSITORY_ID = 'dkd.phpcmis.session.repository.id';
    const REPOSITORY_URL_CACHE = 'dkd.phpcmis.binding.browser.repositoryurls';

    // --- workarounds ---

//    const INCLUDE_OBJECTID_URL_PARAM_ON_CHECKOUT = 'dkd.phpcmis.workaround.includeObjectIdOnCheckout';
    const OMIT_CHANGE_TOKENS = 'dkd.phpcmis.workaround.omitChangeTokens';
}
