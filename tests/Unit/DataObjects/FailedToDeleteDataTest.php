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

use Dkd\PhpCmis\DataObjects\FailedToDeleteData;

class FailedToDeleteDataTest extends \PHPUnit_Framework_TestCase
{
    public function testSetIdsSetsIdsPropertyToGivenValue()
    {
        $ids = array('foo', 'bar');
        $failedToDeleteData = new FailedToDeleteData();
        $failedToDeleteData->setIds($ids);
        $this->assertAttributeSame($ids, 'ids', $failedToDeleteData);

        return $failedToDeleteData;
    }

    /**
     * @depends testSetIdsSetsIdsPropertyToGivenValue
     */
    public function testGetIdsReturnsIdsProperty($failedToDeleteData)
    {
        $this->assertSame(array('foo', 'bar'), $failedToDeleteData->getIds());
    }
}
