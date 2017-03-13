<?php
namespace Dkd\PhpCmis\Test\Unit\Bindings\Browser;

use Dkd\PhpCmis\Bindings\Browser\CmisBrowserBinding;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class CmisBrowserBindingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CmisBrowserBinding
     */
    protected $cmisBrowserBinding;

    public function setUp()
    {
        $sessionMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Bindings\\BindingSessionInterface'
        )->getMockForAbstractClass();
        $this->cmisBrowserBinding = new CmisBrowserBinding($sessionMock);
    }

    public function testConstructorSetsSessionAsSessionProperty()
    {
        $sessionMock = $this->getMockBuilder(
            '\\Dkd\\PhpCmis\\Bindings\\BindingSessionInterface'
        )->getMockForAbstractClass();
        $cmisBrowserBinding = new CmisBrowserBinding($sessionMock);
        $this->assertAttributeSame($sessionMock, 'session', $cmisBrowserBinding);
    }

    /**
     * @dataProvider servicesDataProvider
     * @param $expectedInstance
     * @param $propertyName
     */
    public function testConstructorInitializesServiceProperties($expectedInstance, $propertyName)
    {
        $this->assertAttributeInstanceOf($expectedInstance, $propertyName, $this->cmisBrowserBinding);
    }

    /**
     * @dataProvider servicesDataProvider
     * @param $expectedInstance
     * @param $propertyName
     */
    public function testServiceGetterReturnsServiceInstance($expectedInstance, $propertyName)
    {
        $getterName = 'get' . ucfirst($propertyName);
        $this->assertInstanceOf($expectedInstance, $this->cmisBrowserBinding->$getterName());
    }

    public function servicesDataProvider()
    {
        return [
            [
                '\\Dkd\\PhpCmis\\AclServiceInterface',
                'aclService'
            ],
            [
                '\\Dkd\\PhpCmis\\DiscoveryServiceInterface',
                'discoveryService'
            ],
            [
                '\\Dkd\\PhpCmis\\MultiFilingServiceInterface',
                'multiFilingService'
            ],
            [
                '\\Dkd\\PhpCmis\\NavigationServiceInterface',
                'navigationService'
            ],
            [
                '\\Dkd\\PhpCmis\\ObjectServiceInterface',
                'objectService'
            ],
            [
                '\\Dkd\\PhpCmis\\PolicyServiceInterface',
                'policyService'
            ],
            [
                '\\Dkd\\PhpCmis\\RelationshipServiceInterface',
                'relationshipService'
            ],
            [
                '\\Dkd\\PhpCmis\\RepositoryServiceInterface',
                'repositoryService'
            ],
            [
                '\\Dkd\\PhpCmis\\VersioningServiceInterface',
                'versioningService'
            ]
        ];
    }
}
