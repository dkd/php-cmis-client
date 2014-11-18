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

use Dkd\PhpCmis\DataObjects\AccessControlEntry;
use Dkd\PhpCmis\DataObjects\AccessControlList;

class AccessControlListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AccessControlList
     */
    protected $acl;

    public function setUp()
    {
        $this->acl = new AccessControlList();
    }

    public function testSetAcesSetsProperty()
    {
        $aces = array(new AccessControlEntry());
        $this->acl->setAces($aces);
        $this->assertAttributeSame($aces, 'aces', $this->acl);
    }

    public function testSetAcesThrowsExceptionIfAGivenAceItemIsNotOfTypeAceInterface()
    {
        $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException');
        $this->acl->setAces(array(new \stdClass()));
    }

    /**
     * @depends testSetAcesSetsProperty
     */
    public function testGetAcesReturnsPropertyValue()
    {
        $aces = array(new AccessControlEntry());
        $this->acl->setAces($aces);
        $this->assertSame($aces, $this->acl->getAces());
    }

    public function testSetIsExactSetsIsExact()
    {
        $this->acl->setIsExact(true);
        $this->assertAttributeSame(true, 'isExact', $this->acl);
        $this->acl->setIsExact(false);
        $this->assertAttributeSame(false, 'isExact', $this->acl);
    }

    public function testSetIsExactCastsValueToBoolean()
    {
        $this->setExpectedException('\\PHPUnit_Framework_Error_Notice');
        $this->acl->setIsExact(1);
        $this->assertAttributeSame(true, 'isExact', $this->acl);
    }

    /**
     * @depends testSetIsExactSetsIsExact
     */
    public function testIsExactReturnsIsExact()
    {
        $this->acl->setIsExact(true);
        $this->assertTrue($this->acl->isExact());
        $this->acl->setIsExact(false);
        $this->assertFalse($this->acl->isExact());
    }
}
