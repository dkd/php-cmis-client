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

use Dkd\PhpCmis\DataObjects\PropertyId;
use Dkd\PhpCmis\Test\Unit\DataProviderCollectionTrait;

class PropertyIdTest extends PropertyStringTest
{
    use DataProviderCollectionTrait;

    /**
     * @var PropertyId
     */
    protected $subjectUnderTest;

    public function setUp()
    {
        $this->subjectUnderTest = new PropertyId('testId');
    }
}
