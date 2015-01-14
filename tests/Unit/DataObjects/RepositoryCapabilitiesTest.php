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

use Dkd\PhpCmis\DataObjects\CreatablePropertyTypes;
use Dkd\PhpCmis\DataObjects\NewTypeSettableAttributes;
use Dkd\PhpCmis\DataObjects\RepositoryCapabilities;
use Dkd\PhpCmis\Enum\CapabilityAcl;
use Dkd\PhpCmis\Enum\CapabilityChanges;
use Dkd\PhpCmis\Enum\CapabilityContentStreamUpdates;
use Dkd\PhpCmis\Enum\CapabilityJoin;
use Dkd\PhpCmis\Enum\CapabilityOrderBy;
use Dkd\PhpCmis\Enum\CapabilityQuery;
use Dkd\PhpCmis\Enum\CapabilityRenditions;
use Dkd\PhpCmis\Test\Unit\DataProviderCollectionTrait;

class RepositoryCapabilitiesTest extends \PHPUnit_Framework_TestCase
{
    use DataProviderCollectionTrait;

    /**
     * @var RepositoryCapabilities
     */
    protected $repositoryCapabilities;

    public function setUp()
    {
        $this->repositoryCapabilities = new RepositoryCapabilities();
    }

    public function testSetAclCapabilitySetsProperty()
    {
        $aclCapability = CapabilityAcl::cast(CapabilityAcl::DISCOVER);
        $this->repositoryCapabilities->setAclCapability($aclCapability);
        $this->assertAttributeSame($aclCapability, 'aclCapability', $this->repositoryCapabilities);
    }

    /**
     * @depends testSetAclCapabilitySetsProperty
     */
    public function testGetAclCapabilityReturnsPropertyValue()
    {
        $aclCapability = CapabilityAcl::cast(CapabilityAcl::DISCOVER);
        $this->repositoryCapabilities->setAclCapability($aclCapability);
        $this->assertSame($aclCapability, $this->repositoryCapabilities->getAclCapability());
    }

    public function testSetChangesCapabilitySetsProperty()
    {
        $changesCapability = CapabilityChanges::cast(CapabilityChanges::PROPERTIES);
        $this->repositoryCapabilities->setChangesCapability($changesCapability);
        $this->assertAttributeSame($changesCapability, 'changesCapability', $this->repositoryCapabilities);
    }

    /**
     * @depends testSetChangesCapabilitySetsProperty
     */
    public function testGetChangesCapabilityReturnsPropertyValue()
    {
        $changesCapability = CapabilityChanges::cast(CapabilityChanges::PROPERTIES);
        $this->repositoryCapabilities->setChangesCapability($changesCapability);
        $this->assertSame($changesCapability, $this->repositoryCapabilities->getChangesCapability());
    }

    /**
     * @dataProvider booleanCastDataProvider
     * @param boolean $expected
     * @param mixed $value
     */
    public function testSetSupportsAllVersionsSearchableSetsProperty($expected, $value)
    {
        $this->repositoryCapabilities->setSupportsAllVersionsSearchable($value);
        $this->assertAttributeSame($expected, 'supportsAllVersionsSearchable', $this->repositoryCapabilities);
    }

    /**
     * @depends testSetSupportsAllVersionsSearchableSetsProperty
     */
    public function testIsAllVersionsSearchableReturnsPropertyValue()
    {
        $this->repositoryCapabilities->setSupportsAllVersionsSearchable(true);
        $this->assertSame(true, $this->repositoryCapabilities->isAllVersionsSearchableSupported());
    }

    /**
     * @dataProvider booleanCastDataProvider
     * @param boolean $expected
     * @param mixed $value
     */
    public function testSetIsPwcSearchableSetsProperty($expected, $value)
    {
        $this->repositoryCapabilities->setSupportsPwcSearchable($value);
        $this->assertAttributeSame($expected, 'isPwcSearchable', $this->repositoryCapabilities);
    }

    /**
     * @depends testSetIsPwcSearchableSetsProperty
     */
    public function testIsIsPwcSearchableReturnsPropertyValue()
    {
        $this->repositoryCapabilities->setSupportsPwcSearchable(true);
        $this->assertSame(true, $this->repositoryCapabilities->isPwcSearchableSupported());
    }

    /**
     * @dataProvider booleanCastDataProvider
     * @param boolean $expected
     * @param mixed $value
     */
    public function testSetIsPwcUpdatableSetsProperty($expected, $value)
    {
        $this->repositoryCapabilities->setSupportsPwcUpdatable($value);
        $this->assertAttributeSame($expected, 'isPwcUpdatable', $this->repositoryCapabilities);
    }

    /**
     * @depends testSetIsPwcUpdatableSetsProperty
     */
    public function testIsIsPwcUpdatableReturnsPropertyValue()
    {
        $this->repositoryCapabilities->setSupportsPwcUpdatable(true);
        $this->assertSame(true, $this->repositoryCapabilities->isPwcUpdatableSupported());
    }

