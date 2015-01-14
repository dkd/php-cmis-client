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

use Dkd\PhpCmis\Enum\ContentStreamAllowed;

/**
 * Document Object Type.
 */
interface DocumentTypeInterface extends ObjectTypeInterface
{
    /**
     * Gets the <code>isVersionable</code> flag.
     *
     * @return boolean <code>true</code> if this document type is versionable, <code>false</code>
     * if documents of this type cannot be versioned.
     */
    public function isVersionable();

    /**
     * Gets the enum that describes, how content streams have to be handled with
     * this document type.
     *
     * @return ContentStreamAllowed the mode of content stream support
     */
    public function getContentStreamAllowed();
}
