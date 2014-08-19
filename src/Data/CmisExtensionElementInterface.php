<?php
namespace Dkd\PhpCmis\Data;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Represents one node in the extension tree.
 *
 * An extension element can have a value or children, but not both.
 */
interface CmisExtensionElementInterface extends \Serializable
{
    /**
     * Returns the attributes of the extension.
     *
     * @return array
     */
    public function getAttributes();

    /**
     * Returns the children of this extension.
     *
     * @return CmisExtensionElementInterface
     */
    public function getChildren();

    /**
     * Returns the name of the extension.
     *
     * @return string
     */
    public function getName();

    /**
     * Returns the namespace of the extension.
     *
     * @return string
     */
    public function getNameSpace();

    /**
     * Returns the value of the extension as a String.
     *
     * @return string
     */
    public function getValue();
}
