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

use Dkd\PhpCmis\DataObjects\RepositoryInfo;
use Dkd\PhpCmis\Enum\BaseTypeId;
use Dkd\PhpCmis\Enum\CmisVersion;

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
        return array(
            // string properties
            array(
                'propertyName' => 'id',
                'validValue' => 'exampleString',
                'invalidValue' => 123
            ),
            array(
                'propertyName' => 'name',
                'validValue' => 'exampleString',
                'invalidValue' => 123
            ),
            array(
                'propertyName' => 'description',
                'validValue' => 'exampleString',
                'invalidValue' => 123
            ),
            array(
                'propertyName' => 'rootFolderId',
                'validValue' => 'exampleString',
                'invalidValue' => 123
            ),
            array(
                'propertyName' => 'principalIdAnonymous',
                'validValue' => 'exampleString',
                'invalidValue' => 123
            ),
            array(
                'propertyName' => 'principalIdAnyone',
                'validValue' => 'exampleString',
                'invalidValue' => 123
            ),
            array(
                'propertyName' => 'thinClientUri',
                'validValue' => 'exampleString',
                'invalidValue' => 123
            ),
            array(
                'propertyName' => 'latestChangeLogToken',
                'validValue' => 'exampleString',
                'invalidValue' => 123
            ),
            array(
                'propertyName' => 'vendorName',
                'validValue' => 'exampleString',
                'invalidValue' => 123
            ),
            array(
                'propertyName' => 'productName',
                'validValue' => 'exampleString',
                'invalidValue' => 123
            ),
            array(
                'propertyName' => 'productVersion',
                'validValue' => 'exampleString',
                'invalidValue' => 123
            ),
            // boolean properties
            array(
                'propertyName' => 'changesIncomplete',
                'validValue' => true,
                'invalidValue' => 1
            ),
            // RepositoryCapabilitiesInterface properties
            array(
                'propertyName' => 'capabilities',
                'validValue' => $this->getMockForAbstractClass('\\Dkd\\PhpCmis\\Data\\RepositoryCapabilitiesInterface'),
                'invalidValue' => self::DO_NOT_TEST_INVALID_TYPE_VALUE
            ),
            // AclCapabilitiesInterface properties
            array(
                'propertyName' => 'aclCapabilities',
                'validValue' => $this->getMockForAbstractClass('\\Dkd\\PhpCmis\\Data\\AclCapabilitiesInterface'),
                'invalidValue' => self::DO_NOT_TEST_INVALID_TYPE_VALUE
            ),
            // CmisVersion properties
            array(
                'propertyName' => 'cmisVersion',
                'validValue' => CmisVersion::cast(CmisVersion::CMIS_1_1),
                'invalidValue' => self::DO_NOT_TEST_INVALID_TYPE_VALUE
            ),
            // BaseTypeId[] properties
            array(
                'propertyName' => 'changesOnType',
                'validValue' => array(BaseTypeId::cast(BaseTypeId::CMIS_DOCUMENT)),
                'invalidValue' => array('foo')
            ),
            // ExtensionFeatureInterface[] properties
            array(
                'propertyName' => 'extensionFeatures',
                'validValue' => array(
                    $this->getMockForAbstractClass(
                        '\\Dkd\\PhpCmis\\Data\\ExtensionFeatureInterface'
                    )
                ),
                'invalidValue' => array(new \stdClass())
            )
        );
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
        $this->setDependencies(array('testSetPropertySetsProperty'));
        $this->repositoryInfo->$setterName($validValue);
        $this->assertSame($validValue, $this->repositoryInfo->$getterName());
    }
}
