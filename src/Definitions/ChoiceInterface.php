<?php
namespace Dkd\PhpCmis\Definitions;

/**
 * Choice value interface.
 */
interface ChoiceInterface
{
    /**
     * @return ChoiceInterface[]
     */
    public function getChoice();

    /**
     * Return the display name of the choice value.
     *
     * @return string
     */
    public function getDisplayName();

    /**
     * Return the value of the choice value.
     *
     * @return array
     */
    public function getValue();
}
