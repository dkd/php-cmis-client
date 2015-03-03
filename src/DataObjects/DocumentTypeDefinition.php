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

use Dkd\PhpCmis\Definitions\MutableDocumentTypeDefinitionInterface;
use Dkd\PhpCmis\Enum\ContentStreamAllowed;

/**
 * Document type definition.
 */
class DocumentTypeDefinition extends AbstractTypeDefinition implements MutableDocumentTypeDefinitionInterface
{
    /**
     * @var ContentStreamAllowed
     */
    protected $contentStreamAllowed;

    /**
     * @var boolean
     */
    protected $isVersionable = false;

    /**
     * Object constructor sets defaults
     *
     * @param string $id The type definition id
     */
    public function __construct($id)
    {
        parent::__construct($id);
        $this->contentStreamAllowed = ContentStreamAllowed::cast(ContentStreamAllowed::NOTALLOWED);
    }

    /**
     * Returns whether objects of this type are versionable or not.
     *
     * @return boolean
     */
    public function isVersionable()
    {
        return $this->isVersionable;
    }

    /**
     * Sets whether objects of this type are versionable or not.
     *
     * @param boolean $isVersionable
     */
    public function setIsVersionable($isVersionable)
    {
        $this->isVersionable = $this->castValueToSimpleType('boolean', $isVersionable);
    }

    /**
     * Returns if a content stream must be set.
     *
     * @return ContentStreamAllowed
     */
    public function getContentStreamAllowed()
    {
        return $this->contentStreamAllowed;
    }

    /**
     * Sets if a content stream is allowed, not allowed or is required.
     *
     * @param ContentStreamAllowed $contentStreamAllowed
     */
    public function setContentStreamAllowed(ContentStreamAllowed $contentStreamAllowed)
    {
        $this->contentStreamAllowed = $contentStreamAllowed;
    }
}
