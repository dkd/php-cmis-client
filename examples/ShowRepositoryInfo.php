<?php
require_once(__DIR__ . '/../vendor/autoload.php');
if (!is_file(__DIR__ . '/conf/Configuration.php')) {
    die("Please add your connection credentials to the file \"" . __DIR__ . "/conf/Configuration.php\".\n");
} else {
    require_once(__DIR__ . '/conf/Configuration.php');
}

$httpInvoker = new \GuzzleHttp\Client(
    array(
        'defaults' => array(
            'auth' => array(
                CMIS_BROWSER_USER,
                CMIS_BROWSER_PASSWORD
            )
        )
    )
);

$parameters = array(
    \Dkd\PhpCmis\SessionParameter::BINDING_TYPE => \Dkd\PhpCmis\Enum\BindingType::BROWSER,
    \Dkd\PhpCmis\SessionParameter::BROWSER_URL => CMIS_BROWSER_URL,
    \Dkd\PhpCmis\SessionParameter::BROWSER_SUCCINCT => false,
    \Dkd\PhpCmis\SessionParameter::HTTP_INVOKER_OBJECT => $httpInvoker,
);

$sessionFactory = new \Dkd\PhpCmis\SessionFactory();

// If no repository id is defined use the first repository
if (CMIS_REPOSITORY_ID === null) {
    $repositories = $sessionFactory->getRepositories($parameters);
    $parameters[\Dkd\PhpCmis\SessionParameter::REPOSITORY_ID] = $repositories[0]->getId();
} else {
    $parameters[\Dkd\PhpCmis\SessionParameter::REPOSITORY_ID] = CMIS_REPOSITORY_ID;
}

$session = $sessionFactory->createSession($parameters);

$operationContext = new \Dkd\PhpCmis\OperationContext();
$rootFolderID = $session->getRepositoryInfo()->getRootFolderId();

echo "Root folder ID: $rootFolderID\n\n";
echo "Repository Information (Object Properties):\n-------------";
$repositoryData = $session->getObject(
    new \Dkd\PhpCmis\DataObjects\ObjectId($session->getRepositoryInfo()->getRootFolderId()),
    $operationContext
);

foreach ($repositoryData->getProperties() as $property) {
    $value = $property->getDefinition()->getPropertyType()->equals(\Dkd\PhpCmis\Enum\PropertyType::DATETIME) ?
        $property->getFirstValue()->format("Y-m-d H:i:s") : (string) $property->getFirstValue();
    echo "\n" . $property->getDisplayName() . ': ' . $value;
}
echo "\n-----------\n";
