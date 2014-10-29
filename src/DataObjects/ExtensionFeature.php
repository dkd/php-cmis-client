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

use Dkd\PhpCmis\Data\ExtensionFeatureInterface;

/**
 * Representation of an extension feature.
 */
class ExtensionFeature extends AbstractExtensionData implements ExtensionFeatureInterface
{
    /**
     * @var string
     */
    protected $commonName = '';

    /**
     * @var string
     */
    protected $description = '';

    /**
     * @var string[]
     */
    protected $featureData = array();

    /**
     * @var string
     */
    protected $id = '';

    /**
     * @var string
     */
    protected $url = '';

    /**
     * @var string
     */
    protected $versionLabel = '';

    /**
     * @inheritDoc
     */
    public function getCommonName()
    {
        return $this->commonName;
    }

    /**
     * @param string $commonName
     */
    public function setCommonName($commonName)
    {
        $this->commonName = (string) $commonName;
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = (string) $description;
    }

    /**
     * @inheritDoc
     */
    public function getFeatureData()
    {
        return $this->featureData;
    }

    /**
     * @param string[] $featureData
     */
    public function setFeatureData(array $featureData)
    {
        $featureData = array_map('strval', $featureData);
        $this->featureData = $featureData;
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = (string) $id;
    }

    /**
     * @inheritDoc
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = (string) $url;
    }

    /**
     * @inheritDoc
     */
    public function getVersionLabel()
    {
        return $this->versionLabel;
    }

    /**
     * @param string $versionLabel
     */
    public function setVersionLabel($versionLabel)
    {
        $this->versionLabel = (string) $versionLabel;
    }
}
