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
use Dkd\PhpCmis\Data\ObjectListInterface;
use Dkd\PhpCmis\Enum\IncludeRelationships;

/**
 * Discovery Service interface.
 *
 * See the CMIS 1.0 and CMIS 1.1 specifications for details on the operations,
 * parameters, exceptions and the domain model.
 */
interface DiscoveryServiceInterface
{
    /**
     * Gets a list of content changes.
     *
     * @param string $repositoryId
     * @param string $changeLogToken
     * @param boolean $includeProperties
     * @param string $filter
     * @param boolean $includePolicyIds
     * @param boolean $includeAcl
     * @param int $maxItems
     * @param ExtensionDataInterface $extension
     * @return ObjectListInterface
     */
    public function getContentChanges(
        $repositoryId,
        $changeLogToken,
        $includeProperties,
        $filter,
        $includePolicyIds,
        $includeAcl,
        $maxItems,
        Data\ExtensionDataInterface $extension
    );

    /**
     * Executes a CMIS query statement against the contents of the repository.
     *
     * @param string $repositoryId
     * @param string $statement
     * @param boolean $searchAllVersions
     * @param boolean $includeAllowableActions
     * @param IncludeRelationships $includeRelationships
     * @param string $renditionFilter
     * @param int $maxItems
     * @param int $skipCount
     * @param ExtensionDataInterface $extension
     * @return ObjectListInterface
     */
    public function query(
        $repositoryId,
        $statement,
        $searchAllVersions,
        $includeAllowableActions,
        Enum\IncludeRelationships $includeRelationships,
        $renditionFilter,
        $maxItems,
        $skipCount,
        Data\ExtensionDataInterface $extension
    );
}
