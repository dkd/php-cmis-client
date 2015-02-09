<?php
namespace Dkd\PhpCmis\Test\Unit\Converter;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Bindings\Browser\JSONConstants;
use Dkd\PhpCmis\Converter\JsonConverter;
use Dkd\PhpCmis\DataObjects\AccessControlEntry;
use Dkd\PhpCmis\DataObjects\AccessControlList;
use Dkd\PhpCmis\DataObjects\AclCapabilities;
use Dkd\PhpCmis\DataObjects\AllowableActions;
use Dkd\PhpCmis\DataObjects\ChangeEventInfo;
use Dkd\PhpCmis\DataObjects\CmisExtensionElement;
use Dkd\PhpCmis\DataObjects\CreatablePropertyTypes;
use Dkd\PhpCmis\DataObjects\ExtensionFeature;
use Dkd\PhpCmis\DataObjects\NewTypeSettableAttributes;
use Dkd\PhpCmis\DataObjects\ObjectData;
use Dkd\PhpCmis\DataObjects\ObjectInFolderContainer;
use Dkd\PhpCmis\DataObjects\ObjectInFolderData;
use Dkd\PhpCmis\DataObjects\ObjectInFolderList;
use Dkd\PhpCmis\DataObjects\ObjectList;
use Dkd\PhpCmis\DataObjects\ObjectParentData;
use Dkd\PhpCmis\DataObjects\PermissionDefinition;
use Dkd\PhpCmis\DataObjects\PermissionMapping;
use Dkd\PhpCmis\DataObjects\PolicyIdList;
use Dkd\PhpCmis\DataObjects\Principal;
use Dkd\PhpCmis\DataObjects\Properties;
use Dkd\PhpCmis\DataObjects\PropertyBoolean;
use Dkd\PhpCmis\DataObjects\PropertyDateTime;
use Dkd\PhpCmis\DataObjects\PropertyDecimal;
use Dkd\PhpCmis\DataObjects\PropertyHtml;
use Dkd\PhpCmis\DataObjects\PropertyId;
use Dkd\PhpCmis\DataObjects\PropertyInteger;
use Dkd\PhpCmis\DataObjects\PropertyString;
use Dkd\PhpCmis\DataObjects\PropertyUri;
use Dkd\PhpCmis\DataObjects\RenditionData;
use Dkd\PhpCmis\DataObjects\RepositoryCapabilities;
use Dkd\PhpCmis\DataObjects\RepositoryInfoBrowserBinding;
use Dkd\PhpCmis\Enum\AclPropagation;
use Dkd\PhpCmis\Enum\Action;
use Dkd\PhpCmis\Enum\BaseTypeId;
use Dkd\PhpCmis\Enum\CapabilityAcl;
use Dkd\PhpCmis\Enum\CapabilityChanges;
use Dkd\PhpCmis\Enum\CapabilityContentStreamUpdates;
use Dkd\PhpCmis\Enum\CapabilityJoin;
use Dkd\PhpCmis\Enum\CapabilityOrderBy;
use Dkd\PhpCmis\Enum\CapabilityQuery;
use Dkd\PhpCmis\Enum\CapabilityRenditions;
use Dkd\PhpCmis\Enum\ChangeType;
use Dkd\PhpCmis\Enum\CmisVersion;
use Dkd\PhpCmis\Enum\PropertyType;
use Dkd\PhpCmis\Enum\SupportedPermissions;
use Dkd\PhpCmis\Test\Unit\ReflectionHelperTrait;
use GuzzleHttp\Message\MessageFactory;
use PHPUnit_Framework_MockObject_MockObject;

class JsonConverterTest extends \PHPUnit_Framework_TestCase
{
    use ReflectionHelperTrait;

    /**
     * @var JsonConverter
     */
    protected $jsonConverter;

    /**
     * @var CmisExtensionElement[]
     */
    protected $cmisExtensionsDummy;

    public function setUp()
    {
        $this->jsonConverter = new JsonConverter();
        $this->cmisExtensionsDummy = array(new CmisExtensionElement(null, 'myCustomKey', array(), 'myCustomValue'));
    }

    public function testConvertExtensionReturnsEmptyArrayIfGivenDataIsEmpty()
    {
        $this->assertSame(array(), $this->jsonConverter->convertExtension(array()));
    }

    public function testConvertExtensionIgnoresValuesWithKeysThatAreGivenInCmisKeysParameter()
    {
        $this->assertEmpty($this->jsonConverter->convertExtension(array('foo' => 'bar'), array('foo')));
        $this->assertCount(
            1,
            $this->jsonConverter->convertExtension(array('foo' => 'bar', 'bar' => 'foo'), array('foo'))
        );
    }

    /**
     * @depends testConvertExtensionReturnsEmptyArrayIfGivenDataIsEmpty
     * @depends testConvertExtensionIgnoresValuesWithKeysThatAreGivenInCmisKeysParameter
     */
    public function testConvertExtensionConvertsDataToCmisExtensionElementsAndReturnsArrayOfElements()
    {
        $expected = array(
            new CmisExtensionElement(null, 'cmis:foo', array(), 'foo'),
            new CmisExtensionElement(null, 'cmis:bar', array(), 'bar'),
            new CmisExtensionElement(
                null,
                'cmis:baz',
                array(),
                null,
                array(new CmisExtensionElement(null, 'cmis:bazfoo', array(), 'bazfoo'))
            ),
        );

        $result = $this->jsonConverter->convertExtension(
            array('cmis:foo' => 'foo', 'cmis:bar' => 'bar', 'cmis:baz' => array('cmis:bazfoo' => 'bazfoo'))
        );

        $this->assertEquals($expected, $result);
    }

