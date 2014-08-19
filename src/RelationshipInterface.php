<?php
namespace Dkd\PhpCmis;

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
