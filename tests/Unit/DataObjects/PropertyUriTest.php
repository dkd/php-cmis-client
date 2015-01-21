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

use Dkd\PhpCmis\DataObjects\PropertyUri;
use Dkd\PhpCmis\Test\Unit\DataProviderCollectionTrait;

class PropertyUriTest extends PropertyStringTest
{
    use DataProviderCollectionTrait;

    /**
     * @var PropertyUri
     */
    protected $subjectUnderTest;

    public function setUp()
    {
        $this->subjectUnderTest = new PropertyUri('testId');
    }
}
