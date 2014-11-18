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

use Dkd\PhpCmis\DataObjects\AllowableActions;
use Dkd\PhpCmis\Enum\Action;

class AllowableActionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AllowableActions
     */
    protected $allowableActions;

    public function setUp()
    {
        $this->allowableActions = new AllowableActions();
    }

    public function testSetAllowableActionsThrowsExceptionIfGivenListContainsInvalidValue()
    {
        $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException');
        $this->allowableActions->setAllowableActions(array('foo'));
    }

    public function testSetAllowableActionsAssignsActionsToAttribute()
    {
        $actions = array(Action::cast(Action::CAN_ADD_OBJECT_TO_FOLDER), Action::cast(Action::CAN_APPLY_ACL));
        $this->allowableActions->setAllowableActions($actions);

        $this->assertAttributeSame($actions, 'allowableActions', $this->allowableActions);
    }

    /**
     * @depends testSetAllowableActionsAssignsActionsToAttribute
     */
    public function testGetAllowableActionsReturnsArrayWithActions()
    {
        $actions = array(Action::cast(Action::CAN_ADD_OBJECT_TO_FOLDER), Action::cast(Action::CAN_APPLY_ACL));
        $this->allowableActions->setAllowableActions($actions);

        $this->assertSame($actions, $this->allowableActions->getAllowableActions());
    }
}
