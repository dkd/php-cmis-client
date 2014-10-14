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

use Dkd\PhpCmis\DataObjects\AclCapabilities;
use Dkd\PhpCmis\Enum\AclPropagation;
use Dkd\PhpCmis\Enum\SupportedPermissions;

class AclCapabilitiesTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var AclCapabilities
     */
    protected $aclCapabilities;

    public function setUp()
    {
        $this->aclCapabilities = new AclCapabilities();
    }

    public function testSetAclPropagationSetsProperty()
    {
        $this->aclCapabilities->setAclPropagation(AclPropagation::cast(AclPropagation::OBJECTONLY));
    }

    /**
     * @depends testSetAclPropagationSetsProperty
     */
    public function testGetAclPropagationReturnsPropertyValue()
    {
        $aclPropagation = AclPropagation::cast(AclPropagation::OBJECTONLY);
        $this->aclCapabilities->setAclPropagation($aclPropagation);
        $this->assertSame($aclPropagation, $this->aclCapabilities->getAclPropagation());
    }

    public function testSetPermissionsSetsProperty()
    {
        $permissionDefinition = $this->getMockForAbstractClass(
            '\\Dkd\\PhpCmis\\Definitions\\PermissionDefinitionInterface'
        );
        $this->aclCapabilities->setPermissions(array($permissionDefinition));
    }

    /**
     * @dataProvider invalidPermissionDefinitionsDataProvider
     * @param $permissionDefinition
     * @param $expectedExceptionText
     */
    public function testSetPermissionsThrowsExceptionIfInvalidAttributeGiven(
        $permissionDefinition,
        $expectedExceptionText
    ) {
        $this->setExpectedException('\\InvalidArgumentException', $expectedExceptionText);
        $this->aclCapabilities->setPermissions(array($permissionDefinition));
    }

    public function invalidPermissionDefinitionsDataProvider()
    {
        return array(
            array(
                'string',
                'Argument of type "string" given but argument of type '
                . '"\\Dkd\\PhpCmis\\Definitions\\PermissionDefinitionInterface" was expected.'
            ),
            array(
                0,
                'Argument of type "integer" given but argument of type '
                . '"\\Dkd\\PhpCmis\\Definitions\\PermissionDefinitionInterface" was expected.'
            ),
            array(
                array(),
                'Argument of type "array" given but argument of type '
                . '"\\Dkd\\PhpCmis\\Definitions\\PermissionDefinitionInterface" was expected.'
            ),
            array(
                new \stdClass(),
                'Argument of type "stdClass" given but argument of type '
                . '"\\Dkd\\PhpCmis\\Definitions\\PermissionDefinitionInterface" was expected.'
            )
        );
    }

    /**
     * @depends testSetPermissionsSetsProperty
     */
    public function testGetPermissionsReturnsPropertyValue()
    {
        $permissionDefinition = $this->getMockForAbstractClass(
            '\\Dkd\\PhpCmis\\Definitions\\PermissionDefinitionInterface'
        );
        $this->aclCapabilities->setPermissions(array($permissionDefinition));
        $this->assertSame(array($permissionDefinition), $this->aclCapabilities->getPermissions());
    }

    public function testSetPermissionMappingSetsProperty()
    {
        $permissionMapping = $this->getMockForAbstractClass(
            '\\Dkd\\PhpCmis\\Definitions\\PermissionMappingInterface'
        );
        $this->aclCapabilities->setPermissionMapping(array($permissionMapping));
    }

    /**
     * @dataProvider invalidPermissionDefinitionsDataProvider
     * @param $permissionDefinition
     * @param $expectedExceptionText
     */
    public function testSetPermissionMappingThrowsExceptionIfInvalidAttributeGiven(
        $permissionDefinition,
        $expectedExceptionText
    ) {
        $this->setExpectedException('\\InvalidArgumentException', $expectedExceptionText);
        $this->aclCapabilities->setPermissions(array($permissionDefinition));
    }

    public function invalidPermissionMappingsDataProvider()
    {
        return array(
            array(
                'string',
                'Argument of type "string" given but argument of type '
                . '"\\Dkd\\PhpCmis\\Definitions\\PermissionMappingInterface" was expected.'
            ),
            array(
                0,
                'Argument of type "integer" given but argument of type '
                . '"\\Dkd\\PhpCmis\\Definitions\\PermissionMappingInterface" was expected.'
            ),
            array(
                array(),
                'Argument of type "array" given but argument of type '
                . '"\\Dkd\\PhpCmis\\Definitions\\PermissionMappingInterface" was expected.'
            ),
            array(
                new \stdClass(),
                'Argument of type "stdClass" given but argument of type '
                . '"\\Dkd\\PhpCmis\\Definitions\\PermissionMappingInterface" was expected.'
            )
        );
    }

    /**
     * @depends testSetPermissionMappingSetsProperty
     */
    public function testGetPermissionMappingReturnsPropertyValue()
    {
        $permissionMapping = $this->getMockForAbstractClass(
            '\\Dkd\\PhpCmis\\Definitions\\PermissionMappingInterface'
        );
        $this->aclCapabilities->setPermissionMapping(array($permissionMapping));
        $this->assertSame(array($permissionMapping), $this->aclCapabilities->getPermissionMapping());
    }

    public function testSetSupportedPermissionsSetsProperty()
    {
        $this->aclCapabilities->setSupportedPermissions(SupportedPermissions::cast(SupportedPermissions::BOTH));
    }

    /**
     * @depends testSetSupportedPermissionsSetsProperty
     */
    public function testGetSupportedPermissionsReturnsPropertyValue()
    {
        $supportedPermissions = SupportedPermissions::cast(SupportedPermissions::BOTH);
        $this->aclCapabilities->setSupportedPermissions($supportedPermissions);
        $this->assertSame($supportedPermissions, $this->aclCapabilities->getSupportedPermissions());
    }
}