    public function testSetContentStreamUpdatesCapabilitySetsProperty()
    {
        $ContentStreamUpdatesCapability = CapabilityContentStreamUpdates::cast(CapabilityContentStreamUpdates::PWCONLY);
        $this->repositoryCapabilities->setContentStreamUpdatesCapability($ContentStreamUpdatesCapability);
        $this->assertAttributeSame(
            $ContentStreamUpdatesCapability,
            'contentStreamUpdatesCapability',
            $this->repositoryCapabilities
        );
    }

    /**
     * @depends testSetContentStreamUpdatesCapabilitySetsProperty
     */
    public function testGetContentStreamUpdatesCapabilityReturnsPropertyValue()
    {
        $ContentStreamUpdatesCapability = CapabilityContentStreamUpdates::cast(CapabilityContentStreamUpdates::PWCONLY);
        $this->repositoryCapabilities->setContentStreamUpdatesCapability($ContentStreamUpdatesCapability);
        $this->assertSame(
            $ContentStreamUpdatesCapability,
            $this->repositoryCapabilities->getContentStreamUpdatesCapability()
        );
    }

    public function testSetCreatablePropertyTypesSetsProperty()
    {
        $creatablePropertyTypes = new CreatablePropertyTypes();
        $this->repositoryCapabilities->setCreatablePropertyTypes($creatablePropertyTypes);
        $this->assertAttributeSame($creatablePropertyTypes, 'creatablePropertyTypes', $this->repositoryCapabilities);
    }

    /**
     * @depends testSetCreatablePropertyTypesSetsProperty
     */
    public function testGetCreatablePropertyTypesReturnsPropertyValue()
    {
        $creatablePropertyTypes = new CreatablePropertyTypes();
        $this->repositoryCapabilities->setCreatablePropertyTypes($creatablePropertyTypes);
        $this->assertSame($creatablePropertyTypes, $this->repositoryCapabilities->getCreatablePropertyTypes());
    }

    public function testSetJoinCapabilitySetsProperty()
    {
        $joinCapability = CapabilityJoin::cast(CapabilityJoin::INNERANDOUTER);
        $this->repositoryCapabilities->setJoinCapability($joinCapability);
        $this->assertAttributeSame($joinCapability, 'joinCapability', $this->repositoryCapabilities);
    }

    /**
     * @depends testSetJoinCapabilitySetsProperty
     */
    public function testGetJoinCapabilityReturnsPropertyValue()
    {
        $joinCapability = CapabilityJoin::cast(CapabilityJoin::INNERANDOUTER);
        $this->repositoryCapabilities->setJoinCapability($joinCapability);
        $this->assertSame($joinCapability, $this->repositoryCapabilities->getJoinCapability());
    }

    public function testSetNewTypeSettableAttributesSetsProperty()
    {
        $newTypeSettableAttributes = new NewTypeSettableAttributes();
        $this->repositoryCapabilities->setNewTypeSettableAttributes($newTypeSettableAttributes);
        $this->assertAttributeSame(
            $newTypeSettableAttributes,
            'newTypeSettableAttributes',
            $this->repositoryCapabilities
        );
    }

    /**
     * @depends testSetNewTypeSettableAttributesSetsProperty
     */
    public function testGetNewTypeSettableAttributesReturnsPropertyValue()
    {
        $newTypeSettableAttributes = new NewTypeSettableAttributes();
        $this->repositoryCapabilities->setNewTypeSettableAttributes($newTypeSettableAttributes);
        $this->assertSame($newTypeSettableAttributes, $this->repositoryCapabilities->getNewTypeSettableAttributes());
    }

    public function testSetOrderByCapabilitySetsProperty()
    {
        $orderByCapability = CapabilityOrderBy::cast(CapabilityOrderBy::CUSTOM);
        $this->repositoryCapabilities->setOrderByCapability($orderByCapability);
        $this->assertAttributeSame($orderByCapability, 'orderByCapability', $this->repositoryCapabilities);
    }

    /**
     * @depends testSetOrderByCapabilitySetsProperty
     */
    public function testGetOrderByCapabilityReturnsPropertyValue()
    {
        $orderByCapability = CapabilityOrderBy::cast(CapabilityOrderBy::CUSTOM);
        $this->repositoryCapabilities->setOrderByCapability($orderByCapability);
        $this->assertSame($orderByCapability, $this->repositoryCapabilities->getOrderByCapability());
    }

    public function testSetQueryCapabilitySetsProperty()
    {
        $queryCapability = CapabilityQuery::cast(CapabilityQuery::BOTHCOMBINED);
        $this->repositoryCapabilities->setQueryCapability($queryCapability);
        $this->assertAttributeSame($queryCapability, 'queryCapability', $this->repositoryCapabilities);
    }

