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
