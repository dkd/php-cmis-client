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

use Dkd\PhpCmis\DataObjects\PermissionMapping;
use Dkd\PhpCmis\Test\Unit\DataProviderCollectionTrait;

class PermissionMappingTest extends \PHPUnit_Framework_TestCase
{
    use DataProviderCollectionTrait;

    /**
     * @var PermissionMapping
     */
    protected $permissionMapping;

    public function setUp()
    {
        $this->permissionMapping = new PermissionMapping();
    }

    /**
     * @dataProvider stringCastDataProvider
     * @param string $expected
     * @param mixed $value
     */
    public function testSetPermissionsSetsProperty($expected, $value)
    {
        $this->permissionMapping->setPermissions(array($value));
        $this->assertAttributeSame(array($expected), 'permissions', $this->permissionMapping);
    }

    /**
     * @depends testSetPermissionsSetsProperty
     */
    public function testGetPermissionsReturnsPropertyValue()
    {
        $this->permissionMapping->setPermissions(array('foo'));
        $this->assertSame(array('foo'), $this->permissionMapping->getPermissions());
    }

    /**
     * @dataProvider stringCastDataProvider
     * @param string $expected
     * @param mixed $value
     */
    public function testSetKeySetsProperty($expected, $value)
    {
        $this->permissionMapping->setKey($value);
        $this->assertAttributeSame($expected, 'key', $this->permissionMapping);
    }

    /**
     * @depends testSetKeySetsProperty
     */
    public function testGetKeyReturnsPropertyValue()
    {
        $this->permissionMapping->setKey('foo');
        $this->assertSame('foo', $this->permissionMapping->getKey());
    }
}
