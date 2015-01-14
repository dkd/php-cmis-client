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

use Dkd\PhpCmis\Data\DocumentTypeInterface;
use Dkd\PhpCmis\Definitions\DocumentTypeDefinitionInterface;
use Dkd\PhpCmis\SessionInterface;

/**
 * Document Type implementation.
 */
class DocumentType extends DocumentTypeDefinition implements DocumentTypeInterface
{
    use ObjectTypeHelperTrait {
        ObjectTypeHelperTrait::__construct as private objectTypeConstructor;
    }

    /**
     * @param SessionInterface $session
     * @param DocumentTypeDefinitionInterface $typeDefinition
     */
    public function __construct(
        SessionInterface $session,
        DocumentTypeDefinitionInterface $typeDefinition
    ) {
        $this->objectTypeConstructor($session, $typeDefinition);
    }
}