    public function testConvertRepositoryCapabilitiesReturnsNullIfGivenDataIsEmpty()
    {
        $this->assertNull($this->jsonConverter->convertRepositoryCapabilities(array()));
    }

    public function testConvertRepositoryCapabilitiesConvertsArrayToRepositoryCapabilitiesObject()
    {
        $expectedCapabilities = new RepositoryCapabilities();
        $expectedCapabilities->setSupportsUnfiling(true);
        $expectedCapabilities->setSupportsPwcUpdatable(true);
        $expectedCapabilities->setSupportsMultifiling(true);
        $expectedCapabilities->setSupportsGetFolderTree(true);
        $expectedCapabilities->setSupportsGetDescendants(true);
        $expectedCapabilities->setAclCapability(CapabilityAcl::cast(CapabilityAcl::MANAGE));
        $expectedCapabilities->setChangesCapability(CapabilityChanges::cast(CapabilityChanges::PROPERTIES));
        $expectedCapabilities->setContentStreamUpdatesCapability(
            CapabilityContentStreamUpdates::cast(CapabilityContentStreamUpdates::ANYTIME)
        );
        $creatablePropertyTypes = new CreatablePropertyTypes();
        $creatablePropertyTypes->setCanCreate(
            array(
                PropertyType::cast(PropertyType::DATETIME),
                PropertyType::cast(PropertyType::ID),
                PropertyType::cast(PropertyType::HTML)
            )
        );
        $creatablePropertyTypes->setExtensions($this->cmisExtensionsDummy);
        $expectedCapabilities->setCreatablePropertyTypes($creatablePropertyTypes);
        $expectedCapabilities->setQueryCapability(CapabilityQuery::cast(CapabilityQuery::BOTHCOMBINED));

        $newTypeSettableAttributes = new NewTypeSettableAttributes();
        $newTypeSettableAttributes->setCanSetId(true);
        $newTypeSettableAttributes->setExtensions($this->cmisExtensionsDummy);
        $expectedCapabilities->setNewTypeSettableAttributes($newTypeSettableAttributes);

        $expectedCapabilities->setJoinCapability(CapabilityJoin::cast(CapabilityJoin::NONE));
        $expectedCapabilities->setRenditionsCapability(CapabilityRenditions::cast(CapabilityRenditions::NONE));
        $expectedCapabilities->setOrderByCapability(CapabilityOrderBy::cast(CapabilityOrderBy::NONE));

        $expectedCapabilities->setExtensions($this->cmisExtensionsDummy);

        $repositoryInfoArray = current(
            $this->getResponseFixtureContentAsArray('Cmis/v1.1/BrowserBinding/getRepositoryInfo-full-response.log')
        );

        $result = $this->jsonConverter->convertRepositoryCapabilities(
            $repositoryInfoArray[JSONConstants::JSON_REPINFO_CAPABILITIES]
        );

        $this->assertEquals($expectedCapabilities, $result);

        return $result;
    }

    public function testConvertAllowableActionsReturnsNullIfEmptyArrayGiven()
    {
        $this->assertNull($this->jsonConverter->convertAllowableActions(array()));
    }

    public function testConvertAllowableActionsConvertsArrayToAllowableActionsObject()
    {
        $actions = array(
            Action::cast(Action::CAN_GET_CONTENT_STREAM),
            Action::cast(Action::CAN_REMOVE_OBJECT_FROM_FOLDER),
            Action::cast(Action::CAN_MOVE_OBJECT),
            Action::cast(Action::CAN_DELETE_CONTENT_STREAM),
            Action::cast(Action::CAN_GET_PROPERTIES),
            Action::cast(Action::CAN_GET_OBJECT_PARENTS),
            Action::cast(Action::CAN_SET_CONTENT_STREAM),
            Action::cast(Action::CAN_ADD_OBJECT_TO_FOLDER),
            Action::cast(Action::CAN_DELETE_OBJECT),
            Action::cast(Action::CAN_UPDATE_PROPERTIES)
        );
        $allowableActions = new AllowableActions();
        $allowableActions->setAllowableActions($actions);
        $allowableActions->setExtensions($this->cmisExtensionsDummy);

        $getObjectResponse = $this->getResponseFixtureContentAsArray('Cmis/v1.1/BrowserBinding/getObject-response.log');

        $result = $this->jsonConverter->convertAllowableActions(
            $getObjectResponse[JSONConstants::JSON_OBJECT_ALLOWABLE_ACTIONS]
        );

        $this->assertEquals($allowableActions, $result);

        return $result;
    }

    public function testConvertPolicyIdsConvertsArrayToPolicyIdsListObject()
    {
        $expectedPolicyIdsList = new PolicyIdList();
        $expectedPolicyIdsList->setPolicyIds(array('id1', 'id2'));
        $expectedPolicyIdsList->setExtensions($this->cmisExtensionsDummy);

        $getObjectResponse = $this->getResponseFixtureContentAsArray('Cmis/v1.1/BrowserBinding/getObject-response.log');
        $result = $this->jsonConverter->convertPolicyIdList($getObjectResponse[JSONConstants::JSON_OBJECT_POLICY_IDS]);

        $this->assertEquals($expectedPolicyIdsList, $result);

        return $result;
    }

    public function testConvertAclReturnsNullIfEmptyArrayGiven()
    {
        $this->assertNull($this->jsonConverter->convertAcl(array(), true));
    }

