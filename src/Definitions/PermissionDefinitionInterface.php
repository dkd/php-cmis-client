<?php
namespace Dkd\PhpCmis\Definitions;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Data\ExtensionDataInterface;

/**
 * Permission definition.
 */
interface PermissionDefinitionInterface extends ExtensionDataInterface
{
    /**
     * Returns a human readable description of the permission.
     *
     * @return string
     */
    public function getDescription();

    /**
     * Returns the permission ID.
     *
     * @return string
     */
    public function getId();
}
