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
use Dkd\PhpCmis\SessionInterface;

/**
 * Item Type implementation.
 */
class ItemType extends ItemTypeDefinition implements ItemTypeInterface
{

    use ObjectTypeHelperTrait {
        ObjectTypeHelperTrait::__construct as private objectTypeConstructor;
    }

    /**
     * @param SessionInterface $session
     * @param ItemTypeDefinitionInterface $typeDefinition
     */
    public function __construct(
        SessionInterface $session,
        ItemTypeDefinitionInterface $typeDefinition
    ) {
        $this->objectTypeConstructor($session, $typeDefinition);
    }
}
