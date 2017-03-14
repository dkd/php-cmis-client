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

use Dkd\PhpCmis\DataObjects\RepositoryInfoBrowserBinding;

/**
 * Class RepositoryInfoBrowserBindingTest
 */
class RepositoryInfoBrowserBindingTest extends RepositoryInfoTest
{
    public function setUp()
    {
        $this->repositoryInfo = new RepositoryInfoBrowserBinding();
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
                'propertyName' => 'repositoryUrl',
                'validValue' => 'exampleString',
                'invalidValue' => 123
            ],
            [
                'propertyName' => 'rootUrl',
                'validValue' => 'exampleString',
                'invalidValue' => 123
            ]
        ];
    }
}