    /**
     * @depends testSetQueryCapabilitySetsProperty
     */
    public function testGetQueryCapabilityReturnsPropertyValue()
    {
        $queryCapability = CapabilityQuery::cast(CapabilityQuery::BOTHCOMBINED);
        $this->repositoryCapabilities->setQueryCapability($queryCapability);
        $this->assertSame($queryCapability, $this->repositoryCapabilities->getQueryCapability());
    }

    public function testSetRenditionsCapabilitySetsProperty()
    {
        $renditionsCapability = CapabilityRenditions::cast(CapabilityRenditions::NONE);
        $this->repositoryCapabilities->setRenditionsCapability($renditionsCapability);
        $this->assertAttributeSame($renditionsCapability, 'renditionsCapability', $this->repositoryCapabilities);
    }

    /**
     * @depends testSetRenditionsCapabilitySetsProperty
     */
    public function testGetRenditionsCapabilityReturnsPropertyValue()
    {
        $renditionsCapability = CapabilityRenditions::cast(CapabilityRenditions::NONE);
        $this->repositoryCapabilities->setRenditionsCapability($renditionsCapability);
        $this->assertSame($renditionsCapability, $this->repositoryCapabilities->getRenditionsCapability());
    }

    /**
     * @dataProvider booleanCastDataProvider
     * @param boolean $expected
     * @param mixed $value
     */
    public function testSetSupportsGetDescendantsSetsProperty($expected, $value)
    {
        $this->repositoryCapabilities->setSupportsGetDescendants($value);
        $this->assertAttributeSame($expected, 'supportsGetDescendants', $this->repositoryCapabilities);
    }

    /**
     * @depends testSetIsPwcUpdatableSetsProperty
     */
    public function testSetIsGetDescendantsSupportedReturnsPropertyValue()
    {
        $this->repositoryCapabilities->setSupportsGetDescendants(true);
        $this->assertSame(true, $this->repositoryCapabilities->isGetDescendantsSupported());
    }

    /**
     * @dataProvider booleanCastDataProvider
     * @param boolean $expected
     * @param mixed $value
     */
    public function testSetSupportsGetFolderTreeSetsProperty($expected, $value)
    {
        $this->repositoryCapabilities->setSupportsGetFolderTree($value);
        $this->assertAttributeSame($expected, 'supportsGetFolderTree', $this->repositoryCapabilities);
    }

    /**
     * @depends testSetIsPwcUpdatableSetsProperty
     */
    public function testSetIsGetFolderTreeSupportedReturnsPropertyValue()
    {
        $this->repositoryCapabilities->setSupportsGetFolderTree(true);
        $this->assertSame(true, $this->repositoryCapabilities->isGetFolderTreeSupported());
    }

    /**
     * @dataProvider booleanCastDataProvider
     * @param boolean $expected
     * @param mixed $value
     */
    public function testSetSupportsMultifilingSetsProperty($expected, $value)
    {
        $this->repositoryCapabilities->setSupportsMultifiling($value);
        $this->assertAttributeSame($expected, 'supportsMultifiling', $this->repositoryCapabilities);
    }

    /**
     * @depends testSetIsPwcUpdatableSetsProperty
     */
    public function testSetIsMultifilingSupportedReturnsPropertyValue()
    {
        $this->repositoryCapabilities->setSupportsMultifiling(true);
        $this->assertSame(true, $this->repositoryCapabilities->isMultifilingSupported());
    }

    /**
     * @dataProvider booleanCastDataProvider
     * @param boolean $expected
     * @param mixed $value
     */
    public function testSetSupportsUnfilingSetsProperty($expected, $value)
    {
        $this->repositoryCapabilities->setSupportsUnfiling($value);
        $this->assertAttributeSame($expected, 'supportsUnfiling', $this->repositoryCapabilities);
    }

    /**
     * @depends testSetIsPwcUpdatableSetsProperty
     */
    public function testSetIsUnfilingSupportedReturnsPropertyValue()
    {
        $this->repositoryCapabilities->setSupportsUnfiling(true);
        $this->assertSame(true, $this->repositoryCapabilities->isUnfilingSupported());
    }

    /**
     * @dataProvider booleanCastDataProvider
     * @param boolean $expected
     * @param mixed $value
     */
    public function testSetSupportsVersionSpecificFilingSetsProperty($expected, $value)
    {
        $this->repositoryCapabilities->setSupportsVersionSpecificFiling($value);
        $this->assertAttributeSame($expected, 'supportsVersionSpecificFiling', $this->repositoryCapabilities);
    }

    /**
     * @depends testSetIsPwcUpdatableSetsProperty
     */
    public function testSetIsVersionSpecificFilingSupportedReturnsPropertyValue()
    {
        $this->repositoryCapabilities->setSupportsVersionSpecificFiling(true);
        $this->assertSame(true, $this->repositoryCapabilities->isVersionSpecificFilingSupported());
    }
}
