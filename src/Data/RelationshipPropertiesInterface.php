<?php
namespace Dkd\PhpCmis\Data;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Accessors to CMIS relationship properties.
 */
interface RelationshipPropertiesInterface
{
    /**
     * Returns the source ID of this CMIS relationship (CMIS property cmis:sourceId).
     *
     * @return ObjectIdInterface the source ID or <code>null</code> if the property hasn't been requested,
     * hasn't been provided by the repository, or the property value isn't set
     */
    public function getSourceId();

    /**
     * Returns the target ID of this CMIS relationship (CMIS property cmis:targetId).
     *
     * @return ObjectIdInterface the target ID or <code>null</code> if the property hasn't been requested,
     * hasn't been provided by the repository, or the property value isn't set
     */
    public function getTargetId();
}
