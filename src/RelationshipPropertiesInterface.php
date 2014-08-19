<?php
namespace Dkd\PhpCmis;

/**
 * Accessors to CMIS relationship properties.
 */
interface RelationshipPropertiesInterface
{
    /**
     * Returns the source ID of this CMIS relationship (CMIS property cmis:sourceId).
     *
     * @return ObjectIdInterface the source ID or null if the property hasn't been requested,
     * hasn't been provided by the repository, or the property value isn't set
     */
    public function getSourceId();

    /**
     * Returns the target ID of this CMIS relationship (CMIS property cmis:targetId).
     *
     * @return ObjectIdInterface the target ID or null if the property hasn't been requested,
     * hasn't been provided by the repository, or the property value isn't set
     */
    public function getTargetId();
}
