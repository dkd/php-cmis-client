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

use Dkd\PhpCmis\Data\ItemTypeInterface;
use Dkd\PhpCmis\Definitions\ItemTypeDefinitionInterface;
use Dkd\PhpCmis\Definitions\TypeDefinitionInterface;
use Dkd\PhpCmis\Exception\CmisInvalidArgumentException;
use Dkd\PhpCmis\SessionInterface;

/**
 * Item Type implementation.
 */
class ItemType extends ItemTypeDefinition implements ItemTypeInterface
{
    use ObjectTypeHelperTrait;

    /**
     * Constructor of the object type. This constructor MUST call the parent constructor of the type definition
     * and MUST all the <code>ObjectTypeHelperTrait::objectTypeConstructor</code>
     *
     * @param SessionInterface $session
     * @param ItemTypeDefinitionInterface $typeDefinition
     * @throws CmisInvalidArgumentException Exception is thrown if invalid TypeDefinition is given
     */
    public function __construct(
        SessionInterface $session,
        TypeDefinitionInterface $typeDefinition
    ) {
        if (!$typeDefinition instanceof ItemTypeDefinitionInterface) {
            throw new CmisInvalidArgumentException(
                sprintf(
                    'Type definition must be instance of ItemTypeDefinitionInterface but is "%s"',
                    get_class($typeDefinition)
                )
            );
        }
        parent::__construct($typeDefinition->getId());
        $this->objectTypeConstructor($session, $typeDefinition);
    }
}
