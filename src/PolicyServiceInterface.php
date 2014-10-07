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
 * Policy Service interface.
 *
 * See the CMIS 1.0 and CMIS 1.1 specifications for details on the operations,
 * parameters, exceptions and the domain model.
 */
interface PolicyServiceInterface
{
    /**
     * Applies a specified policy to an object.
     *
     * @param string $repositoryId
     * @param string $policyId
     * @param string $objectId
     * @param ExtensionDataInterface $extension
     * @return mixed
     */
    public function applyPolicy($repositoryId, $policyId, $objectId, ExtensionDataInterface $extension);

    /**
     * Gets the list of policies currently applied to the specified object.
     *
     * @param string $repositoryId
     * @param string $objectId
     * @param string $filter
     * @param ExtensionDataInterface $extension
     * @return mixed
     */
    public function getAppliedPolicies($repositoryId, $objectId, $filter, ExtensionDataInterface $extension);

    /**
     * Removes a specified policy from an object.
     *
     * @param string $repositoryId
     * @param string $policyId
     * @param string $objectId
     * @param ExtensionDataInterface $extension
     * @return mixed
     */
    public function removePolicy($repositoryId, $policyId, $objectId, ExtensionDataInterface $extension);
}
