<?php
use Dkd\PhpCmis\Data\FolderInterface;

require_once('CreateFolder.php');

echo "Now trying to delete the folder...\n";

if ($folder !== null) {
    /** @var FolderInterface $folder */
    $folder = $session->getObject($folder);
    $folder->delete(true);

    echo "The generated folder has now been deleted.\n";
} else {
    exit("Folder has not been created and therefore could not be deleted!\n");
}
