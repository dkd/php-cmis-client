<?php
namespace Dkd\PhpCmis;

use Dkd\PhpCmis\CmisObject\CmisObjectInterface;

/**
 * CMIS policy interface.
 */
interface PolicyInterface extends
    CmisObjectInterface,
    FileableCmisObjectInterface,
    ObjectIdInterface,
    PolicyPropertiesInterface
{

}
