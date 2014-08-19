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

use Dkd\PhpCmis\Data\ExtensionsDataInterface;
use Dkd\PhpCmis\Enum\AclPropagation;

interface AclServiceInterface
{
    /**
     * Adds or removes the given ACEs to or from the ACL of the object.
     *
     * @param string $repositoryId
     * @param string $objectId
     * @param AclInterface $addAces
     * @param AclInterface $removeAces
     * @param AclPropagation $aclPropagation
     * @param ExtensionsDataInterface $extension
     * @return AclInterface the ACL of the object
     */
    public function applyAcl(
        $repositoryId,
        $objectId,
        AclInterface $addAces,
        AclInterface $removeAces,
        AclPropagation $aclPropagation,
        ExtensionsDataInterface $extension
    );

    /**
     * Get the ACL currently applied to the specified object.
     *
     * @param string $repositoryId
     * @param string $objectId
     * @param boolean $onlyBasicPermissions
     * @param ExtensionsDataInterface $extension
     * @return AclInterface the ACL of the object
     */
    public function getAcl($repositoryId, $objectId, $onlyBasicPermissions, ExtensionsDataInterface $extension);
}
