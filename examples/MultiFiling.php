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


$propertiesDoc = array(
    \Dkd\PhpCmis\PropertyIds::OBJECT_TYPE_ID => 'cmis:document',
    \Dkd\PhpCmis\PropertyIds::NAME => 'Demo Object'
);

$propertiesFolder = array(
    \Dkd\PhpCmis\PropertyIds::OBJECT_TYPE_ID => 'cmis:folder',
    \Dkd\PhpCmis\PropertyIds::NAME => 'Demo Folder'
);

$propertiesFolder2 = array(
    \Dkd\PhpCmis\PropertyIds::OBJECT_TYPE_ID => 'cmis:folder',
    \Dkd\PhpCmis\PropertyIds::NAME => 'Demo Folder 2'
);


try {
    echo "Create CMIS Document with file README.md\n";

    $document = $session->createDocument(
        $propertiesDoc,
        $session->createObjectId($session->getRepositoryInfo()->getRootFolderId()),
        \GuzzleHttp\Stream\Stream::factory(fopen(__DIR__ . '/../README.md', 'r'))
    );

    echo "Document has been created. Document Id: " . $document->getId() . "\n";

    echo "Create CMIS Folder\n";

    $folder = $session->createFolder(
        $propertiesFolder,
        $session->createObjectId($session->getRepositoryInfo()->getRootFolderId())
    );

    echo "Folder has been created. Folder Id: " . $folder->getId() . "\n";

    echo "Create CMIS Folder\n";

    $folder2 = $session->createFolder(
        $propertiesFolder2,
        $session->createObjectId($session->getRepositoryInfo()->getRootFolderId())
    );

    echo "Folder2 has been created. Folder Id: " . $folder->getId() . "\n";

    echo "Mutlifill document to folders \n";

    $multiFillingService = $session->getBinding()->getMultiFilingService();

    $multiFillingService->addObjectToFolder($parameters[\Dkd\PhpCmis\SessionParameter::REPOSITORY_ID],$document->getId(),$folder->getId());

    $multiFillingService->addObjectToFolder($parameters[\Dkd\PhpCmis\SessionParameter::REPOSITORY_ID],$document->getId(),$folder2->getId());

    echo "Mutlifill done.\n";

    $multiFillingService->removeObjectFromFolder($parameters[\Dkd\PhpCmis\SessionParameter::REPOSITORY_ID],$document->getId(),$folder->getId());

    echo "Removed from multifilled folder.\n";

} catch (\Dkd\PhpCmis\Exception\CmisContentAlreadyExistsException $e) {
    echo "********* ERROR **********\n";
    echo $e->getMessage() . "\n";
    echo "**************************\n";
    exit();
}
