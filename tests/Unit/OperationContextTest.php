<?php
namespace Dkd\PhpCmis\Test\Unit;

use Dkd\PhpCmis\Constants;
use Dkd\PhpCmis\Enum\IncludeRelationships;
use Dkd\PhpCmis\OperationContext;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class OperationContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OperationContext
     */
    protected $operationContext;

    public function setUp()
    {
        $this->operationContext = new OperationContext();
    }

    public function testConstructorCallsSetRenditionFilterToInitalizeIt()
    {
        $operationContextMock = $this->getMockBuilder('\\Dkd\\PhpCmis\\OperationContext')->disableOriginalConstructor(
        )->getMock();
        $operationContextMock->expects($this->once())->method('setRenditionFilter')->with(array());

        // now call the constructor
        $reflectedClass = new \ReflectionClass(get_class($operationContextMock));
        $constructor = $reflectedClass->getConstructor();
        $constructor->invoke($operationContextMock);
    }

    public function testConstructorInitializesIncludeRelationshipsAttribute()
    {
        $this->assertAttributeEquals(
            IncludeRelationships::cast(IncludeRelationships::NONE),
            'includeRelationships',
            $this->operationContext
        );
    }

    public function testSetIncludeAclsSetsProperty()
    {
        $this->operationContext->setIncludeAcls(true);
        $this->assertAttributeSame(true, 'includeAcls', $this->operationContext);
        $this->operationContext->setIncludeAcls(false);
        $this->assertAttributeSame(false, 'includeAcls', $this->operationContext);
    }

    public function testSetIncludeAllowableActionsSetsProperty()
    {
        $this->operationContext->setIncludeAllowableActions(true);
        $this->assertAttributeSame(true, 'includeAllowableActions', $this->operationContext);
        $this->operationContext->setIncludeAllowableActions(false);
        $this->assertAttributeSame(false, 'includeAllowableActions', $this->operationContext);
    }

    public function testSetIncludePathSegmentsSetsProperty()
    {
        $this->operationContext->setIncludePathSegments(true);
        $this->assertAttributeSame(true, 'includePathSegments', $this->operationContext);
        $this->operationContext->setIncludePathSegments(false);
        $this->assertAttributeSame(false, 'includePathSegments', $this->operationContext);
    }

    public function testSetIncludePoliciesSetsProperty()
    {
        $this->operationContext->setIncludePolicies(true);
        $this->assertAttributeSame(true, 'includePolicies', $this->operationContext);
        $this->operationContext->setIncludePolicies(false);
        $this->assertAttributeSame(false, 'includePolicies', $this->operationContext);
    }

    public function testSetFilterSetsProperty()
    {
        $this->operationContext->setFilter(array('foo'));
        $this->assertAttributeSame(array('foo'), 'filter', $this->operationContext);
        $this->operationContext->setFilter(array('baz', 'bar'));
        $this->assertAttributeSame(array('baz', 'bar'), 'filter', $this->operationContext);
    }

    public function testSetFilterSetsPropertyAndIgnoresEmptyFilterValues()
    {
        $this->operationContext->setFilter(array('foo', '', 0, 'bar'));
        $this->assertAttributeSame(array('foo', '0', 'bar'), 'filter', $this->operationContext);
    }

    public function testSetFilterThrowsExceptionIfFilterContainsComma()
    {
        $this->setExpectedException('\\InvalidArgumentException', 'Filter must not contain a comma!');
        $this->operationContext->setFilter(array('foo', 'bar,baz'));
    }

    public function testSetRenditionFilterSetsProperty()
    {
        $this->operationContext->setRenditionFilter(array('foo'));
        $this->assertAttributeSame(array('foo'), 'renditionFilter', $this->operationContext);
        $this->operationContext->setRenditionFilter(array('baz', 'bar'));
        $this->assertAttributeSame(array('baz', 'bar'), 'renditionFilter', $this->operationContext);
    }

    public function testSetRenditionFilterThrowsExceptionIfFilterContainsComma()
    {
        $this->setExpectedException('\\InvalidArgumentException', 'Rendition must not contain a comma!');
        $this->operationContext->setRenditionFilter(array('foo', 'bar,baz'));
    }

    public function testSetRenditionFilterIgnoresEmptyFilters()
    {
        $this->operationContext->setRenditionFilter(array('', 0, 'foo'));
        $this->assertAttributeSame(array('0' ,'foo'), 'renditionFilter', $this->operationContext);
    }

    public function testSetRenditionFilterSetsRenditionNoneIfEmptyListOfRenditionsGiven()
    {
        $this->operationContext->setRenditionFilter(array());
        $this->assertAttributeSame(array(Constants::RENDITION_NONE), 'renditionFilter', $this->operationContext);
    }

    public function testSetLoadSecondaryTypePropertiesSetsProperty()
    {
        $this->operationContext->setLoadSecondaryTypeProperties(false);
        $this->assertAttributeSame(false, 'loadSecondaryTypeProperties', $this->operationContext);
        $this->operationContext->setLoadSecondaryTypeProperties(true);
        $this->assertAttributeSame(true, 'loadSecondaryTypeProperties', $this->operationContext);
    }

    public function testSetMaxItemsPerPageSetsProperty()
    {
        $this->operationContext->setMaxItemsPerPage(10);
        $this->assertAttributeSame(10, 'maxItemsPerPage', $this->operationContext);
        $this->operationContext->setMaxItemsPerPage(20);
        $this->assertAttributeSame(20, 'maxItemsPerPage', $this->operationContext);
    }

    public function testSetMaxItemsPerPageThrowsExceptionIfInvalidValueIsGiven()
    {
        $this->setExpectedException('\\InvalidArgumentException');
        $this->operationContext->setMaxItemsPerPage(0);
    }

    public function testSetOrderBySetsProperty()
    {
        $this->operationContext->setOrderBy('foo ASC, Bar desc');
        $this->assertAttributeSame('foo ASC, Bar desc', 'orderBy', $this->operationContext);
    }

    public function testSetIncludeRelationshipsSetsProperty()
    {
        $this->operationContext->setIncludeRelationships(IncludeRelationships::cast(IncludeRelationships::BOTH));
        $this->assertAttributeEquals(
            IncludeRelationships::cast(IncludeRelationships::BOTH),
            'includeRelationships',
            $this->operationContext
        );
        $this->operationContext->setIncludeRelationships(IncludeRelationships::cast(IncludeRelationships::NONE));
        $this->assertAttributeEquals(
            IncludeRelationships::cast(IncludeRelationships::NONE),
            'includeRelationships',
            $this->operationContext
        );
    }

    public function testSetFilterStringExplodesStringByCommaAndSetsResultAsFilterProperty()
    {
        $this->operationContext->setFilterString('');
        $this->assertAttributeSame(array(), 'filter', $this->operationContext);
        $this->operationContext->setFilterString('foo,bar');
        $this->assertAttributeSame(array('foo', 'bar'), 'filter', $this->operationContext);
        $this->operationContext->setFilterString('foo,bar,baz');
        $this->assertAttributeSame(array('foo', 'bar', 'baz'), 'filter', $this->operationContext);
    }

    public function testSetRenditionFilterStringExplodesStringByCommaAndSetsResultAsFilterProperty()
    {
        $this->operationContext->setRenditionFilterString('');
        $this->assertAttributeSame(array('cmis:none'), 'renditionFilter', $this->operationContext);
        $this->operationContext->setRenditionFilterString('foo,bar');
        $this->assertAttributeSame(array('foo', 'bar'), 'renditionFilter', $this->operationContext);
        $this->operationContext->setRenditionFilterString('foo,bar,baz');
        $this->assertAttributeSame(array('foo', 'bar', 'baz'), 'renditionFilter', $this->operationContext);
    }

    public function testGetCacheKeyReturnsStringBasedOnPropertyValues()
    {
        $this->assertSame('0101||none|cmis:none', $this->operationContext->getCacheKey());

        $this->operationContext->setIncludeAcls(true)
                               ->setIncludeAllowableActions(false)
                               ->setIncludePolicies(true)
                               ->setIncludePathSegments(false)
                               ->setFilter(array('foo', 'bar'))
                               ->setIncludeRelationships(IncludeRelationships::cast(IncludeRelationships::BOTH))
                               ->setRenditionFilter(array('baz', 'foo'));

        $this->assertSame(
            '1010|foo,bar,cmis:objectId,cmis:baseTypeId,cmis:objectTypeId|both|baz,foo',
            $this->operationContext->getCacheKey()
        );
    }

    public function testSetCacheEnabledSetsProperty()
    {
        $this->assertAttributeSame(false, 'cacheEnabled', $this->operationContext);
        $returnValue = $this->operationContext->setCacheEnabled(true);
        $this->assertAttributeSame(true, 'cacheEnabled', $this->operationContext);
        $this->assertSame($this->operationContext, $returnValue);
    }

    public function testIsCacheEnabledReturnsValueOfProperty()
    {
        $this->assertAttributeSame($this->operationContext->isCacheEnabled(), 'cacheEnabled', $this->operationContext);
    }

    /**
     * @depends testSetFilterSetsProperty
     */
    public function testGetFilterReturnsValueOfProperty()
    {
        $this->operationContext->setFilter(array('foo', 'bar'));
        $this->assertAttributeSame($this->operationContext->getFilter(), 'filter', $this->operationContext);
    }

    public function testIsIncludeAclsReturnsValueOfProperty()
    {
        $this->assertAttributeSame($this->operationContext->isIncludeAcls(), 'includeAcls', $this->operationContext);
    }

    public function testIsIncludeAllowableActionsReturnsValueOfProperty()
    {
        $this->assertAttributeSame(
            $this->operationContext->isIncludeAllowableActions(),
            'includeAllowableActions',
            $this->operationContext
        );
    }

    public function testIsIncludePathSegmentsReturnsValueOfProperty()
    {
        $this->assertAttributeSame(
            $this->operationContext->isIncludePathSegments(),
            'includePathSegments',
            $this->operationContext
        );
    }

    public function testIsIncludePoliciesReturnsValueOfProperty()
    {
        $this->assertAttributeSame(
            $this->operationContext->isIncludePolicies(),
            'includePolicies',
            $this->operationContext
        );
    }

    public function testGetIncludeRelationshipsReturnsValueOfProperty()
    {
        $this->assertAttributeSame(
            $this->operationContext->getIncludeRelationships(),
            'includeRelationships',
            $this->operationContext
        );
    }

    public function testGetMaxItemsPerPageReturnsValueOfProperty()
    {
        $this->assertAttributeSame(
            $this->operationContext->getMaxItemsPerPage(),
            'maxItemsPerPage',
            $this->operationContext
        );
    }

    public function testGetOrderByReturnsValueOfProperty()
    {
        $this->assertAttributeSame(
            $this->operationContext->getOrderBy(),
            'orderBy',
            $this->operationContext
        );
    }

    public function testGetRenditionFilterReturnsValueOfProperty()
    {
        $this->assertAttributeSame(
            $this->operationContext->getRenditionFilter(),
            'renditionFilter',
            $this->operationContext
        );
    }

    public function testGetFilterStringReturnsNullIfNoFilterIsSet()
    {
        $this->assertAttributeSame(array(), 'filter', $this->operationContext);
        $this->assertNull($this->operationContext->getQueryFilterString());
    }

    /**
     * @depends testSetFilterSetsProperty
     */
    public function testGetFilterStringReturnsStarIfFilterContainsAStar()
    {
        $this->operationContext->setFilter(array('foo', OperationContext::PROPERTIES_WILDCARD));
        $this->assertSame(OperationContext::PROPERTIES_WILDCARD, $this->operationContext->getQueryFilterString());
    }

    /**
     * @depends testSetFilterSetsProperty
     * @depends testSetLoadSecondaryTypePropertiesSetsProperty
     */
    public function testGetFilterStringAddsRequiredPropertiesAndReturnsValueOfPropertyAsString()
    {
        $this->operationContext->setFilter(array('foo', 'bar'));
        $this->assertSame(
            'foo,bar,cmis:objectId,cmis:baseTypeId,cmis:objectTypeId',
            $this->operationContext->getQueryFilterString()
        );

        $this->operationContext->setLoadSecondaryTypeProperties(true);
        $this->assertSame(
            'foo,bar,cmis:objectId,cmis:baseTypeId,cmis:objectTypeId,cmis:secondaryObjectTypeIds',
            $this->operationContext->getQueryFilterString()
        );
    }

    public function testGetRenditionFilterStringReturnsNullIfNoFilterIsDefined()
    {
        $operationContext = new \ReflectionClass('\\Dkd\\PhpCmis\\OperationContext');
        $renditionFilterProperty = $operationContext->getProperty('renditionFilter');
        $renditionFilterProperty->setAccessible(true);
        $renditionFilterProperty->setValue($this->operationContext, array());

        $this->assertNull($this->operationContext->getRenditionFilterString());
    }

    /**
     * @depends testSetRenditionFilterSetsProperty
     */
    public function testGetRenditionFilterStringReturnsCommaSeparatedStringOfRenditionFilters()
    {
        $this->operationContext->setRenditionFilter(array('foo', 'bar'));
        $this->assertSame('foo,bar', $this->operationContext->getRenditionFilterString());
    }

    public function testLoadSecondaryTypePropertiesReturnsValueOfProperty()
    {
        $this->assertAttributeSame(
            $this->operationContext->loadSecondaryTypeProperties(),
            'loadSecondaryTypeProperties',
            $this->operationContext
        );
    }
}
