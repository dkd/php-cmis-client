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

use Dkd\PhpCmis\DataObjects\NewTypeSettableAttributes;

class NewTypeSettableAttributesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NewTypeSettableAttributes
     */
    protected $newTypeSettableAttributes;

    public function setUp()
    {
        $this->newTypeSettableAttributes = new NewTypeSettableAttributes();
    }

    /**
     * @dataProvider booleanCastDataProvider
     * @param $expected
     * @param $value
     */
    public function testSetIdSetsPropertyAsBoolean($expected, $value)
    {
        $this->newTypeSettableAttributes->setId($value);
        $this->assertAttributeSame($expected, 'id', $this->newTypeSettableAttributes);
    }

    /**
     * @depends testSetIdSetsPropertyAsBoolean
     */
    public function testCanSetIdReturnsPropertyValue()
    {
        $this->newTypeSettableAttributes->setId(true);
        $this->assertSame(true, $this->newTypeSettableAttributes->canSetId());
    }

    /**
     * @dataProvider booleanCastDataProvider
     * @param $expected
     * @param $value
     */
    public function testSetLocalNameSetsPropertyAsBoolean($expected, $value)
    {
        $this->newTypeSettableAttributes->setLocalName($value);
        $this->assertAttributeSame($expected, 'localName', $this->newTypeSettableAttributes);
    }

    /**
     * @depends testSetLocalNameSetsPropertyAsBoolean
     */
    public function testCanSetLocalNameReturnsPropertyValue()
    {
        $this->newTypeSettableAttributes->setLocalName(true);
        $this->assertSame(true, $this->newTypeSettableAttributes->canSetLocalName());
    }

    /**
     * @dataProvider booleanCastDataProvider
     * @param $expected
     * @param $value
     */
    public function testSetLocalNamespaceSetsPropertyAsBoolean($expected, $value)
    {
        $this->newTypeSettableAttributes->setLocalNamespace($value);
        $this->assertAttributeSame($expected, 'localNamespace', $this->newTypeSettableAttributes);
    }

    /**
     * @depends testSetLocalNamespaceSetsPropertyAsBoolean
     */
    public function testCanSetLocalNamespaceReturnsPropertyValue()
    {
        $this->newTypeSettableAttributes->setLocalNamespace(true);
        $this->assertSame(true, $this->newTypeSettableAttributes->canSetLocalNamespace());
    }

    /**
     * @dataProvider booleanCastDataProvider
     * @param $expected
     * @param $value
     */
    public function testSetDisplayNameSetsPropertyAsBoolean($expected, $value)
    {
        $this->newTypeSettableAttributes->setDisplayName($value);
        $this->assertAttributeSame($expected, 'displayName', $this->newTypeSettableAttributes);
    }

    /**
     * @depends testSetDisplayNameSetsPropertyAsBoolean
     */
    public function testCanSetDisplayNameReturnsPropertyValue()
    {
        $this->newTypeSettableAttributes->setDisplayName(true);
        $this->assertSame(true, $this->newTypeSettableAttributes->canSetDisplayName());
    }

    /**
     * @dataProvider booleanCastDataProvider
     * @param $expected
     * @param $value
     */
    public function testSetQueryNameSetsPropertyAsBoolean($expected, $value)
    {
        $this->newTypeSettableAttributes->setQueryName($value);
        $this->assertAttributeSame($expected, 'queryName', $this->newTypeSettableAttributes);
    }

    /**
     * @depends testSetQueryNameSetsPropertyAsBoolean
     */
    public function testCanSetQueryNameReturnsPropertyValue()
    {
        $this->newTypeSettableAttributes->setQueryName(true);
        $this->assertSame(true, $this->newTypeSettableAttributes->canSetQueryName());
    }

    /**
     * @dataProvider booleanCastDataProvider
     * @param $expected
     * @param $value
     */
    public function testSetDescriptionSetsPropertyAsBoolean($expected, $value)
    {
        $this->newTypeSettableAttributes->setDescription($value);
        $this->assertAttributeSame($expected, 'description', $this->newTypeSettableAttributes);
    }

    /**
     * @depends testSetDescriptionSetsPropertyAsBoolean
     */
    public function testCanSetDescriptionReturnsPropertyValue()
    {
        $this->newTypeSettableAttributes->setDescription(true);
        $this->assertSame(true, $this->newTypeSettableAttributes->canSetDescription());
    }

    /**
     * @dataProvider booleanCastDataProvider
     * @param $expected
     * @param $value
     */
    public function testSetCreatableSetsPropertyAsBoolean($expected, $value)
    {
        $this->newTypeSettableAttributes->setCreatable($value);
        $this->assertAttributeSame($expected, 'creatable', $this->newTypeSettableAttributes);
    }

    /**
     * @depends testSetCreatableSetsPropertyAsBoolean
     */
    public function testCanSetCreatableReturnsPropertyValue()
    {
        $this->newTypeSettableAttributes->setCreatable(true);
        $this->assertSame(true, $this->newTypeSettableAttributes->canSetCreatable());
    }

    /**
     * @dataProvider booleanCastDataProvider
     * @param $expected
     * @param $value
     */
    public function testSetFileableSetsPropertyAsBoolean($expected, $value)
    {
        $this->newTypeSettableAttributes->setFileable($value);
        $this->assertAttributeSame($expected, 'fileable', $this->newTypeSettableAttributes);
    }

    /**
     * @depends testSetFileableSetsPropertyAsBoolean
     */
    public function testCanSetFileableReturnsPropertyValue()
    {
        $this->newTypeSettableAttributes->setFileable(true);
        $this->assertSame(true, $this->newTypeSettableAttributes->canSetFileable());
    }

    /**
     * @dataProvider booleanCastDataProvider
     * @param $expected
     * @param $value
     */
    public function testSetQueryableSetsPropertyAsBoolean($expected, $value)
    {
        $this->newTypeSettableAttributes->setQueryable($value);
        $this->assertAttributeSame($expected, 'queryable', $this->newTypeSettableAttributes);
    }

    /**
     * @depends testSetQueryableSetsPropertyAsBoolean
     */
    public function testCanSetQueryableReturnsPropertyValue()
    {
        $this->newTypeSettableAttributes->setQueryable(true);
        $this->assertSame(true, $this->newTypeSettableAttributes->canSetQueryable());
    }

    /**
     * @dataProvider booleanCastDataProvider
     * @param $expected
     * @param $value
     */
    public function testSetFulltextIndexedSetsPropertyAsBoolean($expected, $value)
    {
        $this->newTypeSettableAttributes->setFulltextIndexed($value);
        $this->assertAttributeSame($expected, 'fulltextIndexed', $this->newTypeSettableAttributes);
    }

    /**
     * @depends testSetFulltextIndexedSetsPropertyAsBoolean
     */
    public function testCanSetFulltextIndexedReturnsPropertyValue()
    {
        $this->newTypeSettableAttributes->setFulltextIndexed(true);
        $this->assertSame(true, $this->newTypeSettableAttributes->canSetFulltextIndexed());
    }

    /**
     * @dataProvider booleanCastDataProvider
     * @param $expected
     * @param $value
     */
    public function testSetIncludedInSupertypeQuerySetsPropertyAsBoolean($expected, $value)
    {
        $this->newTypeSettableAttributes->setIncludedInSupertypeQuery($value);
        $this->assertAttributeSame($expected, 'includedInSupertypeQuery', $this->newTypeSettableAttributes);
    }

    /**
     * @depends testSetIncludedInSupertypeQuerySetsPropertyAsBoolean
     */
    public function testCanSetIncludedInSupertypeQueryReturnsPropertyValue()
    {
        $this->newTypeSettableAttributes->setIncludedInSupertypeQuery(true);
        $this->assertSame(true, $this->newTypeSettableAttributes->canSetIncludedInSupertypeQuery());
    }

    /**
     * @dataProvider booleanCastDataProvider
     * @param $expected
     * @param $value
     */
    public function testSetControllablePolicySetsPropertyAsBoolean($expected, $value)
    {
        $this->newTypeSettableAttributes->setControllablePolicy($value);
        $this->assertAttributeSame($expected, 'controllablePolicy', $this->newTypeSettableAttributes);
    }

    /**
     * @depends testSetControllablePolicySetsPropertyAsBoolean
     */
    public function testCanSetControllablePolicyReturnsPropertyValue()
    {
        $this->newTypeSettableAttributes->setControllablePolicy(true);
        $this->assertSame(true, $this->newTypeSettableAttributes->canSetControllablePolicy());
    }

    /**
     * @dataProvider booleanCastDataProvider
     * @param $expected
     * @param $value
     */
    public function testSetControllableACLSetsPropertyAsBoolean($expected, $value)
    {
        $this->newTypeSettableAttributes->setControllableACL($value);
        $this->assertAttributeSame($expected, 'controllableACL', $this->newTypeSettableAttributes);
    }

    /**
     * @depends testSetControllableACLSetsPropertyAsBoolean
     */
    public function testCanSetControllableACLReturnsPropertyValue()
    {
        $this->newTypeSettableAttributes->setControllableACL(true);
        $this->assertSame(true, $this->newTypeSettableAttributes->canSetControllableACL());
    }

    public function booleanCastDataProvider()
    {
        return array(
            array(true, true),
            array(true, 1),
            array(true, '1'),
            array(true, 'string'),
            array(false, false),
            array(false, 0),
            array(false, '0'),
            array(false, null),
        );
    }
}