    public function testConvertAclConvertsArrayToAclObject()
    {

        $principal = new Principal('anyone');
        $principal->setExtensions($this->cmisExtensionsDummy);
        $ace = new AccessControlEntry($principal, array('cmis:all'));
        $ace->setIsDirect(true);
        $ace->setExtensions($this->cmisExtensionsDummy);
        $acl = new AccessControlList(array($ace));
        $acl->setIsExact(true);
        $acl->setExtensions($this->cmisExtensionsDummy);

        $getObjectResponse = $this->getResponseFixtureContentAsArray('Cmis/v1.1/BrowserBinding/getObject-response.log');
        $result = $this->jsonConverter->convertAcl(
            $getObjectResponse[JSONConstants::JSON_OBJECT_ACL],
            $getObjectResponse[JSONConstants::JSON_OBJECT_EXACT_ACL]
        );

        $this->assertEquals($acl, $result);

        return $result;
    }

    /**
     * @depends testConvertAclConvertsArrayToAclObject
     */
    public function testConvertAclSkipsAclIfPrincipalIdIsEmpty()
    {
        $getObjectResponse = $this->getResponseFixtureContentAsArray('Cmis/v1.1/BrowserBinding/getObject-response.log');
        unset($getObjectResponse[JSONConstants::JSON_OBJECT_ACL]
            [JSONConstants::JSON_ACL_ACES][0][JSONConstants::JSON_ACE_PRINCIPAL][JSONConstants::JSON_ACE_PRINCIPAL_ID]);
        $acl = $this->jsonConverter->convertAcl(
            $getObjectResponse[JSONConstants::JSON_OBJECT_ACL],
            $getObjectResponse[JSONConstants::JSON_OBJECT_EXACT_ACL]
        );
        $this->assertEmpty($acl->getAces());
    }

    public function testConvertRenditionReturnsNullIfEmptyDataArrayGiven()
    {
        $this->assertNull($this->jsonConverter->convertRendition(array()));
    }

    public function testConvertRenditionConvertsArrayToRenditionObject()
    {
        $expectedRendition = new RenditionData();
        $expectedRendition->setStreamId('workspace://SpacesStore/8b49dd01-56bb-4980-b161-1249ac93eb72');
        $expectedRendition->setHeight(100);
        $expectedRendition->setWidth(100);
        $expectedRendition->setLength(539);
        $expectedRendition->setKind('doclib');
        $expectedRendition->setMimeType('image/png');
        $expectedRendition->setRenditionDocumentId('workspace://SpacesStore/8b49dd01-56bb-4980-b161-1249ac93eb73');
        $expectedRendition->setTitle('title');
        $expectedRendition->setExtensions($this->cmisExtensionsDummy);

        $getObjectResponse = $this->getResponseFixtureContentAsArray('Cmis/v1.1/BrowserBinding/getObject-response.log');
        $result = $this->jsonConverter->convertRendition(
            current($getObjectResponse[JSONConstants::JSON_OBJECT_RENDITIONS])
        );

        $this->assertEquals($expectedRendition, $result);
    }

    public function testConvertRenditionsReturnsEmptyArrayIfEmptyDataArrayGiven()
    {
        $this->assertSame(array(), $this->jsonConverter->convertRenditions(array()));
    }

    public function testConvertRenditionsConvertsArrayToRenditionObjects()
    {
        $expectedRendition1 = new RenditionData();
        $expectedRendition1->setStreamId('workspace://SpacesStore/8b49dd01-56bb-4980-b161-1249ac93eb72');
        $expectedRendition1->setHeight(100);
        $expectedRendition1->setWidth(100);
        $expectedRendition1->setLength(539);
        $expectedRendition1->setKind('doclib');
        $expectedRendition1->setMimeType('image/png');
        $expectedRendition1->setRenditionDocumentId('workspace://SpacesStore/8b49dd01-56bb-4980-b161-1249ac93eb73');
        $expectedRendition1->setTitle('title');
        $expectedRendition1->setExtensions($this->cmisExtensionsDummy);

        $expectedRendition2 = new RenditionData();
        $expectedRendition2->setStreamId('workspace://SpacesStore/8b49dd01-56bb-4980-b161-1249ac93eb74');
        $expectedRendition2->setHeight(100);
        $expectedRendition2->setWidth(100);
        $expectedRendition2->setLength(539);
        $expectedRendition2->setKind('doclib');
        $expectedRendition2->setMimeType('image/png');
        $expectedRendition2->setRenditionDocumentId('workspace://SpacesStore/8b49dd01-56bb-4980-b161-1249ac93eb75');
        $expectedRendition2->setTitle('title2');
        $expectedRendition2->setExtensions($this->cmisExtensionsDummy);

        $getObjectResponse = $this->getResponseFixtureContentAsArray('Cmis/v1.1/BrowserBinding/getObject-response.log');
        $result = $this->jsonConverter->convertRenditions(
            $getObjectResponse[JSONConstants::JSON_OBJECT_RENDITIONS]
        );

        $this->assertEquals(array($expectedRendition1, $expectedRendition2), $result);

        return $result;
    }


    public function testConvertAclCapabilitiesReturnsNullIfEmptyArrayGiven()
    {
        $this->assertNull($this->jsonConverter->convertAclCapabilities(array()));
    }

