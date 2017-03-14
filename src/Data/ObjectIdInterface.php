<?php
namespace Dkd\PhpCmis\Data;

/*
 * This file is part of php-cmis-client.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Interface ObjectIdInterface
 */
interface ObjectIdInterface
{
    /**
     * Returns the object ID
     *
     * @return string
     */
    public function getId();
}
