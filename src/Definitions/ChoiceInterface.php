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
