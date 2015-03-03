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

use Dkd\PhpCmis\Data\ObjectTypeInterface;
use Dkd\PhpCmis\Data\RelationshipTypeInterface;
use Dkd\PhpCmis\Definitions\RelationshipTypeDefinitionInterface;
use Dkd\PhpCmis\Definitions\TypeDefinitionInterface;
use Dkd\PhpCmis\Exception\CmisInvalidArgumentException;
use Dkd\PhpCmis\SessionInterface;

/**
 * Folder Type implementation.
 */
class RelationshipType extends RelationshipTypeDefinition implements RelationshipTypeInterface
{
    use ObjectTypeHelperTrait;

    /**
     * @var ObjectTypeInterface[]|null
     */
    protected $allowedSourceTypes;

    /**
     * @var ObjectTypeInterface[]|null
     */
    protected $allowedTargetTypes;

    /**
     * Constructor of the object type. This constructor MUST set the session property to the given session and
     * call the <code>self::populate</code> method with the given <code>$typeDefinition</code>
     *
     * @param SessionInterface $session
     * @param RelationshipTypeDefinitionInterface $typeDefinition
     * @throws CmisInvalidArgumentException Exception is thrown if invalid TypeDefinition is given
     */
    public function __construct(
        SessionInterface $session,
        TypeDefinitionInterface $typeDefinition
    ) {
        if (!$typeDefinition instanceof RelationshipTypeDefinitionInterface) {
            throw new CmisInvalidArgumentException(
                sprintf(
                    'Type definition must be instance of RelationshipTypeDefinitionInterface but is "%s"',
                    get_class($typeDefinition)
                )
            );
        }
        $this->session = $session;
        parent::__construct($typeDefinition->getId());
        $this->populate($typeDefinition);
    }

    /**
     * Reset the allowedSourceTypes to <code>null</code> so that the get recreated on the
     * new defined ids.
     *
     * @param string[] $allowedSourceTypeIds
     */
    public function setAllowedSourceTypeIds(array $allowedSourceTypeIds)
    {
        $this->allowedSourceTypes = null;
        parent::setAllowedSourceTypeIds($allowedSourceTypeIds);
    }

    /**
     * @return ObjectTypeInterface[]
     */
    public function getAllowedSourceTypes()
    {
        if ($this->allowedSourceTypes === null) {
            $this->allowedSourceTypes = array();
            foreach ($this->getAllowedSourceTypeIds() as $id) {
                $this->allowedSourceTypes[] = $this->getSession()->getTypeDefinition($id);
            }
        }

        return $this->allowedSourceTypes;
    }

    /**
     * Reset the allowedSourceTypes to <code>null</code> so that the get recreated on the
     * new defined ids.
     *
     * @param string[] $allowedTargetTypeIds
     */
    public function setAllowedTargetTypeIds(array $allowedTargetTypeIds)
    {
        $this->allowedTargetTypes = null;
        parent::setAllowedTargetTypeIds($allowedTargetTypeIds);
    }

    /**
     * @return ObjectTypeInterface[]
     */
    public function getAllowedTargetTypes()
    {
        if ($this->allowedTargetTypes === null) {
            $this->allowedTargetTypes = array();
            foreach ($this->getAllowedTargetTypeIds() as $id) {
                $this->allowedTargetTypes[] = $this->getSession()->getTypeDefinition($id);
            }
        }

        return $this->allowedTargetTypes;
    }
}
