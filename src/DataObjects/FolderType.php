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
use Dkd\PhpCmis\SessionInterface;

/**
 * Folder Type implementation.
 */
class FolderType extends FolderTypeDefinition implements FolderTypeInterface
{
    use ObjectTypeHelperTrait {
        ObjectTypeHelperTrait::__construct as private objectTypeConstructor;
    }

    /**
     * @param SessionInterface $session
     * @param FolderTypeDefinitionInterface $typeDefinition
     */
    public function __construct(
        SessionInterface $session,
        FolderTypeDefinitionInterface $typeDefinition
    ) {
        $this->objectTypeConstructor($session, $typeDefinition);
    }
}
