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

use Dkd\PhpCmis\Data\SecondaryTypeInterface;
use Dkd\PhpCmis\Definitions\SecondaryTypeDefinitionInterface;
use Dkd\PhpCmis\SessionInterface;

/**
 * Secondary Type implementation.
 */
class SecondaryType extends SecondaryTypeDefinition implements SecondaryTypeInterface
{
    use ObjectTypeHelperTrait {
        ObjectTypeHelperTrait::__construct as private objectTypeConstructor;
    }

    /**
     * @param SessionInterface $session
     * @param SecondaryTypeDefinitionInterface $typeDefinition
     */
    public function __construct(
        SessionInterface $session,
        SecondaryTypeDefinitionInterface $typeDefinition
    ) {
        $this->objectTypeConstructor($session, $typeDefinition);
    }
}
