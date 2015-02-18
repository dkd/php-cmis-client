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

use Dkd\PhpCmis\Enum\Action;

/**
 * Allowable Actions.
 */
interface AllowableActionsInterface extends ExtensionDataInterface
{
    /**
     * Returns the Allowable Actions that are set.
     *
     * @return Action[] the Allowable Actions that are set, not <code>null</code>
     */
    public function getAllowableActions();
}
