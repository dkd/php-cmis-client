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
interface CmisExtensionElementInterface
{
    /**
     * Returns the attributes of the extension.
     *
     * The attributes must follow the XML rules for attributes.
     * Don't rely on attributes because the Browser binding does not support attributes!
     *
     * @return array the extension attributes or <code>null</code> if the attributes are not set or not supported by
     *      the binding
     */
    public function getAttributes();

    /**
     * Returns the children of this extension.
     *
     * @return CmisExtensionElementInterface[]
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
     * The namespace must follow the XML rules for namespaces.
     * Don't rely on namespaces because the Browser binding does not support namespaces!
     *
     * @return string the extension namespace or <code>null</code> if the namespace is not set or not supported by
     *      the binding
     */
    public function getNameSpace();

    /**
     * Returns the value of the extension as a String.
     *
     * @return string|null
     */
    public function getValue();
}