    public function testConvertAclCapabilitiesConvertsArrayToAclCapabilitiesObject()
    {
        $expectedAclCapabilities = new AclCapabilities();
        $expectedAclCapabilities->setAclPropagation(AclPropagation::cast('objectonly'));
        $expectedAclCapabilities->setSupportedPermissions(SupportedPermissions::cast('basic'));
        $permissionMapping = new PermissionMapping();
        $permissionMapping->setKey('canGetDescendents.Folder');
        $permissionMapping->setPermissions(array('cmis:read'));
        $permissionMapping->setExtensions($this->cmisExtensionsDummy);
        $expectedAclCapabilities->setPermissionMapping(array('canGetDescendents.Folder' => $permissionMapping));
        $permissionDefinition = new PermissionDefinition();
        $permissionDefinition->setId('cmis:read');
        $permissionDefinition->setDescription('Read');
        $permissionDefinition->setExtensions($this->cmisExtensionsDummy);
        $expectedAclCapabilities->setPermissions(array($permissionDefinition));
        $expectedAclCapabilities->setExtensions($this->cmisExtensionsDummy);

        $repositoryInfoArray = current(
            $this->getResponseFixtureContentAsArray('Cmis/v1.1/BrowserBinding/getRepositoryInfo-full-response.log')
        );

        $result = $this->jsonConverter->convertAclCapabilities(
            $repositoryInfoArray[JSONConstants::JSON_REPINFO_ACL_CAPABILITIES]
        );

        $this->assertEquals($expectedAclCapabilities, $result);

        return $result;
    }

    public function testConvertPropertiesReturnsNullIfEmptyDataGiven()
    {
        $this->assertNull($this->jsonConverter->convertProperties(array()));
        $this->assertNull($this->jsonConverter->convertProperties(null));
    }

    public function testConvertPropertiesThrowsExceptionIfPropertyWithoutIdAndQueryNameGiven()
    {
        $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisRuntimeException');
        $this->jsonConverter->convertProperties(array('foo' => array()));
    }

    public function testConvertPropertiesThrowsExceptionIfPropertyWithInvalidTypeGiven()
    {
        $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisRuntimeException');
        $this->jsonConverter->convertProperties(array('foo' => array('id' => 'id', 'type' => 'invalidType')));
    }

    /**
     * @covers  Dkd\PhpCmis\Converter\JsonConverter::convertProperties
     * @covers  Dkd\PhpCmis\Converter\JsonConverter::convertStringValues
     * @covers  Dkd\PhpCmis\Converter\JsonConverter::convertBooleanValues
     * @covers  Dkd\PhpCmis\Converter\JsonConverter::convertIntegerValues
     * @covers  Dkd\PhpCmis\Converter\JsonConverter::convertDecimalValues
     * @return Properties
     */
    public function testConvertPropertiesConvertsArrayToPropertiesObject()
    {
        $properties = new Properties();

        $htmlProperty = new PropertyHtml('HtmlProp', 'dummy value');
        $htmlProperty->setLocalName('HtmlProp');
        $htmlProperty->setQueryName('HtmlProp');
        $htmlProperty->setDisplayName('Sample Html Property');
        $properties->addProperty($htmlProperty);

        $idProperty = new PropertyId('IdProp', 'dummy value');
        $idProperty->setLocalName('IdProp');
        $idProperty->setQueryName('IdProp');
        $idProperty->setDisplayName('Sample Id Property');
        $properties->addProperty($idProperty);

        $dateTimeMultiValueProperty = new PropertyDateTime(
            'DateTimePropMV',
            array(
                new \DateTime('@1342160128'),
                new \DateTime('@1342170128')
            )
        );
        $dateTimeMultiValueProperty->setLocalName('DateTimePropMV');
        $dateTimeMultiValueProperty->setQueryName('DateTimePropMV');
        $dateTimeMultiValueProperty->setDisplayName('Sample DateTime multi-value Property');
        $properties->addProperty($dateTimeMultiValueProperty);

        $uriProperty = new PropertyUri('UriProp', 'dummy value');
        $uriProperty->setLocalName('UriProp');
        $uriProperty->setQueryName('UriProp');
        $uriProperty->setDisplayName('Sample Uri Property');
        $properties->addProperty($uriProperty);

        $decimalProperty = new PropertyDecimal('DecimalProp', 1.2);
        $decimalProperty->setLocalName('DecimalProp');
        $decimalProperty->setQueryName('DecimalProp');
        $decimalProperty->setDisplayName('Sample Decimal Property');
        $properties->addProperty($decimalProperty);

        $uriMultiValueProperty = new PropertyUri('UriPropMV', array('dummy value', 'dummy value'));
        $uriMultiValueProperty->setLocalName('UriPropMV');
        $uriMultiValueProperty->setQueryName('UriPropMV');
        $uriMultiValueProperty->setDisplayName('Sample Uri multi-value Property');
        $properties->addProperty($uriMultiValueProperty);

        $booleanProperty = new PropertyBoolean('BooleanProp', true);
        $booleanProperty->setLocalName('BooleanProp');
        $booleanProperty->setQueryName('BooleanProp');
        $booleanProperty->setDisplayName('Sample Boolean Property');
        $properties->addProperty($booleanProperty);

        $idMultiValueProperty = new PropertyId('IdPropMV', array('dummy value', 'dummy value'));
        $idMultiValueProperty->setLocalName('IdPropMV');
        $idMultiValueProperty->setQueryName('IdPropMV');
        $idMultiValueProperty->setDisplayName('Sample Id Html multi-value Property');
        $properties->addProperty($idMultiValueProperty);

        $pickListProperty = new PropertyString('PickListProp', 'blue');
        $pickListProperty->setLocalName('PickListProp');
        $pickListProperty->setQueryName('PickListProp');
        $pickListProperty->setDisplayName('Sample Pick List Property');
        $properties->addProperty($pickListProperty);

        $htmlMultiValueProperty = new PropertyHtml('HtmlPropMV', array('dummy value', 'dummy value'));
        $htmlMultiValueProperty->setLocalName('HtmlPropMV');
        $htmlMultiValueProperty->setQueryName('HtmlPropMV');
        $htmlMultiValueProperty->setDisplayName('Sample Html multi-value Property');
        $properties->addProperty($htmlMultiValueProperty);

        $intProperty = new PropertyInteger('IntProp', 12);
        $intProperty->setLocalName('IntProp');
        $intProperty->setQueryName('IntProp');
        $intProperty->setDisplayName('Sample Int Property');
        $properties->addProperty($intProperty);

        $stringProperty = new PropertyString('StringProp', 'My Doc StringProperty 18');
        $stringProperty->setLocalName('StringProp');
        $stringProperty->setQueryName('StringProp');
        $stringProperty->setDisplayName('Sample String Property');
        $properties->addProperty($stringProperty);

        $decimalMultiValueProperty = new PropertyDecimal('DecimalPropMV', array(1.2, 2.3));
        $decimalMultiValueProperty->setLocalName('DecimalPropMV');
        $decimalMultiValueProperty->setQueryName('DecimalPropMV');
        $decimalMultiValueProperty->setDisplayName('Sample Decimal multi-value Property');
        $properties->addProperty($decimalMultiValueProperty);

        $dateTimeProperty = new PropertyDateTime('DateTimeProp', new \DateTime('@1342160128'));
        $dateTimeProperty->setLocalName('DateTimeProp');
        $dateTimeProperty->setQueryName('DateTimeProp');
        $dateTimeProperty->setDisplayName('Sample DateTime Property');
        $properties->addProperty($dateTimeProperty);

        $booleanMultiValueProperty = new PropertyBoolean('BooleanPropMV', array(true, false));
        $booleanMultiValueProperty->setLocalName('BooleanPropMV');
        $booleanMultiValueProperty->setQueryName('BooleanPropMV');
        $booleanMultiValueProperty->setDisplayName('Sample Boolean multi-value Property');
        $properties->addProperty($booleanMultiValueProperty);

        $intMultiValueProperty = new PropertyInteger('IntPropMV', array(1, 2));
        $intMultiValueProperty->setLocalName('IntPropMV');
        $intMultiValueProperty->setQueryName('IntPropMV');
        $intMultiValueProperty->setDisplayName('Sample Int multi-value Property');
        $properties->addProperty($intMultiValueProperty);

        $properties->setExtensions($this->cmisExtensionsDummy);

        $getObjectResponse = $this->getResponseFixtureContentAsArray('Cmis/v1.1/BrowserBinding/getObject-response.log');
        $result = $this->jsonConverter->convertProperties(
            $getObjectResponse[JSONConstants::JSON_OBJECT_PROPERTIES],
            $getObjectResponse[JSONConstants::JSON_OBJECT_PROPERTIES_EXTENSION]
        );

        $this->assertEquals($properties, $result);

        return $result;
    }

