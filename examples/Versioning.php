<?php
require_once(__DIR__ . '/../vendor/autoload.php');
if (!is_file(__DIR__ . '/conf/Configuration.php')) {
	die("Please add your connection credentials to the file \"" . __DIR__ . "/conf/Configuration.php\".\n");
} else {
	require_once(__DIR__ . '/conf/Configuration.php');
}

// Extracting arguments for example script
$major = (boolean) isset($argv[1]) ? $argv[1] : false;

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
	$repositoryId = $repositories[0]->getId();
} else {
	$repositoryId = CMIS_REPOSITORY_ID;
}

$parameters[\Dkd\PhpCmis\SessionParameter::REPOSITORY_ID] = $repositoryId;

$session = $sessionFactory->createSession($parameters);
$rootFolder = $session->getObject($session->createObjectId($session->getRootFolder()->getId()));

try {

	$document = null;
	$stream = \GuzzleHttp\Stream\Stream::factory(fopen(__DIR__ . '/../README.md', 'r'));
	foreach ($rootFolder->getChildren() as $child) {
		if ($child->getName() === 'Demo Object') {
			echo "[*] Using existing README.md document in repository root folder.\n";
			$document = $child;
			break;
		}
	}

	if (!$document) {
		echo "[*] Create CMIS Document with file README.md\n";

		$properties = array(
			\Dkd\PhpCmis\PropertyIds::OBJECT_TYPE_ID => 'cmis:document',
			\Dkd\PhpCmis\PropertyIds::NAME => 'Demo Object'
		);

		$document = $session->createDocument($properties, $rootFolder, $stream);
		$document = $session->getObject($document);

		echo "[*] Document has been created. Document Id: " . $document->getId() . "\n";
	}

	$checkedOutDocumentId = $document->getVersionSeriesCheckedOutId();
	if ($checkedOutDocumentId) {
		echo "[*] Document is already checked out - resuming working copy\n";
		$checkedOutDocumentId = $session->createObjectId($checkedOutDocumentId);
	} else {
		echo "[*] Checking out document to private working copy (PWC)... ";
		$checkedOutDocumentId = $document->checkOut();
		echo "done!\n";

	}

	echo "[*] Versioned ID before: " . $document->getId() . "\n";
	echo "[*] Versioned ID during checkout: ". $checkedOutDocumentId . "\n";

	echo "[*] Checking in with updated properties (";
	if ($major) {
		echo 'major';
	} else {
		echo 'minor - to make a major revision pass a "1" - one - as argument to the example script';
	}
	echo ")\n";

	$checkedInDocumentId = $session->getObject($checkedOutDocumentId)->checkIn(
		$major,
		array(
			\Dkd\PhpCmis\PropertyIds::DESCRIPTION => 'New description'
		),
		$stream,
		'Checked out/in by system'
	);


	echo "[*] Versioned ID after: " . $checkedInDocumentId->getId() . "\n\n";

	echo "The following versions of README.md are now stored in the repository:\n";
	foreach ($document->getAllVersions() as $version) {
		echo "  * " . $version->getVersionLabel() . ' ';
		echo $version->isLatestVersion() ? 'LATEST' : '';
		echo $version->isLatestMajorVersion() ? 'LATEST MAJOR' : '';
		echo "\n";
	}
	echo "\n";

	echo "Please delete README.md from repository now by hand if you so wish!\n";
	echo "You can run this script again if you don't - it will use the same file/document.\n";
} catch (\Dkd\PhpCmis\Exception\CmisVersioningException $e) {
	echo "********* ERROR **********\n";
	echo $e->getMessage() . "\n";
	echo "**************************\n";
	exit();
}
