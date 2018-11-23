<?php
namespace Dkd\PhpCmis\Bindings\Browser;

/*
 * This file is part of php-cmis-client.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\AclServiceInterface;
use Dkd\PhpCmis\Constants;
use Dkd\PhpCmis\Data\AclInterface;
use Dkd\PhpCmis\Data\ExtensionDataInterface;
use Dkd\PhpCmis\Enum\AclPropagation;

/**
 * Acl Service Browser Binding client.
 */
class AclService extends AbstractBrowserBindingService implements AclServiceInterface
{
    /**
     * Adds or removes the given ACEs to or from the ACL of the object.
     *
     * @param string $repositoryId The identifier for the repository.
     * @param string $objectId The identifier of the object.
     * @param AclInterface|null $addAces The ACEs to be added.
     * @param AclInterface|null $removeAces The ACEs to be removed.
     * @param AclPropagation|null $aclPropagation Specifies how ACEs should be handled.
     * @param ExtensionDataInterface|null $extension
     * @return AclInterface the ACL of the object
     */
    public function applyAcl(
        $repositoryId,
        $objectId,
        AclInterface $addAces = null,
        AclInterface $removeAces = null,
        AclPropagation $aclPropagation = null,
        ExtensionDataInterface $extension = null
    ) {
        // TODO: Implement applyAcl() method.
    }

    /**
     * Get the ACL currently applied to the specified object.
     *
     * @param string $repositoryId The identifier for the repository.
     * @param string $objectId The identifier of the object.
     * @param boolean $onlyBasicPermissions The repository SHOULD make a best effort to fully express the native
     *      security applied to the object.
     *      <code>true</code> indicates that the client requests that the returned ACL be expressed using
     *      only the CMIS basic permissions.
     *      <code>false</code> indicates that the server may respond using either solely CMIS basic permissions,
     *      or repository specific permissions or some combination of both.
     * @param ExtensionDataInterface|null $extension
     * @return AclInterface the ACL of the object
     */
    public function getAcl(
        $repositoryId,
        $objectId,
        $onlyBasicPermissions = true,
        ExtensionDataInterface $extension = null
    ) {
        $url = $this->getObjectUrl($repositoryId, $objectId, Constants::SELECTOR_ACL);
        $responseData = $this->read($url)->json();
        return $this->getJsonConverter()->convertAcl($responseData);
    }
}
