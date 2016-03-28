<?php
namespace Dkd\PhpCmis\Test\Unit\DataObjects;

use Dkd\PhpCmis\DataObjects\Choice;
use Dkd\PhpCmis\Definitions\ChoiceInterface;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class ChoiceTest extends \PHPUnit_Framework_TestCase
{
    const CLASS_TO_TEST = '\\Dkd\\PhpCmis\\DataObjects\\Choice';

    /**
     * @var Choice
     */
    protected $choice;

    public function setUp()
    {
        $this->choice = new Choice();
    }

    public function testSetChoiceSetsPropertyValue()
    {
        /** @var ChoiceInterface $choice */
        $choice = $this->getMockForAbstractClass(self::CLASS_TO_TEST);
        $this->choice->setChoices(array($choice));
        $this->assertAttributeEquals(array($choice), 'choices', $this->choice);
    }

    public function testSetChoiceThrowsExceptionIfChoiceListContainsInvalidValue()
    {
        /** @var ChoiceInterface $choice */
        $choice = $this->getMockForAbstractClass(self::CLASS_TO_TEST);
        $this->setExpectedException('\\Dkd\\PhpCmis\\Exception\\CmisInvalidArgumentException', '', 1413440336);
        $this->choice->setChoices(array($choice, new \stdClass()));
    }

    /**
     * @depends testSetChoiceSetsPropertyValue
     */
    public function testGetChoicesGetsPropertyValue()
    {
        $choice = $this->getMockForAbstractClass(self::CLASS_TO_TEST);
        $this->choice->setChoices(array($choice));

        $this->assertEquals(array($choice), $this->choice->getChoices());
    }

    public function testSetDisplayNameSetsPropertyValue()
    {
        $displayName = 'displayNameValue';
        $this->choice->setDisplayName($displayName);
        $this->assertAttributeSame($displayName, 'displayName', $this->choice);
    }

    /**
     * @depends testSetDisplayNameSetsPropertyValue
     */
    public function testGetDisplayNameGetsPropertyValue()
    {
        $displayName = 'displayNameValue';
        $this->choice->setDisplayName($displayName);
        $this->assertSame($displayName, $this->choice->getDisplayName());
    }

    public function testSetValueSetsPropertyValue()
    {
        $value = array('value', 1, true, new Choice());
        $this->choice->setValue($value);
        $this->assertAttributeSame($value, 'value', $this->choice);
    }

    /**
     * @depends testSetValueSetsPropertyValue
     */
    public function testGetValueGetsPropertyValue()
    {
        $value = array('value', 1, true, new Choice());
        $this->choice->setValue($value);
        $this->assertSame($value, $this->choice->getValue());
    }
}
