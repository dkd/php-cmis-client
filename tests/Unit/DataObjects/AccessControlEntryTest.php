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
use Dkd\PhpCmis\DataObjects\Principal;

class AccessControlEntryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var AccessControlEntry
     */
    protected $ace;

    public function setUp()
    {
        $this->ace = new AccessControlEntry();
    }

    public function testSetPermissionsSetsPermissions()
    {
        $permissions = array('foo', 'bar');
        $this->ace->setPermissions($permissions);
        $this->assertAttributeSame($permissions, 'permissions', $this->ace);
    }

    public function testSetPermissionsThrowsExceptionIfPermissionItemIsNotOfTypeString()
    {
        $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException');
        $this->ace->setPermissions(array(new \stdClass()));
    }

    /**
     * @depends testSetPermissionsSetsPermissions
     */
    public function testGetPermissionsReturnsPermissions()
    {
        $permissions = array('foo', 'bar');
        $this->ace->setPermissions($permissions);
        $this->assertSame($permissions, $this->ace->getPermissions());
    }

    public function testSetPrincipalSetsPrincipal()
    {
        $principal = new Principal();
        $this->ace->setPrincipal($principal);
        $this->assertAttributeSame($principal, 'principal', $this->ace);
    }

    /**
     * @depends testSetPrincipalSetsPrincipal
     */
    public function testGetPrincipalReturnsPrincipal()
    {
        $principal = new Principal();
        $this->ace->setPrincipal($principal);
        $this->assertSame($principal, $this->ace->getPrincipal());
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
