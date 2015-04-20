<?php
namespace Dkd\PhpCmis\Bindings\Browser;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Data\ExtensionDataInterface;
use Dkd\PhpCmis\MultiFilingServiceInterface;

/**
 * MultiFiling Service Browser Binding client.
 */
class MultiFilingService extends AbstractBrowserBindingService implements MultiFilingServiceInterface
{
    /**
     * Adds an existing fileable non-folder object to a folder.
     *
     * @param string $repositoryId The identifier for the repository.
     * @param string $objectId The identifier for the object.
     * @param string $folderId The folder into which the object is to be filed.
     * @param boolean $allVersions Add all versions of the object to the folder if the repository supports
     *      version-specific filing. Defaults to <code>true</code>.
     * @param ExtensionDataInterface|null $extension
     */
    public function addObjectToFolder(
        $repositoryId,
        $objectId,
        $folderId,
        $allVersions = true,
        ExtensionDataInterface $extension = null
    ) {
        // TODO: Implement addObjectToFolder() method.
    }

    /**
     * Removes an existing fileable non-folder object from a folder.
     *
     * @param string $repositoryId The identifier for the repository.
     * @param string $objectId The identifier for the object.
     * @param string|null $folderId The folder from which the object is to be removed.
     *      If no value is specified, then the repository MUST remove the object from all folders in which it is
     *      currently filed.
     * @param ExtensionDataInterface|null $extension
     */
    public function removeObjectFromFolder(
        $repositoryId,
        $objectId,
        $folderId = null,
        ExtensionDataInterface $extension = null
    ) {
        // TODO: Implement removeObjectFromFolder() method.
    }
}
