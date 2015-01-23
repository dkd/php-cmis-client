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
use Dkd\PhpCmis\PrincipalInterface;
use PHPUnit_Framework_MockObject_MockObject;

class AccessControlEntryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var AccessControlEntry
     */
    protected $ace;

    /**
     * @var string[]
     */
    protected $dummyPermissions = array('foo', 'bar');

    /**
     * @var PrincipalInterface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $dummyPrincipal;

    public function setUp()
    {
        $this->dummyPrincipal = $this->getMockBuilder('\\Dkd\\PhpCmis\\PrincipalInterface')->getMockForAbstractClass();
        $this->ace = new AccessControlEntry(
            $this->dummyPrincipal,
            $this->dummyPermissions
        );
    }

    public function testSetPermissionsSetsPermissions()
    {
        $permissions = array('baz', 'bazz');
        $this->ace->setPermissions($permissions);
        $this->assertAttributeSame($permissions, 'permissions', $this->ace);
    }

    public function testSetPermissionsThrowsExceptionIfPermissionItemIsNotOfTypeString()
    {
        $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException');
        $this->ace->setPermissions(array(new \stdClass()));
    }

    public function testSetPrincipalSetsPrincipal()
    {
        $principal = $this->getMockBuilder('\\Dkd\\PhpCmis\\PrincipalInterface')->getMockForAbstractClass();
        $this->ace->setPrincipal($principal);
        $this->assertAttributeSame($principal, 'principal', $this->ace);
    }

    /**
     * @depends testSetPermissionsSetsPermissions
     * @depends testSetPrincipalSetsPrincipal
     */
    public function testConstructorSetsPermissionAndPrincipalIfGiven()
    {
        $this->assertAttributeSame($this->dummyPrincipal, 'principal', $this->ace);
        $this->assertAttributeSame($this->dummyPermissions, 'permissions', $this->ace);
    }

    /**
     * @depends testSetPermissionsSetsPermissions
     */
    public function testGetPermissionsReturnsPermissions()
    {
        $this->assertSame($this->dummyPermissions, $this->ace->getPermissions());
    }

    /**
     * @depends testSetPrincipalSetsPrincipal
     */
    public function testGetPrincipalReturnsPrincipal()
    {
        $this->assertSame($this->dummyPrincipal, $this->ace->getPrincipal());
    }

    public function testSetIsDirectSetsIsDirect()
    {
        $this->ace->setIsDirect(true);
        $this->assertAttributeSame(true, 'isDirect', $this->ace);
        $this->ace->setIsDirect(false);
        $this->assertAttributeSame(false, 'isDirect', $this->ace);
    }

    public function testSetIsDirectCastsValueToBoolean()
    {
        $this->setExpectedException('\\PHPUnit_Framework_Error_Notice');
        $this->ace->setIsDirect(1);
        $this->assertAttributeSame(true, 'isDirect', $this->ace);
    }

    /**
     * @depends testSetIsDirectSetsIsDirect
     */
    public function testIsDirectReturnsIsDirect()
    {
        $this->ace->setIsDirect(true);
        $this->assertTrue($this->ace->isDirect());
        $this->ace->setIsDirect(false);
        $this->assertFalse($this->ace->isDirect());
    }
}
