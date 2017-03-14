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

use Dkd\PhpCmis\DataObjects\RepositoryInfo;
use Dkd\PhpCmis\Enum\BaseTypeId;
use Dkd\PhpCmis\Enum\CmisVersion;

/**
 * Class RepositoryInfoTest
 */
class RepositoryInfoTest extends \PHPUnit_Framework_TestCase
{
    const DO_NOT_TEST_INVALID_TYPE_VALUE = 'doNotTestInvalidType';

    /**
     * @var RepositoryInfo
     */
    protected $repositoryInfo;

    public function setUp()
    {
        $this->repositoryInfo = new RepositoryInfo();
    }

    /**
     * DataProvider for all properties with a valid value and an invalid value
     *
     * @return array
     */
    public function propertiesOfSutDataProvider()
    {
        return [
            // string properties
            [
                'propertyName' => 'id',
                'validValue' => 'exampleString',
                'invalidValue' => 123
            ],
            [
                'propertyName' => 'name',
                'validValue' => 'exampleString',
                'invalidValue' => 123
            ],
            [
                'propertyName' => 'description',
                'validValue' => 'exampleString',
                'invalidValue' => 123
            ],
            [
                'propertyName' => 'rootFolderId',
                'validValue' => 'exampleString',
                'invalidValue' => 123
            ],
            [
                'propertyName' => 'principalIdAnonymous',
                'validValue' => 'exampleString',
                'invalidValue' => 123
            ],
            [
                'propertyName' => 'principalIdAnyone',
                'validValue' => 'exampleString',
                'invalidValue' => 123
            ],
            [
                'propertyName' => 'thinClientUri',
                'validValue' => 'exampleString',
                'invalidValue' => 123
            ],
            [
                'propertyName' => 'latestChangeLogToken',
                'validValue' => 'exampleString',
                'invalidValue' => 123
            ],
            [
                'propertyName' => 'vendorName',
                'validValue' => 'exampleString',
                'invalidValue' => 123
            ],
            [
                'propertyName' => 'productName',
                'validValue' => 'exampleString',
                'invalidValue' => 123
            ],
            [
                'propertyName' => 'productVersion',
                'validValue' => 'exampleString',
                'invalidValue' => 123
            ],
            // boolean properties
            [
                'propertyName' => 'changesIncomplete',
                'validValue' => true,
                'invalidValue' => 1
            ],
            // RepositoryCapabilitiesInterface properties
            [
                'propertyName' => 'capabilities',
                'validValue' => $this->getMockForAbstractClass('\\Dkd\\PhpCmis\\Data\\RepositoryCapabilitiesInterface'),
                'invalidValue' => self::DO_NOT_TEST_INVALID_TYPE_VALUE
            ],
            // AclCapabilitiesInterface properties
            [
                'propertyName' => 'aclCapabilities',
                'validValue' => $this->getMockForAbstractClass('\\Dkd\\PhpCmis\\Data\\AclCapabilitiesInterface'),
                'invalidValue' => self::DO_NOT_TEST_INVALID_TYPE_VALUE
            ],
            // CmisVersion properties
            [
                'propertyName' => 'cmisVersion',
                'validValue' => CmisVersion::cast(CmisVersion::CMIS_1_1),
                'invalidValue' => self::DO_NOT_TEST_INVALID_TYPE_VALUE
            ],
            // BaseTypeId[] properties
            [
                'propertyName' => 'changesOnType',
                'validValue' => [BaseTypeId::cast(BaseTypeId::CMIS_DOCUMENT)],
                'invalidValue' => ['foo']
            ],
            // ExtensionFeatureInterface[] properties
            [
                'propertyName' => 'extensionFeatures',
                'validValue' => [
                    $this->getMockForAbstractClass(
                        '\\Dkd\\PhpCmis\\Data\\ExtensionFeatureInterface'
                    )
                ],
                'invalidValue' => [new \stdClass()]
            ]
        ];
    }

    /**
     * Test setter for a property
     *
     * @dataProvider propertiesOfSutDataProvider
     * @param string $propertyName
     * @param mixed $validValue
     */
    public function testPropertySetterSetsProperty($propertyName, $validValue)
    {
        $setterName = 'set' . ucfirst($propertyName);
        $this->repositoryInfo->$setterName($validValue);
        $this->assertAttributeSame($validValue, $propertyName, $this->repositoryInfo);
    }

    /**
     * Test setter for a property - should cast value to expected type
     *
     * @dataProvider propertiesOfSutDataProvider
     * @param string $propertyName
     * @param mixed $validValue
     * @param mixed $invalidValue
     */
    public function testPropertySetterCastsValueToExpectedType($propertyName, $validValue, $invalidValue)
    {
        if ($invalidValue !== self::DO_NOT_TEST_INVALID_TYPE_VALUE) {
            $setterName = 'set' . ucfirst($propertyName);
            $validType = gettype($validValue);
            if ($validType === 'object' || $validType === 'array') {
                $this->setExpectedException(
                    '\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException',
                    '',
                    1413440336
                );
                $this->repositoryInfo->$setterName($invalidValue);
            } else {
                @$this->repositoryInfo->$setterName($invalidValue);
                $this->assertAttributeInternalType($validType, $propertyName, $this->repositoryInfo);
            }
        }
    }

    /**
     * Test getter for a property
     *
     * @dataProvider propertiesOfSutDataProvider
     * @param string $propertyName
     * @param mixed $validValue
     */
    public function testPropertyGetterReturnsPropertyValue($propertyName, $validValue)
    {
        $setterName = 'set' . ucfirst($propertyName);
        $getterName = 'get' . ucfirst($propertyName);
        $this->setDependencies(['testSetPropertySetsProperty']);
        $this->repositoryInfo->$setterName($validValue);
        $this->assertSame($validValue, $this->repositoryInfo->$getterName());
    }
}
