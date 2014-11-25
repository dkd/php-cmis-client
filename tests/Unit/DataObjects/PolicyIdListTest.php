<?php
namespace Dkd\PhpCmis\Test\Unit\DataObjects;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\DataObjects\PolicyIdList;

class PolicyIdListTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var PolicyIdList
     */
    protected $policyIdList;

    public function setUp()
    {
        $this->policyIdList = new PolicyIdList();
    }

    public function testSetPolicyIdsSetsProperty()
    {
        $policyIdsList = array('foo', 'bar');
        $this->policyIdList->setPolicyIds($policyIdsList);
        $this->assertAttributeSame($policyIdsList, 'policyIds', $this->policyIdList);
    }

    /**
     * @depends testSetPolicyIdsSetsProperty
     */
    public function testGetPolicyIdsReturnsPropertyValue()
    {
        $policyIdsList = array('foo', 'bar');
        $this->policyIdList->setPolicyIds($policyIdsList);
        $this->assertSame($policyIdsList, $this->policyIdList->getPolicyIds());
    }
}
