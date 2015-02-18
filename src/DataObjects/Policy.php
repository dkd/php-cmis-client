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

use Dkd\PhpCmis\Data\PolicyInterface;
use Dkd\PhpCmis\PropertyIds;

/**
 * Cmis Policy implementation
 */
class Policy extends AbstractFileableCmisObject implements PolicyInterface
{
    /**
     * Returns the policy text of this CMIS policy (CMIS property cmis:policyText).
     *
     * @return string|null the policy text or <code>null</code> if the property hasn't been requested,
     *                     hasn't been provided by the repository, or the property value isn't set
     */
    public function getPolicyText()
    {
        return $this->getPropertyValue(PropertyIds::POLICY_TEXT);
    }
}
