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

use Dkd\PhpCmis\DataObjects\PropertyHtmlDefinition;

/**
 * Class PropertyHtmlDefinitionTest
 */
class PropertyHtmlDefinitionTest extends \PHPUnit_Framework_TestCase
{
    public function testAssertIsInstanceOfAbstractPropertyDefinition()
    {
        $this->assertInstanceOf(
            '\\Dkd\\PhpCmis\\DataObjects\\AbstractPropertyDefinition',
            new PropertyHtmlDefinition('testId')
        );
    }
}
