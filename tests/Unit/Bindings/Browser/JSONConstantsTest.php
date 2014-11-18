<?php

namespace Dkd\PhpCmis\Test\Unit\Bindings\Browser;

use Dkd\PhpCmis\Bindings\Browser\JSONConstants;

/**
 * This file is part of php-cmis-client
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class JSONConstantsTest extends \PHPUnit_Framework_TestCase
{

    public function testGetRepositoryInfoKeysReturnsContentOfStaticArray()
    {
        $this->assertSame(
            $this->getStaticAttribute('\\Dkd\\PhpCmis\\Bindings\\Browser\\JSONConstants', 'REPOSITORY_INFO_KEYS'),
            JSONConstants::getRepositoryInfoKeys()
        );
    }

    public function testGetCapabilityKeysReturnsContentOfStaticArray()
    {
        $this->assertSame(
            $this->getStaticAttribute('\\Dkd\\PhpCmis\\Bindings\\Browser\\JSONConstants', 'CAPABILITY_KEYS'),
            JSONConstants::getCapabilityKeys()
        );
    }

    public function testGetCapabilityCreatablePropertyKeysReturnsContentOfStaticArray()
    {
        $this->assertSame(
            $this->getStaticAttribute(
                '\\Dkd\\PhpCmis\\Bindings\\Browser\\JSONConstants',
                'CAPABILITY_CREATABLE_PROPERTY_KEYS'
            ),
            JSONConstants::getCapabilityCreatablePropertyKeys()
        );
    }

    public function testGetCapabilityNewTypeSettableAttributeKeysReturnsContentOfStaticArray()
    {
        $this->assertSame(
            $this->getStaticAttribute(
                '\\Dkd\\PhpCmis\\Bindings\\Browser\\JSONConstants',
                'CAP_NEW_TYPE_SETTABLE_ATTRIBUTES_KEYS'
            ),
            JSONConstants::getCapabilityNewTypeSettableAttributeKeys()
        );
    }

    public function testGetAclCapabilityKeysReturnsContentOfStaticArray()
    {
        $this->assertSame(
            $this->getStaticAttribute('\\Dkd\\PhpCmis\\Bindings\\Browser\\JSONConstants', 'ACL_CAPABILITY_KEYS'),
            JSONConstants::getAclCapabilityKeys()
        );
    }

    public function testGetAclCapabilityPermissionKeysReturnsContentOfStaticArray()
    {
        $this->assertSame(
            $this->getStaticAttribute(
                '\\Dkd\\PhpCmis\\Bindings\\Browser\\JSONConstants',
                'ACL_CAPABILITY_PERMISSION_KEYS'
            ),
            JSONConstants::getAclCapabilityPermissionKeys()
        );
    }

    public function testGetAclCapabilityMappingKeysReturnsContentOfStaticArray()
    {
        $this->assertSame(
            $this->getStaticAttribute(
                '\\Dkd\\PhpCmis\\Bindings\\Browser\\JSONConstants',
                'ACL_CAPABILITY_MAPPING_KEYS'
            ),
            JSONConstants::getAclCapabilityMappingKeys()
        );
    }

    public function testGetObjectKeysReturnsContentOfStaticArray()
    {
        $this->assertSame(
            $this->getStaticAttribute('\\Dkd\\PhpCmis\\Bindings\\Browser\\JSONConstants', 'OBJECT_KEYS'),
            JSONConstants::getObjectKeys()
        );
    }

    public function testGetPropertyKeysReturnsContentOfStaticArray()
    {
        $this->assertSame(
            $this->getStaticAttribute('\\Dkd\\PhpCmis\\Bindings\\Browser\\JSONConstants', 'PROPERTY_KEYS'),
            JSONConstants::getPropertyKeys()
        );
    }

    public function testGetChangeEventKeysReturnsContentOfStaticArray()
    {
        $this->assertSame(
            $this->getStaticAttribute('\\Dkd\\PhpCmis\\Bindings\\Browser\\JSONConstants', 'CHANGE_EVENT_KEYS'),
            JSONConstants::getChangeEventKeys()
        );
    }

    public function testGetRenditionKeysReturnsContentOfStaticArray()
    {
        $this->assertSame(
            $this->getStaticAttribute('\\Dkd\\PhpCmis\\Bindings\\Browser\\JSONConstants', 'RENDITION_KEYS'),
            JSONConstants::getRenditionKeys()
        );
    }

    public function testGetFeatureKeysReturnsContentOfStaticArray()
    {
        $this->assertSame(
            $this->getStaticAttribute('\\Dkd\\PhpCmis\\Bindings\\Browser\\JSONConstants', 'FEATURE_KEYS'),
            JSONConstants::getFeatureKeys()
        );
    }

    public function testGetPolicyIdsKeysReturnsContentOfStaticArray()
    {
        $this->assertSame(
            $this->getStaticAttribute('\\Dkd\\PhpCmis\\Bindings\\Browser\\JSONConstants', 'POLICY_IDS_KEYS'),
            JSONConstants::getPolicyIdsKeys()
        );
    }

    public function testGetAclKeysReturnsContentOfStaticArray()
    {
        $this->assertSame(
            $this->getStaticAttribute('\\Dkd\\PhpCmis\\Bindings\\Browser\\JSONConstants', 'ACL_KEYS'),
            JSONConstants::getAclKeys()
        );
    }

    public function testGetPrincipalKeysReturnsContentOfStaticArray()
    {
        $this->assertSame(
            $this->getStaticAttribute('\\Dkd\\PhpCmis\\Bindings\\Browser\\JSONConstants', 'ACE_PRINCIPAL_KEYS'),
            JSONConstants::getAcePrincipalKeys()
        );
    }

    public function testGetAceKeysReturnsContentOfStaticArray()
    {
        $this->assertSame(
            $this->getStaticAttribute('\\Dkd\\PhpCmis\\Bindings\\Browser\\JSONConstants', 'ACE_KEYS'),
            JSONConstants::getAceKeys()
        );
    }
}
