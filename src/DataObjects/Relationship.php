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

use Dkd\PhpCmis\CmisObject\CmisObjectInterface;
use Dkd\PhpCmis\Data\ObjectIdInterface;
use Dkd\PhpCmis\Data\RelationshipInterface;
use Dkd\PhpCmis\OperationContextInterface;
use Dkd\PhpCmis\PropertyIds;

/**
 * Cmis Relationship implementation
 */
class Relationship extends AbstractFileableCmisObject implements RelationshipInterface
{
    /**
     * Gets the source object using the given OperationContext.
     *
     * @param OperationContextInterface|null $context
     * @return CmisObjectInterface|null If the source object ID is invalid, <code>null</code> will be returned.
     */
    public function getSource(OperationContextInterface $context = null)
    {
        $sourceId = $this->getSourceId();
        if ($sourceId === null) {
            return null;
        }
        $context = $this->ensureContext($context);

        return $this->getSession()->getObject($sourceId, $context);
    }

    /**
     * Gets the target object using the given OperationContext.
     *
     * @param OperationContextInterface|null $context
     * @return CmisObjectInterface If the target object ID is invalid, <code>null</code> will be returned.
     */
    public function getTarget(OperationContextInterface $context = null)
    {
        $context = $this->ensureContext($context);
        $targetId = $this->getTargetId();
        if ($targetId === null) {
            return null;
        }

        return $this->getSession()->getObject($targetId, $context);
    }

    /**
     * Returns the source ID of this CMIS relationship (CMIS property cmis:sourceId).
     *
     * @return ObjectIdInterface|null the source ID or <code>null</code> if the property hasn't been requested,
     * hasn't been provided by the repository, or the property value isn't set
     */
    public function getSourceId()
    {
        $sourceId = $this->getPropertyValue(PropertyIds::SOURCE_ID);
        if (empty($sourceId)) {
            return null;
        }

        return $this->getSession()->createObjectId($sourceId);
    }

    /**
     * Returns the target ID of this CMIS relationship (CMIS property cmis:targetId).
     *
     * @return ObjectIdInterface the target ID or <code>null</code> if the property hasn't been requested,
     * hasn't been provided by the repository, or the property value isn't set
     */
    public function getTargetId()
    {
        $targetId = $this->getPropertyValue(PropertyIds::TARGET_ID);
        if (empty($targetId)) {
            return null;
        }

        return $this->getSession()->createObjectId($targetId);
    }
}