    /**
     * @covers  Dkd\PhpCmis\Converter\JsonConverter::convertObject
     * @depends testConvertAclConvertsArrayToAclObject
     * @depends testConvertAllowableActionsConvertsArrayToAllowableActionsObject
     * @depends testConvertPolicyIdsConvertsArrayToPolicyIdsListObject
     * @depends testConvertPropertiesConvertsArrayToPropertiesObject
     * @depends testConvertRenditionsConvertsArrayToRenditionObjects
     * @param $acl
     * @param $allowableActions
     * @param $policyIds
     * @param $properties
     * @param $renditions
     * @return ObjectData
     */
    public function testConvertObjectConvertsArrayToObjectDataObject(
        $acl,
        $allowableActions,
        $policyIds,
        $properties,
        $renditions
    ) {
        $expectedObject = new ObjectData();
        $expectedObject->setAcl($acl);
        $expectedObject->setAllowableActions($allowableActions);
        $expectedObject->setPolicyIds($policyIds);
        $expectedObject->setRenditions($renditions);
        $changeEvent = new ChangeEventInfo();
        $changeTime = new \DateTime();
        $changeTime->setTimestamp(1342160128);
        $changeEvent->setChangeTime($changeTime);
        $changeEvent->setChangeType(ChangeType::cast(ChangeType::UPDATED));
        $changeEvent->setExtensions($this->cmisExtensionsDummy);
        $expectedObject->setChangeEventInfo($changeEvent);
        $expectedObject->setIsExactAcl(true);
        $expectedObject->setExtensions($this->cmisExtensionsDummy);
        $expectedObject->setProperties($properties);

        $getObjectResponse = $this->getResponseFixtureContentAsArray('Cmis/v1.1/BrowserBinding/getObject-response.log');
        $object = $this->jsonConverter->convertObject($getObjectResponse);

        $this->assertEquals($expectedObject, $object);

        return $object;
    }

    public function testConvertObjectReturnsNullIfEmptyArrayGiven()
    {
        $this->assertNull($this->jsonConverter->convertObject(array()));
    }

    public function testConvertObjectsReturnsEmptyArrayIfEmptyArrayGiven()
    {
        $this->assertCount(0, $this->jsonConverter->convertObjects(array()));
    }

    /**
     * @covers  Dkd\PhpCmis\Converter\JsonConverter::convertObjects
     * @depends testConvertObjectConvertsArrayToObjectDataObject
     * @param $object
     */
    public function testConvertObjectsConvertsArrayToObjectDataArray($object)
    {
        $getObjectResponse = $this->getResponseFixtureContentAsArray('Cmis/v1.1/BrowserBinding/getObject-response.log');

        $result = $this->jsonConverter->convertObjects(array($getObjectResponse, $getObjectResponse, null, array()));

        $this->assertEquals(array($object, $object), $result);
    }

    public function testConvertRepositoryInfoReturnsNullIfGivenDataIsEmpty()
    {
        $this->assertNull($this->jsonConverter->convertRepositoryInfo(array()));
    }

