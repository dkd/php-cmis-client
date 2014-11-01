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
        $aclPropagation = AclPropagation::cast(AclPropagation::OBJECTONLY);
        $this->aclCapabilities->setAclPropagation($aclPropagation);
        $this->assertAttributeSame($aclPropagation, 'aclPropagation', $this->aclCapabilities);
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
        $permissionDefinitions = array($this->getMockForAbstractClass(
            '\\Dkd\\PhpCmis\\Definitions\\PermissionDefinitionInterface'
        ));
        $this->aclCapabilities->setPermissions($permissionDefinitions);
        $this->assertAttributeSame($permissionDefinitions, 'permissions', $this->aclCapabilities);
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
        $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException', $expectedExceptionText);
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
        $permissionMapping = array($this->getMockForAbstractClass(
            '\\Dkd\\PhpCmis\\Data\\PermissionMappingInterface'
        ));
        $this->aclCapabilities->setPermissionMapping($permissionMapping);
        $this->assertAttributeSame($permissionMapping, 'permissionMapping', $this->aclCapabilities);
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
        $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException', $expectedExceptionText);
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
            '\\Dkd\\PhpCmis\\Data\\PermissionMappingInterface'
        );
        $this->aclCapabilities->setPermissionMapping(array($permissionMapping));
        $this->assertSame(array($permissionMapping), $this->aclCapabilities->getPermissionMapping());
    }

    public function testSetSupportedPermissionsSetsProperty()
    {
        $supportedPermissions = SupportedPermissions::cast(SupportedPermissions::BOTH);
        $this->aclCapabilities->setSupportedPermissions($supportedPermissions);
        $this->assertAttributeSame($supportedPermissions, 'supportedPermissions', $this->aclCapabilities);
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
