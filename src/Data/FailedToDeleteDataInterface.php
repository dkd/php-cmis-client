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
 * Failed to delete data interface
 */
interface FailedToDeleteDataInterface extends ExtensionDataInterface
{
    /**
     * Returns the list of ids that could not be deleted.
     *
     * @param string[] $ids List of ids that could not be deleted
     */
    public function setIds(array $ids);

    /**
     * Returns the list of ids that could not be deleted.
     *
     * @return string[]
     */
    public function getIds();
}
