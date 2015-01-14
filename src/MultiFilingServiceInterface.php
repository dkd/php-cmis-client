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
     * @param string $repositoryId
     * @param string $objectId
     * @param string $folderId
     * @param boolean $allVersions
     * @param ExtensionDataInterface $extension
     * @return void
     */
    public function addObjectToFolder(
        $repositoryId,
        $objectId,
        $folderId,
        $allVersions,
        ExtensionDataInterface $extension
    );

    /**
     * Removes an existing fileable non-folder object from a folder.
     *
     * @param string $repositoryId
     * @param string $objectId
     * @param string $folderId
     * @param ExtensionDataInterface $extension
     * @return void
     */
    public function removeObjectFromFolder(
        $repositoryId,
        $objectId,
        $folderId,
        ExtensionDataInterface $extension
    );
}
