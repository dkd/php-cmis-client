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

use Dkd\PhpCmis\Data\CmisExtensionElementInterface;
use Dkd\PhpCmis\Data\ExtensionDataInterface;
use Dkd\PhpCmis\Traits\TypeHelperTrait;
use Dkd\Populate\PopulateTrait;

/**
 * Holds extension data either set by the CMIS repository or the client.
 */
abstract class AbstractExtensionData implements ExtensionDataInterface
{
    use PopulateTrait;
    use TypeHelperTrait;

    /**
     * @var CmisExtensionElementInterface[]
     */
    protected $extensions = array();

    /**
     * Returns the list of top-level extension elements.
     *
     * @return CmisExtensionElementInterface[]
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Sets the list of top-level extension elements.
     *
     * @param CmisExtensionElementInterface[] $extensions
     */
    public function setExtensions(array $extensions)
    {
        foreach ($extensions as $extension) {
            $this->checkType('\\Dkd\\PhpCmis\\Data\\CmisExtensionElementInterface', $extension);
        }

        $this->extensions = $extensions;
    }
}
