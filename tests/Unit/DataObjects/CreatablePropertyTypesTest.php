<?php
namespace Dkd\PhpCmis\Test\Unit\DataObjects;

/*
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\DataObjects\CreatablePropertyTypes;
use Dkd\PhpCmis\Enum\PropertyType;

/**
 * Class CreatablePropertyTypesTest
 */
class CreatablePropertyTypesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CreatablePropertyTypes
     */
    protected $creatablePropertyTypes;

    public function setUp()
    {
        $this->creatablePropertyTypes = new CreatablePropertyTypes();
    }

    public function testSetCanCreateSetsProperty()
    {
        $types = [PropertyType::cast(PropertyType::DATETIME)];

        $this->creatablePropertyTypes->setCanCreate($types);
        $this->assertAttributeSame($types, 'propertyTypeSet', $this->creatablePropertyTypes);
    }

    /**
     * @dataProvider invalidPropertyTypesDataProvider
     * @param $propertyTypes
     * @param $expectedExceptionText
     */
    public function testSetCanCreateThrowsExceptionIfInvalidAttributeGiven(
        $propertyTypes,
        $expectedExceptionText
    ) {
        $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException', $expectedExceptionText);
        $this->creatablePropertyTypes->setCanCreate([$propertyTypes]);
    }

    public function invalidPropertyTypesDataProvider()
    {
        return [
            [
                'string',
                'Argument of type "string" given but argument of type '
                . '"Dkd\\PhpCmis\\Enum\\PropertyType" was expected.'
            ],
            [
                0,
                'Argument of type "integer" given but argument of type '
                . '"Dkd\\PhpCmis\\Enum\\PropertyType" was expected.'
            ],
            [
                [],
                'Argument of type "array" given but argument of type '
                . '"Dkd\\PhpCmis\\Enum\\PropertyType" was expected.'
            ],
            [
                new \stdClass(),
                'Argument of type "stdClass" given but argument of type '
                . '"Dkd\\PhpCmis\\Enum\\PropertyType" was expected.'
            ]
        ];
    }

    /**
     * @depends testSetCanCreateSetsProperty
     */
    public function testCanCreateReturnsProperty()
    {
        $types = [PropertyType::cast(PropertyType::DATETIME)];
        $this->creatablePropertyTypes->setCanCreate($types);
        $this->assertSame($types, $this->creatablePropertyTypes->canCreate());
    }
}
