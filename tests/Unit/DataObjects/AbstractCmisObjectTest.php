<?php
namespace Dkd\PhpCmis\Test\Unit\DataObjects;

/**
 * This file is part of php-cmis-client
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\DataObjects\AbstractCmisObject;
use Dkd\PhpCmis\DataObjects\PropertyDateTimeDefinition;
use Dkd\PhpCmis\DataObjects\PropertyIdDefinition;
use Dkd\PhpCmis\DataObjects\PropertyString;
use Dkd\PhpCmis\DataObjects\PropertyStringDefinition;
use Dkd\PhpCmis\PropertyIds;
use Dkd\PhpCmis\Test\Unit\ReflectionHelperTrait;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

/**
 * Unit Tests for AbstractExtensionData
 */
class AbstractCmisObjectTest extends PHPUnit_Framework_TestCase
{
    use ReflectionHelperTrait;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|AbstractCmisObject
     */
    protected $abstractCmisObject;

    const CLASS_TO_TEST = '\\Dkd\\PhpCmis\\DataObjects\\AbstractCmisObject';

    public function setUp()
    {
        $this->abstractCmisObject = $this->getMockBuilder(self::CLASS_TO_TEST)->enableProxyingToOriginalMethods(
        )->getMockForAbstractClass();
    }

    public function testGetMissingBasePropertiesReturnsAllBaseKeysIfNullGiven()
    {
        $this->assertEquals(
            PropertyIds::getBasePropertyKeys(),
            $this->getMethod(self::CLASS_TO_TEST, 'getMissingBaseProperties')->invokeArgs(
                $this->abstractCmisObject,
                array(null)
            )
        );
    }

    public function testGetMissingBasePropertiesReturnsAllBaseKeysIfEmptyArrayGiven()
    {
        $this->assertEquals(
            PropertyIds::getBasePropertyKeys(),
            $this->getMethod(self::CLASS_TO_TEST, 'getMissingBaseProperties')->invokeArgs(
                $this->abstractCmisObject,
                array(array())
            )
        );
    }

    public function testGetMissingBasePropertiesReturnsEmptyArrayIfAllKeysExist()
    {
        $properties = array(
            new PropertyIdDefinition(PropertyIds::OBJECT_ID),
            new PropertyStringDefinition(PropertyIds::NAME),
            new PropertyString(PropertyIds::CHANGE_TOKEN),
            new PropertyIdDefinition(PropertyIds::OBJECT_TYPE_ID),
            new PropertyIdDefinition(PropertyIds::BASE_TYPE_ID),
            new PropertyStringDefinition(PropertyIds::CREATED_BY),
            new PropertyDateTimeDefinition(PropertyIds::CREATION_DATE),
            new PropertyStringDefinition(PropertyIds::LAST_MODIFIED_BY),
            new PropertyDateTimeDefinition(PropertyIds::LAST_MODIFICATION_DATE),
        );

        $this->assertEmpty(
            $this->getMethod(self::CLASS_TO_TEST, 'getMissingBaseProperties')->invokeArgs(
                $this->abstractCmisObject,
                array($properties)
            )
        );
    }

    public function testGetMissingBasePropertiesReturnsArrayOfMissingKeys()
    {
        $properties = array(
            new PropertyIdDefinition(PropertyIds::OBJECT_ID),
            new PropertyStringDefinition(PropertyIds::NAME),
            new PropertyString(PropertyIds::CHANGE_TOKEN),
        );

        $acceptedKeys = array(
            PropertyIds::OBJECT_TYPE_ID,
            PropertyIds::BASE_TYPE_ID,
            PropertyIds::CREATED_BY,
            PropertyIds::CREATION_DATE,
            PropertyIds::LAST_MODIFIED_BY,
            PropertyIds::LAST_MODIFICATION_DATE,
        );

        $this->assertEquals(
            $acceptedKeys,
            array_values(
                $this->getMethod(self::CLASS_TO_TEST, 'getMissingBaseProperties')->invokeArgs(
                    $this->abstractCmisObject,
                    array($properties)
                )
            )
        );
    }
}
