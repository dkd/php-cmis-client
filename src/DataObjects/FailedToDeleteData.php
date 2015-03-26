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

use Dkd\PhpCmis\Data\FailedToDeleteDataInterface;

/**
 * FailedToDeleteData implementation.
 */
class FailedToDeleteData extends AbstractExtensionData implements FailedToDeleteDataInterface
{
    /**
     * @var string[]
     */
    protected $ids = array();

    /**
     * Returns the list of ids that could not be deleted.
     *
     * @param string[] $ids List of ids that could not be deleted
     */
    public function setIds(array $ids)
    {
        $this->ids = $ids;
    }

    /**
     * Returns the list of ids that could not be deleted.
     *
     * @return string[]
     */
    public function getIds()
    {
        return $this->ids;
    }
}
