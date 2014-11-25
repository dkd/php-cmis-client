<?php
namespace Dkd\PhpCmis\DataObjects;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Data\PolicyIdListInterface;

/**
 * Id property data implementation.
 */
class PolicyIdList extends AbstractExtensionData implements PolicyIdListInterface
{
    /**
     * @var string[]
     */
    protected $policyIds = array();

    /**
     * Returns the list of policy IDs.
     *
     * @return string[] list of policy ids
     */
    public function getPolicyIds()
    {
        return $this->policyIds;
    }

    /**
     * Sets list of policy ids
     *
     * @param string[] list of policy ids
     */
    public function setPolicyIds(array $policyIds)
    {
        foreach ($policyIds as $key => $policyId) {
            $policyIds[$key] = $this->castValueToSimpleType('string', $policyId);
        }
        $this->policyIds = $policyIds;
    }
}
