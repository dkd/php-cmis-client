<?php
namespace Dkd\PhpCmis;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\CmisObject\CmisObjectInterface;

/**
 * CMIS relationship interface.
 */
interface RelationshipInterface extends CmisObjectInterface, RelationshipPropertiesInterface
{
    /**
     * Gets the source object using the given OperationContext.
     *
     * @param OperationContextInterface $context
     * @return CmisObjectInterface|null If the source object ID is invalid, null will be returned.
     */
    public function getSource(OperationContextInterface $context = null);

    /**
     * Gets the target object using the given OperationContext.
     *
     * @param OperationContextInterface $context
     * @return CmisObjectInterface If the target object ID is invalid, null will be returned.
     */
    public function getTarget(OperationContextInterface $context = null);
}
