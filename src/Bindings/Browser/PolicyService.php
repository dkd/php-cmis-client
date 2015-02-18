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
use Dkd\PhpCmis\Data\ObjectDataInterface;
use Dkd\PhpCmis\PolicyServiceInterface;

/**
 * Policy Service Browser Binding client.
 */
class PolicyService extends AbstractBrowserBindingService implements PolicyServiceInterface
{
    /**
     * Applies a specified policy to an object.
     *
     * @param string $repositoryId The identifier for the repository.
     * @param string $policyId The identifier for the policy to be applied.
     * @param string $objectId The identifier of the object.
     * @param ExtensionDataInterface|null $extension
     */
    public function applyPolicy($repositoryId, $policyId, $objectId, ExtensionDataInterface $extension = null)
    {
        // TODO: Implement applyPolicy() method.
    }

    /**
     * Gets the list of policies currently applied to the specified object.
     *
     * @param string $repositoryId The identifier for the repository.
     * @param string $objectId The identifier of the object.
     * @param string|null $filter a comma-separated list of query names that defines which properties must be
     *      returned by the repository (default is repository specific)
     * @param ExtensionDataInterface|null $extension
     * @return ObjectDataInterface[] A list of the policy objects.
     */
    public function getAppliedPolicies(
        $repositoryId,
        $objectId,
        $filter = null,
        ExtensionDataInterface $extension = null
    ) {
        // TODO: Implement getAppliedPolicies() method.
    }

    /**
     * Removes a specified policy from an object.
     *
     * @param string $repositoryId The identifier for the repository.
     * @param string $policyId The identifier for the policy to be removed.
     * @param string $objectId The identifier of the object.
     * @param ExtensionDataInterface|null $extension
     */
    public function removePolicy($repositoryId, $policyId, $objectId, ExtensionDataInterface $extension = null)
    {
        // TODO: Implement removePolicy() method.
    }
}
