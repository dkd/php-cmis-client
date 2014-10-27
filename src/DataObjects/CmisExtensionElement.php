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
     * @var string
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
     * @param string $value
     * @param CmisExtensionElement[] $children
     * @throws \InvalidArgumentException
     */
    public function __construct($namespace, $name, array $attributes = null, $value = null, array $children = null)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('Name must be given!');
        }

        if (!empty($value) && !empty($children)) {
            throw new \InvalidArgumentException('Value and children given! Only one of them is allowed.');
        }

        if (empty($value) && empty($children)) {
            throw new \InvalidArgumentException('Value and children are empty! One of them is required.');
        }

        $this->name = (string) $name;
        $this->namespace = (string) $namespace;

        if (!empty($value)) {
            $this->value = (string) $value;
            $this->children = array();
        } elseif (!empty($children)) {
            $this->value = null;
            // TODO: We should check here if the array does only contain entries of the expected type.
            // This could be done with AbstractExtensionData::checkType() which is currently not available here.
            // The checkType method could be moved to a trait but this would raise the required php version to 5.4.
            $this->children = $children;
        }

        if (!empty($attributes)) {
            $this->attributes = $attributes;
        }
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
