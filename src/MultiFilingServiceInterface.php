<?php
namespace Dkd\PhpCmis;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Data\ExtensionDataInterface;

/**
 * MultiFiling Service interface.
 *
 * See the CMIS 1.0 and CMIS 1.1 specifications for details on the operations,
 * parameters, exceptions and the domain model.
 */
interface MultiFilingServiceInterface
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
    );

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
    );
}
