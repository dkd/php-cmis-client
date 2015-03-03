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

use Dkd\Populate\PopulateInterface;

/**
 * Holds extension data either set by the CMIS repository or the client.
 */
interface ExtensionDataInterface extends PopulateInterface
{
    /**
     * Returns the list of top-level extension elements.
     *
     * @return CmisExtensionElementInterface[]
     */
    public function getExtensions();

    /**
     * Sets the list of top-level extension elements.
     *
     * @param CmisExtensionElementInterface[] $extensions
     */
    public function setExtensions(array $extensions);
}
