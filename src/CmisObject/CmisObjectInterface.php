<?php
namespace Dkd\PhpCmis\CmisObject;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Dkd\PhpCmis\Data\AceInterface;
use Dkd\PhpCmis\Data\AclInterface;
use Dkd\PhpCmis\Data\AllowableActionsInterface;
use Dkd\PhpCmis\Data\CmisExtensionElementInterface;
use Dkd\PhpCmis\Enum\AclPropagation;
use Dkd\PhpCmis\Enum\ExtensionLevel;
use Dkd\PhpCmis\Exception\CmisObjectNotFoundException;
use Dkd\PhpCmis\ObjectIdInterface;
use Dkd\PhpCmis\PolicyInterface;
use Dkd\PhpCmis\RelationshipInterface;
use Dkd\PhpCmis\RenditionInterface;

/**
 * Base interface for all CMIS objects.
 */
interface CmisObjectInterface extends ObjectIdInterface, CmisObjectPropertiesInterface
{
    /**
     * Adds ACEs to the object and refreshes this object afterwards.
     * @param AceInterface[] $addAces
     * @param AclPropagation $aclPropagation
     * @return AclInterface
     */
    public function addAcl(array $addAces, AclPropagation $aclPropagation);

    /**
     * Adds and removes ACEs to the object and refreshes this object afterwards.
     *
     * @param AceInterface[] $addAces
     * @param AceInterface[] $removeAces
     * @param AclPropagation $aclPropagation
     * @return AclInterface
     */
    public function applyAcl(array $addAces, array $removeAces, AclPropagation $aclPropagation);

    /**
     * Applies the provided policies and refreshes this object afterwards.
     * @param ObjectIdInterface[] $policyIds
     * @return void
     */
    public function applyPolicy(array $policyIds);

    /**
     * Deletes this object
     * @param boolean $allVersions if this object is a document this parameter defines whether only this
     * version (false) or all versions (true ) should be deleted, the parameter is ignored for all other object types
     * @return void
     */
    public function delete($allVersions);

    /**
     * Returns the ACL if it has been fetched for this object.
     *
     * @return AclInterface
     */
    public function getAcl();

    /**
     * Returns an adapter based on the given interface.
     *
     * @TODO check that to do here
     *
     * @param mixed $adapterInterface
     * @return mixed an adapter object or null if no adapter object could be created
     */
    public function getAdapter($adapterInterface);

    /**
     * Returns the allowable actions if they have been fetched for this object.
     *
     * @return AllowableActionsInterface[]
     */
    public function getAllowableActions();

    /**
     * Returns the extensions for the given level.
     * @param ExtensionLevel $level the level
     * @return CmisExtensionElementInterface[] the extensions at that level or null if there no extensions
     */
    public function getExtensions(ExtensionLevel $level);

    /**
     * Returns the applied policies if they have been fetched for this object.
     *
     * @return PolicyInterface[]
     */
    public function getPolicies();

    /**
     * Returns the timestamp of the last refresh.
     *
     * @return int the difference, measured in milliseconds, between the last refresh time
     * and midnight, January 1, 1970 UTC.
     */
    public function getRefreshTimestamp();

    /**
     * Returns the relationships if they have been fetched for this object.
     *
     * @return RelationshipInterface[]
     */
    public function getRelationships();

    /**
     * Returns the renditions if they have been fetched for this object.
     *
     * @return RenditionInterface[]
     */
    public function getRenditions();

    /**
     * Reloads this object from the repository.
     *
     * @return void
     * @throws CmisObjectNotFoundException - if the object doesn't exist anymore in the repository
     */
    public function refresh();

    /**
     * Reloads the data from the repository if the last refresh did not occur within durationInMillis.
     *
     * @param integer $durationInMillis
     * @return void
     * @throws CmisObjectNotFoundException - if the object doesn't exist anymore in the repository
     */
    public function refreshIfOld($durationInMillis);

    /**
     * Removes ACEs to the object and refreshes this object afterwards.
     *
     * @param $removeAces
     * @param AclPropagation $aclPropagation
     * @return AclInterface the new ACL of this object
     */
    public function removeAcl($removeAces, AclPropagation $aclPropagation);

    /**
     * Removes the provided policies and refreshes this object afterwards.
     *
     * @param ObjectIdInterface[] $policyIds
     * @return void
     */
    public function removePolicy(array $policyIds);

    /**
     * Renames this object (changes the value of cmis:name).
     * If the repository created a new object, for example a new version, the object id of the
     * new object is returned. Otherwise the object id of the current object is returned.
     *
     * @param string $newName the new name, not null or empty
     * @param boolean $refresh true if this object should be refresh after the update, false if not
     * @return CmisObjectInterface the updated object
     */
    public function rename($newName, $refresh);

    /**
     * Removes the direct ACE of this object, sets the provided ACEs to the object and refreshes this object afterwards.
     * @param AceInterface[] $aces
     * @return AclInterface
     */
    public function setAcl(array $aces);

    /**
     * Updates the provided properties.  If the repository created a new object, for example a new version,
     * the object ID of the new object is returned. Otherwise the object ID of the current object is returned.
     *
     * @param array $properties the properties to update
     * @param $refresh true if this object should be refresh after the update, false if not
     * @return ObjectIdInterface the object ID of the updated object
     */
    public function updateProperties(array $properties, $refresh);
}
