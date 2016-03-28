<?php
namespace Dkd\PhpCmis\DataObjects;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Definitions\ChoiceInterface;
use Dkd\PhpCmis\Traits\TypeHelperTrait;

/**
 * Choice implementation.
 */
class Choice implements ChoiceInterface
{
    use TypeHelperTrait;

    /**
     * @var string
     */
    protected $displayName = '';

    /**
     * @var ChoiceInterface[]|string[]|integer[]|boolean[]|float[]|\DateTime[]
     */
    protected $value = array();

    /**
     * @var ChoiceInterface[]
     */
    protected $choices = array();

    /**
     * @return ChoiceInterface[]
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * @param ChoiceInterface[] $choices
     */
    public function setChoices(array $choices)
    {
        foreach ($choices as $value) {
            $this->checkType('\\Dkd\\PhpCmis\\Definitions\\ChoiceInterface', $value);
        }

        $this->choices = $choices;
    }

    /**
     * Return the display name of the choice value.
     *
     * @return string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Sets the display name of the choice value.
     *
     * @return string $displayName
     */
    public function setDisplayName($displayName)
    {
        $this->checkType('string', $displayName);
        $this->displayName = $displayName;
    }

    /**
     * Return the value of the choice value.
     *
     * @return ChoiceInterface[]|string[]|integer[]|boolean[]|float[]|\DateTime[]
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Sets the value of the choice value.
     *
     * @param ChoiceInterface[]|string[]|integer[]|boolean[]|float[]|\DateTime[] $value
     */
    public function setValue(array $value)
    {
        $this->value = $value;
    }
}
