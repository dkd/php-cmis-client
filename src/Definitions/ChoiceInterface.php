<?php
namespace Dkd\PhpCmis\Definitions;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Choice value interface.
 */
interface ChoiceInterface
{
    /**
     * @return ChoiceInterface[]
     */
    public function getChoices();

    /**
     * @param ChoiceInterface[] $choice
     */
    public function setChoices(array $choice);

    /**
     * Return the display name of the choice value.
     *
     * @return string
     */
    public function getDisplayName();

    /**
     * Sets the display name of the choice value.
     *
     * @return string $displayName
     */
    public function setDisplayName($displayName);

    /**
     * Return the value of the choice value.
     *
     * @return ChoiceInterface[]|string[]|integer[]|boolean[]|float[]|\DateTime[]
     */
    public function getValue();

    /**
     * Sets the value of the choice value.
     *
     * @param ChoiceInterface[]|string[]|integer[]|boolean[]|float[]|\DateTime[] $value
     */
    public function setValue(array $value);
}
