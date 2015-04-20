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

use Dkd\PhpCmis\Data\CmisExtensionElementInterface;

/**
 * Represents one node in the extension tree.
 *
 * An extension element can have a value or children, but not both.
 */
class CmisExtensionElement implements CmisExtensionElementInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var string|null
     */
    protected $value;

    /**
     * @var array
     */
    protected $attributes = array();

    /**
     * @var CmisExtensionElement[]
     */
    protected $children = array();

    /**
     * @param string $namespace
     * @param string $name
     * @param array $attributes
     * @param string|null $value
     * @param CmisExtensionElement[] $children
     * @throws \InvalidArgumentException
     */
    public function __construct(
        $namespace,
        $name,
        array $attributes = array(),
        $value = null,
        array $children = array()
    ) {
        if ($value !== null && count($children) > 0) {
            throw new \InvalidArgumentException('Value and children given! Only one of them is allowed.');
        }

        $name = (string) $name;
        if ($name === '') {
            throw new \InvalidArgumentException('Name must be given!');
        }

        $this->name = $name;
        $this->namespace = (string) $namespace;

        $this->value = ($value === null ? null : (string) $value);

        // TODO: We should check here if the array does only contain entries of the expected type.
        // This could be done with AbstractExtensionData::checkType() which is currently not available here.
        // The checkType method could be moved to a trait but this would raise the required php version to 5.4.
        $this->children = $children;
        $this->attributes = $attributes;
    }

    /**
     * @inheritdoc
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @inheritdoc
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @inheritdoc
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @inheritdoc
     */
    public function getValue()
    {
        return $this->value;
    }
}
