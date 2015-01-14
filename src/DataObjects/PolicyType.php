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

use Dkd\PhpCmis\Data\PolicyTypeInterface;
use Dkd\PhpCmis\Definitions\PolicyTypeDefinitionInterface;
use Dkd\PhpCmis\SessionInterface;

/**
 * Policy Type implementation.
 */
class PolicyType extends PolicyTypeDefinition implements PolicyTypeInterface
{
    use ObjectTypeHelperTrait {
        ObjectTypeHelperTrait::__construct as private objectTypeConstructor;
    }

    /**
     * @param SessionInterface $session
     * @param PolicyTypeDefinitionInterface $typeDefinition
     */
    public function __construct(
        SessionInterface $session,
        PolicyTypeDefinitionInterface $typeDefinition
    ) {
        $this->objectTypeConstructor($session, $typeDefinition);
    }
}
