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

use Dkd\PhpCmis\Data\FolderTypeInterface;
use Dkd\PhpCmis\Definitions\FolderTypeDefinitionInterface;
use Dkd\PhpCmis\Definitions\TypeDefinitionInterface;
use Dkd\PhpCmis\Exception\CmisInvalidArgumentException;
use Dkd\PhpCmis\SessionInterface;

/**
 * Folder Type implementation.
 */
class FolderType extends FolderTypeDefinition implements FolderTypeInterface
{
    use ObjectTypeHelperTrait;

    /**
     * Constructor of the object type. This constructor MUST call the parent constructor of the type definition
     * and MUST all the <code>ObjectTypeHelperTrait::objectTypeConstructor</code>
     *
     * @param SessionInterface $session
     * @param FolderTypeDefinitionInterface $typeDefinition
     * @throws CmisInvalidArgumentException Exception is thrown if invalid TypeDefinition is given
     */
    public function __construct(
        SessionInterface $session,
        TypeDefinitionInterface $typeDefinition
    ) {
        if (!$typeDefinition instanceof FolderTypeDefinitionInterface) {
            throw new CmisInvalidArgumentException(
                sprintf(
                    'Type definition must be instance of FolderTypeDefinitionInterface but is "%s"',
                    get_class($typeDefinition)
                )
            );
        }
        parent::__construct($typeDefinition->getId());
        $this->objectTypeConstructor($session, $typeDefinition);
    }
}
