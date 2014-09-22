<?php
namespace Dkd\PhpCmis;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

interface ObjectIdInterface
{
    /**
     * @param string $id The Object ID as string
     */
    public function __construct($id);

    /**
     * Returns the object ID
     *
     * @return string
     */
    public function getId();

    /**
     * Returns the object ID as string
     *
     * @return mixed
     */
    public function __toString();
}
