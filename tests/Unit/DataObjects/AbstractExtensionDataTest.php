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

use Dkd\PhpCmis\DataObjects\AbstractExtensionData;
use PHPUnit_Framework_MockObject_MockObject;
use PHPUnit_Framework_TestCase;

/**
 * Unit Tests for AbstractExtensionData
 */
class AbstractExtensionDataTest extends PHPUnit_Framework_TestCase
{
    public function testSetExtensionsSetsAttributeAndGetExtensionReturnsAttribute()
    {
        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractExtensionData $abstractExtensionData */
        $abstractExtensionData = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\AbstractExtensionData'
        )->setMethods(array('dummy'))->getMockForAbstractClass();

        $extensions = array(
            $this->getMockBuilder(
                '\\Dkd\\PhpCmis\\Data\\CmisExtensionElementInterface'
            )->getMockForAbstractClass()
        );

        $abstractExtensionData->setExtensions($extensions);
        $this->assertAttributeSame($extensions, 'extensions', $abstractExtensionData);
        $this->assertSame($extensions, $abstractExtensionData->getExtensions());
    }

    public function testSetExtensionsWithInvalidDataThrowsException()
    {
        /** @var PHPUnit_Framework_MockObject_MockObject|AbstractExtensionData $abstractExtensionData */
        $abstractExtensionData = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\DataObjects\\AbstractExtensionData'
        )->setMethods(array('dummy'))->getMockForAbstractClass();

        $this->setExpectedException(
            'InvalidArgumentException',
            'A given extension is of type "stdClass" which does not implement required CmisExtensionElementInterface.'
        );
        $abstractExtensionData->setExtensions(array(new \stdClass()));
    }
}
