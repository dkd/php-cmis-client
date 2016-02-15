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

echo "Add and activate model 'examples/resources/custommodel.xml' as CMIS model\n\n";

$properties = array(
    \Dkd\PhpCmis\PropertyIds::OBJECT_TYPE_ID => 'D:cm:dictionaryModel',
    \Dkd\PhpCmis\PropertyIds::SECONDARY_OBJECT_TYPE_IDS => array(
        'P:cm:titled'
    ),
    \Dkd\PhpCmis\PropertyIds::NAME => 'custommodel.xml',
    'cm:description' => 'Testing model',
    'cm:title' => 'Testing model 2'
);

try {
    $document = $session->createDocument(
        $properties,
        $session->getObjectByPath('/Data Dictionary/Models'),
        \GuzzleHttp\Stream\Stream::factory(fopen(__DIR__ . '/resource/custommodel.xml', 'r'))
    );

    echo "Model has been created in '/Data Dictionary/Models/'. Model Id: " . $document->getId() . "\n";
    $updated = $session->getObject($document)->updateProperties(array(
        'cm:modelActive' => true
    ));
    if ($session->getObject($document)->getPropertyValue('cm:modelActive')) {
        echo "Model '" . $document->getId() . "' was activated.\n";
    } else {
        echo "Model '" . $document->getId() . "' failed to activate!\n";
    }
    echo "To remove the model, delete it manually or run the DeactivateAndDeleteModel.php example.\n";
} catch (\Dkd\PhpCmis\Exception\CmisContentAlreadyExistsException $e) {
    echo "********* ERROR **********\n";
    echo $e->getMessage() . "\n";
    echo "**************************\n";
    exit();
}
