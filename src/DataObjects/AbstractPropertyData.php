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

use Dkd\PhpCmis\Data\MutablePropertyDataInterface;

/**
 * Abstract property data implementation.
 */
abstract class AbstractPropertyData extends AbstractExtensionData implements MutablePropertyDataInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $displayName;

    /**
     * @var string
     */
    protected $localName;

    /**
     * @var string
     */
    protected $queryName;

    /**
     * @var array
     */
    protected $values = array();

    /**
     * @param string $id
     * @param mixed $value
     */
    public function __construct($id, $value = null)
    {
        $this->setId($id);

        if (is_array($value)) {
            $this->setValues($value);
        } elseif ($value !== null) {
            $this->setValue($value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * {@inheritdoc}
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = (string) $displayName;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = (string) $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocalName()
    {
        return $this->localName;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocalName($localName)
    {
        $this->localName = (string) $localName;
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryName()
    {
        return $this->queryName;
    }

    /**
     * {@inheritdoc}
     */
    public function setQueryName($queryName)
    {
        $this->queryName = (string) $queryName;
    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * {@inheritdoc}
     */
    public function setValues(array $values)
    {
        $this->values = array();
        if (is_array($values)) {
            $this->values = array_values($values);
        }
    }

    /**
     * {@inheritdoc}
     */
    final public function setValue($value)
    {
        $this->setValues(array($value));
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstValue()
    {
        if (count($this->values) > 0) {
            return $this->values[0];
        }

        return null;
    }
}
