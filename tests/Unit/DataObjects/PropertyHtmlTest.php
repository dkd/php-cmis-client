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

use Dkd\PhpCmis\DataObjects\PropertyHtml;
use Dkd\PhpCmis\Test\Unit\DataProviderCollectionTrait;

class PropertyHtmlTest extends PropertyStringTest
{
    use DataProviderCollectionTrait;

    /**
     * @var PropertyHtml
     */
    protected $subjectUnderTest;

    public function setUp()
    {
        $this->subjectUnderTest = new PropertyHtml('testId');
    }
}
