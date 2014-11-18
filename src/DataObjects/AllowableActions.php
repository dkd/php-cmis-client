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

use Dkd\PhpCmis\Data\AllowableActionsInterface;
use Dkd\PhpCmis\Enum\Action;

/**
 * Allowable Actions.
 */
class AllowableActions extends AbstractExtensionData implements AllowableActionsInterface
{
    /**
     * @var Action[]
     */
    protected $allowableActions;

    /**
     * @return Action[]
     */
    public function getAllowableActions()
    {
        return $this->allowableActions;
    }

    /**
     * @param Action[] $allowableActions
     */
    public function setAllowableActions(array $allowableActions)
    {
        foreach ($allowableActions as $action) {
            $this->checkType('\\Dkd\\PhpCmis\\Enum\\Action', $action);
        }
        $this->allowableActions = $allowableActions;
    }
}
