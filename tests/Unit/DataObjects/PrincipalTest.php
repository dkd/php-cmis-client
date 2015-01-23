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

use Dkd\PhpCmis\DataObjects\Principal;

class PrincipalTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorSetsId()
    {
        $principal = new Principal('foo');
        $this->assertAttributeSame('foo', 'id', $principal);
    }

    public function testSetPrincipalIdSetsProperty()
    {
        $principal = new Principal('foo');
        $principal->setId('value');
        $this->assertAttributeSame('value', 'id', $principal);
    }

    /**
     * @depends testSetPrincipalIdSetsProperty
     */
    public function testGetPrincipalIdReturnsProperty()
    {
        $principal = new Principal('value');
        $this->assertSame('value', $principal->getId());
    }
}