    /**
     * @covers  Dkd\PhpCmis\Converter\JsonConverter::convertRepositoryInfo
     * @covers  Dkd\PhpCmis\Converter\JsonConverter::setRepositoryInfoValues
     * @covers  Dkd\PhpCmis\Converter\JsonConverter::convertExtensionFeatures
     * @depends testConvertRepositoryCapabilitiesConvertsArrayToRepositoryCapabilitiesObject
     * @depends testConvertAclCapabilitiesConvertsArrayToAclCapabilitiesObject
     * @depends testConvertExtensionConvertsDataToCmisExtensionElementsAndReturnsArrayOfElements
     * @param $repositoryCapabilities
     * @param $aclCapabilities
     */
    public function testConvertRepositoryInfoConvertsArrayToRepositoryInfoObject(
        $repositoryCapabilities,
        $aclCapabilities
    ) {
        $repositoryInfoArray = current(
            $this->getResponseFixtureContentAsArray('Cmis/v1.1/BrowserBinding/getRepositoryInfo-full-response.log')
        );

        $this->assertEquals(
            $this->getExpectedRepositoryInfoObjectForFullRequest($repositoryCapabilities, $aclCapabilities),
            $this->jsonConverter->convertRepositoryInfo($repositoryInfoArray)
        );
    }

    public function testConvertDateTimeValueConvertsMicrosecondsTimestampToDatetimeObject()
    {
        $method = $this->getMethod($this->jsonConverter, 'convertDateTimeValue');
        $result = $method->invoke($this->jsonConverter, 1416299867111);

        $expectedDateTimeObject = new \DateTime('2014-11-18 09:37:47');

        $this->assertEquals($expectedDateTimeObject, $result);
    }

    public function testConvertDateTimeValuesConvertsGivenListToDatetimeObjects()
    {
        $method = $this->getMethod($this->jsonConverter, 'convertDateTimeValues');
        $result = $method->invoke($this->jsonConverter, array(1416299867111, null, '2014-11-18 09:37:47'));

        $expectedDateTimeObject = new \DateTime('2014-11-18 09:37:47');

        $this->assertEquals(array($expectedDateTimeObject, $expectedDateTimeObject), $result);
    }

    public function testConvertDateTimeValueThrowsExceptionIfInvalidStringGiven()
    {
        $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisRuntimeException', null, 1416296900);
        $method = $this->getMethod($this->jsonConverter, 'convertDateTimeValue');
        $method->invoke($this->jsonConverter, 'foo');
    }

    public function testConvertDateTimeValueThrowsExceptionIfInvalidValueGiven()
    {
        $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisRuntimeException', null, 1416296901);
        $method = $this->getMethod($this->jsonConverter, 'convertDateTimeValue');
        $method->invoke($this->jsonConverter, array());
    }

    /**
     * @param RepositoryCapabilities $repositoryCapabilities
     * @param AclCapabilities $aclCapabilities
     * @return RepositoryInfoBrowserBinding
     */
    protected function getExpectedRepositoryInfoObjectForFullRequest($repositoryCapabilities, $aclCapabilities)
    {
        $repositoryInfo = new RepositoryInfoBrowserBinding();
        $repositoryInfo->setId('A1');
        $repositoryInfo->setName('Apache Chemistry OpenCMIS InMemory Repository');
        $repositoryInfo->setCmisVersion(CmisVersion::cast(CmisVersion::CMIS_1_1));
        $repositoryInfo->setDescription('Apache Chemistry OpenCMIS InMemory Repository (Version: ?)');
        $repositoryInfo->setVendorName('Apache Chemistry');
        $repositoryInfo->setProductName('OpenCMIS InMemory-Server');
        $repositoryInfo->setProductVersion('?');
        $repositoryInfo->setRootFolderId('100');
        $repositoryInfo->setRepositoryUrl('http://www.example.com:8080/inmemory/browser/A1');

        $repositoryInfo->setCapabilities($repositoryCapabilities);

        $repositoryInfo->setRootUrl('http://www.example.com:8080/inmemory/browser/A1/root');

        $repositoryInfo->setAclCapabilities($aclCapabilities);

        $repositoryInfo->setLatestChangeLogToken('0');
        $repositoryInfo->setCmisVersion(CmisVersion::cast('1.1'));
        $repositoryInfo->setChangesIncomplete(true);
        $repositoryInfo->setChangesOnType(
            array(BaseTypeId::cast(BaseTypeId::CMIS_DOCUMENT))
        );
        $repositoryInfo->setPrincipalIdAnonymous('anonymous');
        $repositoryInfo->setPrincipalIdAnyone('anyone');

        $repositoryInfo->setExtensions($this->cmisExtensionsDummy);

        $extensionFeature = new ExtensionFeature();
        $extensionFeature->setId('E1');
        $extensionFeature->setUrl('http://foo.bar.baz');
        $extensionFeature->setCommonName('commonName');
        $extensionFeature->setVersionLabel('versionLabel');
        $extensionFeature->setDescription('Description');
        $extensionFeature->setFeatureData(array('foo' => 'bar'));
        $extensionFeature->setExtensions($this->cmisExtensionsDummy);
        $repositoryInfo->setExtensionFeatures(array($extensionFeature));

        return $repositoryInfo;
    }

    /**
     * Returns the content of a json fixture as array
     *
     * @param string $fixture the path to the json fixture file
     * @return array|mixed
     */
    protected function getResponseFixtureContentAsArray($fixture)
    {
        $messageFactory = new MessageFactory();
        $fixtureFilename = dirname(dirname(dirname(__FILE__))) . '/Fixtures/' . $fixture;
        if (!file_exists($fixtureFilename)) {
            $this->fail(sprintf('Fixture "%s" not found!', $fixtureFilename));
        }
        $response = $messageFactory->fromMessage(file_get_contents($fixtureFilename));

        $result = array();
        try {
            $result = $response->json();
        } catch (\GuzzleHttp\Exception\ParseException $exception) {
            $this->fail(sprintf('Fixture "%s" does not contain a valid JSON body!', $fixtureFilename));
        }

        return $result;
    }

