<?php
namespace Dkd\PhpCmis\Data;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * List of policy IDs.
 */
interface PolicyIdListInterface extends ExtensionDataInterface
{
    /**
     * Returns the list policy IDs.
     *
     * @return string[] list of policy ids
     */
    public function getPolicyIds();

    /**
     * Sets list of policy ids
     *
     * @param string[] list of policy ids
     */
    public function setPolicyIds(array $policyIds);
}
