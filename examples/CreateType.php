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

echo "Create CMIS type\n\n";


try {
    $typeMutability = new \Dkd\PhpCmis\DataObjects\TypeMutability();
    $typeMutability->setCanCreate(true);
    $typeMutability->setCanUpdate(true);
    $typeMutability->setCanDelete(true);
    $typeDefinition = $session->getObjectFactory()->createTypeDefinition(
        'typo3:page',
        'page',
        (string) \Dkd\PhpCmis\Enum\BaseTypeId::cast(\Dkd\PhpCmis\Enum\BaseTypeId::CMIS_DOCUMENT),
        (string) \Dkd\PhpCmis\Enum\BaseTypeId::cast(\Dkd\PhpCmis\Enum\BaseTypeId::CMIS_DOCUMENT),
        true,
        true,
        true,
        true,
        true,
        true,
        true,
        '',
        '',
        'TYPO3 Page',
        'TYPO3 Page object',
        $typeMutability
    );

    $session->createType($typeDefinition);

    echo "Type definition has been created. Id: " . $typeDefinition->getId() . "\n";
    echo "Please delete that definition now by hand!\n";
} catch (\Dkd\PhpCmis\Exception\CmisContentAlreadyExistsException $e) {
    echo "********* ERROR **********\n";
    echo $e->getMessage() . "\n";
    echo "**************************\n";
    exit();
}
