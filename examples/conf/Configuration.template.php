<?php
date_default_timezone_set('Europe/Berlin');       // Set the default timezone
define('CMIS_BROWSER_URL', 'http://my.alfresco.tld:8080/alfresco/api/-default-/public/cmis/versions/1.1/browser');
define('CMIS_BROWSER_USER', 'admin');
define('CMIS_BROWSER_PASSWORD', 'AlfrescoAdmin');
// if empty the first repository will be used
define('CMIS_REPOSITORY_ID', null);
