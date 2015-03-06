<?php
require_once('CreateDocument.php');

echo "Now trying to update the created document...\n";

if ($document !== null) {
    $document = $session->getObject($document);
    $properties = array(\Dkd\PhpCmis\PropertyIds::DESCRIPTION => 'Updated on ' . time());
    $document->updateProperties($properties, true);

    echo "The generated document has now been updated and the property " . \Dkd\PhpCmis\PropertyIds::DESCRIPTION
            . " should now has the value '" . $properties[\Dkd\PhpCmis\PropertyIds::DESCRIPTION]. "'.\n";
    echo "Please delete that document now by hand!\n";
} else {
    exit("Document has not been created and therefore could not be updated!\n");
}
