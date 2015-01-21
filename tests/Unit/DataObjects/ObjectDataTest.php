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

use Dkd\PhpCmis\DataObjects\ObjectData;
use Dkd\PhpCmis\DataObjects\Properties;
use Dkd\PhpCmis\DataObjects\PropertyId;
use Dkd\PhpCmis\Enum\BaseTypeId;
use Dkd\PhpCmis\PropertyIds;

class ObjectDataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectData
     */
    protected $objectData;

    public function setUp()
    {
        $this->objectData = new ObjectData();
    }

    public function testSetAclSetsProperty()
    {
        $acl = $this->getMockForAbstractClass('\\Dkd\\PhpCmis\\Data\\AclInterface');
        $this->objectData->setAcl($acl);
        $this->assertAttributeSame($acl, 'acl', $this->objectData);
    }

    /**
     * @depends testSetAclSetsProperty
     */
    public function testGetAclReturnsPropertyValue()
    {
        $acl = $this->getMockForAbstractClass('\\Dkd\\PhpCmis\\Data\\AclInterface');
        $this->objectData->setAcl($acl);
        $this->assertSame($acl, $this->objectData->getAcl());
    }

    public function testSetAllowableActionsSetsProperty()
    {
        $allowableActions = $this->getMockForAbstractClass('\\Dkd\\PhpCmis\\Data\\AllowableActionsInterface');
        $this->objectData->setAllowableActions($allowableActions);
        $this->assertAttributeSame($allowableActions, 'allowableActions', $this->objectData);
    }

    /**
     * @depends testSetAllowableActionsSetsProperty
     */
    public function testGetAllowableActionsReturnsPropertyValue()
    {
        $allowableActions = $this->getMockForAbstractClass('\\Dkd\\PhpCmis\\Data\\AllowableActionsInterface');
        $this->objectData->setAllowableActions($allowableActions);
        $this->assertSame($allowableActions, $this->objectData->getAllowableActions());
    }

    public function testSetChangeEventInfoSetsProperty()
    {
        $changeEventInfo = $this->getMockForAbstractClass('\\Dkd\\PhpCmis\\Data\\ChangeEventInfoInterface');
        $this->objectData->setChangeEventInfo($changeEventInfo);
        $this->assertAttributeSame($changeEventInfo, 'changeEventInfo', $this->objectData);
    }

    /**
     * @depends testSetChangeEventInfoSetsProperty
     */
    public function testGetChangeEventInfoReturnsPropertyValue()
    {
        $changeEventInfo = $this->getMockForAbstractClass('\\Dkd\\PhpCmis\\Data\\ChangeEventInfoInterface');
        $this->objectData->setChangeEventInfo($changeEventInfo);
        $this->assertSame($changeEventInfo, $this->objectData->getChangeEventInfo());
    }

    public function testSetIsExactAclSetsProperty()
    {
        $this->objectData->setIsExactAcl(true);
        $this->assertAttributeSame(true, 'isExactAcl', $this->objectData);
        $this->objectData->setIsExactAcl(false);
        $this->assertAttributeSame(false, 'isExactAcl', $this->objectData);
    }

    /**
     * @depends testSetIsExactAclSetsProperty
     */
    public function testGetIsExactAclReturnsPropertyValue()
    {
        $this->objectData->setIsExactAcl(true);
        $this->assertTrue($this->objectData->isExactAcl());
    }


    public function testSetPolicyIdsSetsProperty()
    {
        $policyIds = $this->getMockForAbstractClass('\\Dkd\\PhpCmis\\Data\\PolicyIdListInterface');
        $this->objectData->setPolicyIds($policyIds);
        $this->assertAttributeSame($policyIds, 'policyIds', $this->objectData);
    }

    /**
     * @depends testSetPolicyIdsSetsProperty
     */
    public function testGetPolicyIdsReturnsPropertyValue()
    {
        $policyIds = $this->getMockForAbstractClass('\\Dkd\\PhpCmis\\Data\\PolicyIdListInterface');
        $this->objectData->setPolicyIds($policyIds);
        $this->assertSame($policyIds, $this->objectData->getPolicyIds());
    }

    public function testSetPropertiesSetsProperty()
    {
        $properties = $this->getMockForAbstractClass('\\Dkd\\PhpCmis\\Data\\PropertiesInterface');
        $this->objectData->setProperties($properties);
        $this->assertAttributeSame($properties, 'properties', $this->objectData);
    }

    /**
     * @depends testSetPropertiesSetsProperty
     */
    public function testGetPropertiesReturnsPropertyValue()
    {
        $properties = $this->getMockForAbstractClass('\\Dkd\\PhpCmis\\Data\\PropertiesInterface');
        $this->objectData->setProperties($properties);
        $this->assertSame($properties, $this->objectData->getProperties());
    }

    public function testSetRelationshipsSetsProperty()
    {
        $relationships = array($this->getMockForAbstractClass('\\Dkd\\PhpCmis\\Data\\ObjectDataInterface'));
        $this->objectData->setRelationships($relationships);
        $this->assertAttributeSame($relationships, 'relationships', $this->objectData);
    }

    /**
     * @depends testSetRelationshipsSetsProperty
     */
    public function testGetRelationshipsReturnsPropertyValue()
    {
        $relationships = array($this->getMockForAbstractClass('\\Dkd\\PhpCmis\\Data\\ObjectDataInterface'));
        $this->objectData->setRelationships($relationships);
        $this->assertSame($relationships, $this->objectData->getRelationships());
    }

    public function testSetRenditionsSetsProperty()
    {
        $renditions = array($this->getMockForAbstractClass('\\Dkd\\PhpCmis\\Data\\ObjectDataInterface'));
        $this->objectData->setRenditions($renditions);
        $this->assertAttributeSame($renditions, 'renditions', $this->objectData);
    }

    /**
     * @depends testSetRenditionsSetsProperty
     */
    public function testGetRenditionsReturnsPropertyValue()
    {
        $renditions = array($this->getMockForAbstractClass('\\Dkd\\PhpCmis\\Data\\ObjectDataInterface'));
        $this->objectData->setRenditions($renditions);
        $this->assertSame($renditions, $this->objectData->getRenditions());
    }

    public function testGetIdReturnsNullIfPropertyDoesNotExist()
    {
        $this->assertNull($this->objectData->getId());
    }

    public function testGetIdReturnsIdPropertyValue()
    {
        $idProperty = new PropertyId(PropertyIds::OBJECT_ID, 'fooPropertyId');
        $properties = new Properties();
        $properties->addProperty($idProperty);
        $this->objectData->setProperties($properties);

        $this->assertSame('fooPropertyId', $this->objectData->getId());
    }

    public function testGetIdReturnsFirstValueOfIdMultiValuePropertyValue()
    {
        $idProperty = new PropertyId(PropertyIds::OBJECT_ID, array('fooPropertyId', 'secondValue'));
        $properties = new Properties();
        $properties->addProperty($idProperty);
        $this->objectData->setProperties($properties);

        $this->assertSame('fooPropertyId', $this->objectData->getId());
    }

    public function testGetBaseTypeIdReturnsNullIfPropertyDoesNotExist()
    {
        $this->assertNull($this->objectData->getBaseTypeId());
    }

    public function testGetBaseTypeIdReturnsNullIfRequestedPropertyDoesNotExist()
    {
        $idProperty = new PropertyId(PropertyIds::OBJECT_ID, array('fooPropertyId', 'secondValue'));
        $properties = new Properties();
        $properties->addProperty($idProperty);
        $this->objectData->setProperties($properties);

        $this->assertNull($this->objectData->getBaseTypeId());
    }

    public function testGetBaseTypeIdReturnsNullIfBaseTypeIdValueIsInvalid()
    {
        $idProperty = new PropertyId(PropertyIds::BASE_TYPE_ID, 'invalidBaseTypeId');
        $properties = new Properties();
        $properties->addProperty($idProperty);
        $this->objectData->setProperties($properties);

        $this->assertNull($this->objectData->getBaseTypeId());
    }

    public function testGetBaseTypeIdReturnsIdPropertyValue()
    {
        $idProperty = new PropertyId(PropertyIds::BASE_TYPE_ID, 'cmis:item');
        $properties = new Properties();
        $properties->addProperty($idProperty);
        $this->objectData->setProperties($properties);

        $this->assertEquals(BaseTypeId::cast(BaseTypeId::CMIS_ITEM), $this->objectData->getBaseTypeId());
    }

    public function testGetBaseTypeIdReturnsFirstValueOfIdMultiValuePropertyValue()
    {
        $idProperty = new PropertyId(PropertyIds::BASE_TYPE_ID, array('cmis:item', 'cmis:document'));
        $properties = new Properties();
        $properties->addProperty($idProperty);
        $this->objectData->setProperties($properties);

        $this->assertEquals(BaseTypeId::cast(BaseTypeId::CMIS_ITEM), $this->objectData->getBaseTypeId());
    }
}
