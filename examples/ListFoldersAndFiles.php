<?php
/**
 * This example will list the children of the CMIS root folder.
 * The list is created recursively but is limited to 5 items per level.
 */

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

// Get the root folder of the repository
$rootFolder = $session->getRootFolder();

echo '+ [ROOT FOLDER]: ' . $rootFolder->getName() . "\n";

printFolderContent($rootFolder);

function printFolderContent(\Dkd\PhpCmis\Data\FolderInterface $folder, $levelIndention = '  ')
{
    $i = 0;
    foreach ($folder->getChildren() as $children) {
        echo $levelIndention;
        $i++;
        if ($i > 10) {
            echo "| ...\n";
            break;
        }

        if ($children instanceof \Dkd\PhpCmis\Data\FolderInterface) {
            echo '+ [FOLDER]: ' . $children->getName() . "\n";
            printFolderContent($children, $levelIndention . '  ');
        } elseif ($children instanceof \Dkd\PhpCmis\Data\DocumentInterface) {
            echo '- [DOCUMENT]: ' . $children->getName() . "\n";
        } else {
            echo '- [ITEM]: ' . $children->getName() . "\n";
        }
    }
}
