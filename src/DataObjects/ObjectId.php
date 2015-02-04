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

use Dkd\PhpCmis\Data\ObjectIdInterface;

/**
 * ObjectId
 */
class ObjectId implements ObjectIdInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @param string $id The Object ID as string
     */
    public function __construct($id)
    {
        if (empty($id) || !is_string($id)) {
            throw new \InvalidArgumentException('Id must not be empty!');
        }
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the object ID as string
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getId();
    }
}
