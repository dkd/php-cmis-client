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

use Dkd\PhpCmis\Definitions\DocumentTypeDefinitionInterface;
use Dkd\PhpCmis\Definitions\MutableDocumentTypeDefinitionInterface;
use Dkd\PhpCmis\Definitions\TypeDefinitionInterface;
use Dkd\PhpCmis\Enum\ContentStreamAllowed;
use Dkd\PhpCmis\Exception\CmisInvalidArgumentException;

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
     * @param DocumentTypeDefinitionInterface $typeDefinition
     */
    public function initialize(TypeDefinitionInterface $typeDefinition)
    {
        if (!$typeDefinition instanceof DocumentTypeDefinitionInterface) {
            throw new CmisInvalidArgumentException(
                sprintf(
                    'In instance of RelationshipTypeDefinition was expected but "%s" was given.',
                    get_class($typeDefinition)
                )
            );
        }
        parent::initialize($typeDefinition);
        $this->setIsVersionable($typeDefinition->isVersionable());
        $this->setContentStreamAllowed($typeDefinition->getContentStreamAllowed());
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