    public function testConvertObjectInFolderConvertsArrayToObjectInFolderData()
    {
        /** @var  PHPUnit_Framework_MockObject_MockObject|JsonConverter $jsonConverterMock */
        $jsonConverterMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Converter\\JsonConverter')->setMethods(
            array('convertObject')
        )->getMock();

        $response = $this->getResponseFixtureContentAsArray('Cmis/v1.1/BrowserBinding/getChildren-response.log');
        $convertInputData = array_shift($response[JSONConstants::JSON_OBJECTINFOLDERLIST_OBJECTS]);
        $convertObjectData = $convertInputData[JSONConstants::JSON_OBJECTINFOLDER_OBJECT];
        $dummyObjectData = new ObjectData();

        $jsonConverterMock->expects($this->once())->method('convertObject')->with($convertObjectData)->willReturn(
            $dummyObjectData
        );

        $expectedObjectInFolderData = new ObjectInFolderData();
        $expectedObjectInFolderData->setPathSegment('My_Document-1-0');
        $expectedObjectInFolderData->setObject($dummyObjectData);

        $this->assertEquals($expectedObjectInFolderData, $jsonConverterMock->convertObjectInFolder($convertInputData));
    }

    public function testConvertObjectInFolderListConvertsArrayToObjectInFolderList()
    {
        /** @var  PHPUnit_Framework_MockObject_MockObject|JsonConverter $jsonConverterMock */
        $jsonConverterMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Converter\\JsonConverter')->setMethods(
            array('convertObjectInFolder')
        )->getMock();

        $dummyObjectInFolderData = new ObjectInFolderData();
        $expectedNumberOfItems = 5;

        $jsonConverterMock->expects($this->exactly($expectedNumberOfItems))->method(
            'convertObjectInFolder'
        )->willReturn($dummyObjectInFolderData);

        $expectedObjectInFolderList = new ObjectInFolderList();
        $expectedObjectInFolderList->setNumItems($expectedNumberOfItems);
        $expectedObjectInFolderList->setHasMoreItems(false);
        $expectedObjectInFolderList->setObjects(
            array_fill(0, $expectedNumberOfItems, new ObjectInFolderData())
        );

        $response = $this->getResponseFixtureContentAsArray('Cmis/v1.1/BrowserBinding/getChildren-response.log');

        $this->assertEquals($expectedObjectInFolderList, $jsonConverterMock->convertObjectInFolderList($response));
    }

    public function testConvertObjectParentsConvertsArrayToObjectParentDataArray()
    {
        /** @var  PHPUnit_Framework_MockObject_MockObject|JsonConverter $jsonConverterMock */
        $jsonConverterMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Converter\\JsonConverter')->setMethods(
            array('convertObjectParentData')
        )->getMock();

        $dummyObjectParentData = new ObjectParentData();
        $numberOfObjectsInResponse = 2;

        $jsonConverterMock->expects($this->exactly($numberOfObjectsInResponse))->method(
            'convertObjectParentData'
        )->willReturn($dummyObjectParentData);

        $expectedObjectParentDataArray = array_fill(0, $numberOfObjectsInResponse, new ObjectParentData());

        $response = $this->getResponseFixtureContentAsArray('Cmis/v1.1/BrowserBinding/getObjectParents-response.log');

        $this->assertEquals($expectedObjectParentDataArray, $jsonConverterMock->convertObjectParents($response));
    }

    public function testConvertObjectParentDataConvertsArrayToObjectParentData()
    {
        /** @var  PHPUnit_Framework_MockObject_MockObject|JsonConverter $jsonConverterMock */
        $jsonConverterMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Converter\\JsonConverter')->setMethods(
            array('convertObject')
        )->getMock();

        $response = $this->getResponseFixtureContentAsArray('Cmis/v1.1/BrowserBinding/getObjectParents-response.log');
        $convertInputData = array_shift($response);
        $convertObjectData = $convertInputData[JSONConstants::JSON_OBJECTPARENTS_OBJECT];
        $dummyObjectData = new ObjectData();

        $jsonConverterMock->expects($this->once())->method('convertObject')->with($convertObjectData)->willReturn(
            $dummyObjectData
        );

        $expectedObjectParentData = new ObjectParentData();
        $expectedObjectParentData->setRelativePathSegment('MultifiledDocument');
        $expectedObjectParentData->setObject($dummyObjectData);

        $this->assertEquals($expectedObjectParentData, $jsonConverterMock->convertObjectParentData($convertInputData));
    }

    public function testConvertObjectListConvertsArrayToObjectList()
    {
        /** @var  PHPUnit_Framework_MockObject_MockObject|JsonConverter $jsonConverterMock */
        $jsonConverterMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Converter\\JsonConverter')->setMethods(
            array('convertObject')
        )->getMock();

        $response = $this->getResponseFixtureContentAsArray('Cmis/v1.1/BrowserBinding/getContentChanges-response.log');

        $dummyObjectData = new ObjectData();
        $expectedNumberOfItems = 39;

        $jsonConverterMock->expects($this->exactly($expectedNumberOfItems))->method('convertObject')->willReturn(
            $dummyObjectData
        );

        $expectedObjectList = new ObjectList();
        $expectedObjectList->setObjects(array_fill(0, $expectedNumberOfItems, new ObjectData()));
        $expectedObjectList->setNumItems($expectedNumberOfItems);
        $expectedObjectList->hasMoreItems(false);

        $this->assertEquals($expectedObjectList, $jsonConverterMock->convertObjectList($response));
    }

    public function testConvertQueryResultListConvertsArrayToObjectList()
    {
        /** @var  PHPUnit_Framework_MockObject_MockObject|JsonConverter $jsonConverterMock */
        $jsonConverterMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Converter\\JsonConverter')->setMethods(
            array('convertObject')
        )->getMock();

        $response = $this->getResponseFixtureContentAsArray('Cmis/v1.1/BrowserBinding/doQuery-response.log');

        $dummyObjectData = new ObjectData();
        $expectedNumberOfItems = 4;

        $jsonConverterMock->expects($this->exactly($expectedNumberOfItems))->method('convertObject')->willReturn(
            $dummyObjectData
        );

        $expectedObjectList = new ObjectList();
        $expectedObjectList->setObjects(array_fill(0, $expectedNumberOfItems, new ObjectData()));
        $expectedObjectList->setNumItems($expectedNumberOfItems);
        $expectedObjectList->hasMoreItems(false);

        $this->assertEquals($expectedObjectList, $jsonConverterMock->convertQueryResultList($response));
    }

    public function testConvertDescendantsConvertsArrayToObjectInFolderContainerArray()
    {
        /** @var  PHPUnit_Framework_MockObject_MockObject|JsonConverter $jsonConverterMock */
        $jsonConverterMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Converter\\JsonConverter')->setMethods(
            array('convertDescendant')
        )->getMock();

        $response = $this->getResponseFixtureContentAsArray('Cmis/v1.1/BrowserBinding/getDescendants-response.log');

        $dummyObjectInFolderContainer = new ObjectInFolderContainer(new ObjectInFolderData());

        $numberOfObjectsInResponse = 5;
        $jsonConverterMock->expects($this->exactly($numberOfObjectsInResponse))->method(
            'convertDescendant'
        )->willReturn($dummyObjectInFolderContainer);

        $expectedDescendantsArray = array_fill(
            0,
            $numberOfObjectsInResponse,
            new ObjectInFolderContainer(
                new ObjectInFolderData()
            )
        );

        $this->assertEquals($expectedDescendantsArray, $jsonConverterMock->convertDescendants($response));
    }

    public function testConvertDescendantConvertsArrayToObjectInFolderContainer()
    {
        /** @var  PHPUnit_Framework_MockObject_MockObject|JsonConverter $jsonConverterMock */
        $jsonConverterMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\Converter\\JsonConverter')->setMethods(
            array('convertObject')
        )->getMock();

        $response = $this->getResponseFixtureContentAsArray('Cmis/v1.1/BrowserBinding/getDescendants-response.log');

        // example with children to test recursion
        $convertInputData = $response[3];

        $dummyObjectData = new ObjectData();

        $jsonConverterMock->expects($this->any())->method('convertObject')->willReturn(
            $dummyObjectData
        );

        $expectedObjectInFolderContainer = new ObjectInFolderContainer(new ObjectInFolderData());

        $objectInFolderData = new ObjectInFolderData();
        $objectInFolderData->setObject($dummyObjectData);
        $objectInFolderData->setPathSegment('My_Folder-1-0');
        $expectedObjectInFolderContainer->setObject($objectInFolderData);

        $dummyObjectInFolderData1 = new ObjectInFolderData();
        $dummyObjectInFolderData1->setObject($dummyObjectData);
        $dummyObjectInFolderData1->setPathSegment('My_Document-2-0');
        $dummyObjectInFolderContainer1 = new ObjectInFolderContainer(new ObjectInFolderData());
        $dummyObjectInFolderContainer1->setObject($dummyObjectInFolderData1);
        $children[] = $dummyObjectInFolderContainer1;

        $dummyObjectInFolderData2 = new ObjectInFolderData();
        $dummyObjectInFolderData2->setObject($dummyObjectData);
        $dummyObjectInFolderData2->setPathSegment('My_Document-2-1');
        $dummyObjectInFolderContainer2 = new ObjectInFolderContainer(new ObjectInFolderData());
        $dummyObjectInFolderContainer2->setObject($dummyObjectInFolderData2);
        $children[] = $dummyObjectInFolderContainer2;

        $dummyObjectInFolderData3 = new ObjectInFolderData();
        $dummyObjectInFolderData3->setObject($dummyObjectData);
        $dummyObjectInFolderData3->setPathSegment('My_Document-2-2');
        $dummyObjectInFolderContainer3 = new ObjectInFolderContainer(new ObjectInFolderData());
        $dummyObjectInFolderContainer3->setObject($dummyObjectInFolderData3);
        $children[] = $dummyObjectInFolderContainer3;

        $dummyObjectInFolderData4 = new ObjectInFolderData();
        $dummyObjectInFolderData4->setObject($dummyObjectData);
        $dummyObjectInFolderData4->setPathSegment('My_Folder-2-0');
        $dummyObjectInFolderContainer4 = new ObjectInFolderContainer(new ObjectInFolderData());
        $dummyObjectInFolderContainer4->setObject($dummyObjectInFolderData4);
        $children[] = $dummyObjectInFolderContainer4;

        $dummyObjectInFolderData5 = new ObjectInFolderData();
        $dummyObjectInFolderData5->setObject($dummyObjectData);
        $dummyObjectInFolderData5->setPathSegment('My_Folder-2-1');
        $dummyObjectInFolderContainer5 = new ObjectInFolderContainer(new ObjectInFolderData());
        $dummyObjectInFolderContainer5->setObject($dummyObjectInFolderData5);
        $children[] = $dummyObjectInFolderContainer5;

        $expectedObjectInFolderContainer->setChildren($children);

        $this->assertEquals($expectedObjectInFolderContainer, $jsonConverterMock->convertDescendant($convertInputData));
    }
}
