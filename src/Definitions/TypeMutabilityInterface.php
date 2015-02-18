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
 * Type mutability flags.
 */
interface TypeMutabilityInterface extends ExtensionDataInterface
{
    /**
     * Indicates if a sub type of this type can be created.
     *
     * @return boolean <code>true</code> if a sub type can be created, <code>false</code> otherwise
     */
    public function canCreate();

    /**
     * Indicates if this type can be deleted.
     *
     * @return boolean <code>true</code> if this type can be deleted, <code>false</code> otherwise
     */
    public function canDelete();

    /**
     * Indicates if this type can be updated.
     *
     * @return boolean <code>true</code> if this type can be updated, <code>false</code> otherwise
     */
    public function canUpdate();
}
