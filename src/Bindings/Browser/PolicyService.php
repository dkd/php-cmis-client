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
     * @param string $repositoryId
     * @param string $policyId
     * @param string $objectId
     * @param ExtensionDataInterface $extension
     * @return void
     */
    public function applyPolicy($repositoryId, $policyId, $objectId, ExtensionDataInterface $extension)
    {
        // TODO: Implement applyPolicy() method.
    }

    /**
     * Gets the list of policies currently applied to the specified object.
     *
     * @param string $repositoryId
     * @param string $objectId
     * @param string $filter
     * @param ExtensionDataInterface $extension
     * @return ObjectDataInterface[]
     */
    public function getAppliedPolicies($repositoryId, $objectId, $filter, ExtensionDataInterface $extension)
    {
        // TODO: Implement getAppliedPolicies() method.
    }

    /**
     * Removes a specified policy from an object.
     *
     * @param string $repositoryId
     * @param string $policyId
     * @param string $objectId
     * @param ExtensionDataInterface $extension
     * @return void
     */
    public function removePolicy($repositoryId, $policyId, $objectId, ExtensionDataInterface $extension)
    {
        // TODO: Implement removePolicy() method.
    }
}
