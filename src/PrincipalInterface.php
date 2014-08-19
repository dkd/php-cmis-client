<?php
namespace Dkd\PhpCmis;

use Dkd\PhpCmis\Data\ExtensionsDataInterface;

/**
 * ACE Principal
 */
interface PrincipalInterface extends ExtensionsDataInterface
{
    /**
     * Returns the principal ID.
     *
     * @return string
     */
    public function getId();
}
