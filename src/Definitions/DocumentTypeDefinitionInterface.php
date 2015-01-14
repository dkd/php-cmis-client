<?php
namespace Dkd\PhpCmis\Definitions;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Enum\ContentStreamAllowed;

/**
 * Document type definition.
 */
interface DocumentTypeDefinitionInterface extends TypeDefinitionInterface
{
    /**
     * Returns whether objects of this type are versionable or not.
     *
     * @return boolean
     */
    public function isVersionable();

    /**
     * Returns if a content stream is allowed, not allowed or is required.
     *
     * @return ContentStreamAllowed
     */
    public function getContentStreamAllowed();
}
