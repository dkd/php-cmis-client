<?php
namespace Dkd\PhpCmis\Data;

use Dkd\PhpCmis\Enum\Action;

/**
 * Allowable Actions.
 */
interface AllowableActionsInterface extends ExtensionsDataInterface
{
    /**
     * Returns the Allowable Actions that are set.
     *
     * @return Action[] the Allowable Actions that are set, not null
     */
    public function getAllowableActions();
}
