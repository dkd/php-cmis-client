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

use Dkd\PhpCmis\Data\ExtensionDataInterface;

/**
 * ACE Principal
 */
interface PrincipalInterface extends ExtensionDataInterface
{
    /**
     * @param string $id
     */
    public function __construct($id);

    /**
     * Returns the principal ID.
     *
     * @return string
     */
    public function getId();

    /**
     * Sets the principal ID
     *
     * @param string $id
     */
    public function setId($id);
}
