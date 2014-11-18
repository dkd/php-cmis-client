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

use Dkd\PhpCmis\Data\AceInterface;
use Dkd\PhpCmis\Data\AclInterface;

/**
 * Access control list data implementation.
 */
class AccessControlList extends AbstractExtensionData implements AclInterface
{
    /**
     * @var AceInterface[]
     */
    protected $aces = array();

    /**
     * @var boolean
     */
    protected $isExact;

    /**
     * @return AceInterface[]
     */
    public function getAces()
    {
        return $this->aces;
    }

    /**
     * @param AceInterface[] $aces
     */
    public function setAces(array $aces)
    {
        foreach ($aces as $ace) {
            $this->checkType('\\Dkd\\PhpCmis\\Data\\AceInterface', $ace);
        }

        $this->aces = $aces;
    }

    /**
     * @return boolean
     */
    public function isExact()
    {
        return $this->isExact;
    }

    /**
     * @param boolean $isExact
     */
    public function setIsExact($isExact)
    {
        $this->isExact = $this->castValueToSimpleType('boolean', $isExact);
    }
}
