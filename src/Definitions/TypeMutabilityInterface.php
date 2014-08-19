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

use Dkd\PhpCmis\Data\ExtensionsDataInterface;

/**
 * Type mutability flags.
 */
interface TypeMutabilityInterface extends ExtensionsDataInterface
{
    /**
     * Indicates if a sub type of this type can be created.
     *
     * @return boolean true if a sub type can be created, false otherwise
     */
    public function canCreate();

    /**
     * Indicates if this type can be deleted.
     *
     * @return boolean true if this type can be deleted, false otherwise
     */
    public function canDelete();

    /**
     * Indicates if this type can be updated.
     *
     * @return boolean true if this type can be updated, false otherwise
     */
    public function canUpdate();
}
