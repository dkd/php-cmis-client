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
use Dkd\PhpCmis\Definitions\TypeDefinitionInterface;
use Dkd\PhpCmis\Exception\CmisInvalidArgumentException;
use Dkd\PhpCmis\SessionInterface;

/**
 * Secondary Type implementation.
 */
class SecondaryType extends SecondaryTypeDefinition implements SecondaryTypeInterface
{
    use ObjectTypeHelperTrait;

    /**
     * Constructor of the object type. This constructor MUST set the session property to the given session and
     * call the <code>self::populate</code> method with the given <code>$typeDefinition</code>
     *
     * @param SessionInterface $session
     * @param SecondaryTypeDefinitionInterface $typeDefinition
     * @throws CmisInvalidArgumentException Exception is thrown if invalid TypeDefinition is given
     */
    public function __construct(
        SessionInterface $session,
        TypeDefinitionInterface $typeDefinition
    ) {
        if (!$typeDefinition instanceof SecondaryTypeDefinitionInterface) {
            throw new CmisInvalidArgumentException(
                sprintf(
                    'Type definition must be instance of SecondaryTypeDefinitionInterface but is "%s"',
                    get_class($typeDefinition)
                )
            );
        }
        $this->session = $session;
        parent::__construct($typeDefinition->getId());
        $this->populate($typeDefinition);
    }
}
