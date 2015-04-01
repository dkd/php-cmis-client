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

use Dkd\PhpCmis\Data\ChangeEventInfoInterface;
use Dkd\PhpCmis\Enum\ChangeType;

/**
 * Change Event Info
 */
class ChangeEventInfo extends AbstractExtensionData implements ChangeEventInfoInterface
{

    /**
     * @var \DateTime
     */
    protected $changeTime;

    /**
     * @var ChangeType
     */
    protected $changeType;

    /**
     * @return \DateTime
     */
    public function getChangeTime()
    {
        return $this->changeTime;
    }

    /**
     * @param \DateTime $changeTime
     */
    public function setChangeTime(\DateTime $changeTime)
    {
        $this->changeTime = $changeTime;
    }

    /**
     * @return ChangeType
     */
    public function getChangeType()
    {
        return $this->changeType;
    }

    /**
     * @param ChangeType $changeType
     */
    public function setChangeType(ChangeType $changeType)
    {
        $this->changeType = $changeType;
    }
}
