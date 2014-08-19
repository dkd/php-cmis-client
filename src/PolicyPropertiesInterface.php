<?php
namespace Dkd\PhpCmis;

/**
 * Accessors to CMIS policy properties.
 */
interface PolicyPropertiesInterface
{
    /**
     * Returns the policy text of this CMIS policy (CMIS property cmis:policyText).
     *
     * @return string the policy text or null if the property hasn't been requested,
     * hasn't been provided by the repository, or the property value isn't set
     */
    public function getPolicyText();
}
