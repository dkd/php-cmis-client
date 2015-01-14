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
 * Mutable Document type definition.
 */
interface MutableDocumentTypeDefinitionInterface extends MutableTypeDefinitionInterface, DocumentTypeDefinitionInterface
{
    /**
     * Sets whether objects of this type are versionable or not.
     *
     * @param boolean $isVersionable
     */
    public function setIsVersionable($isVersionable);

    /**
     * Sets if a content stream is allowed, not allowed or is required.
     *
     * @param ContentStreamAllowed $contentStreamAllowed
     */
    public function setContentStreamAllowed(ContentStreamAllowed $contentStreamAllowed);
}
