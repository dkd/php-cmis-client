<?php
namespace Dkd\PhpCmis\Data;

/**
 * Representation of an extension feature.
 */
interface ExtensionFeatureInterface extends ExtensionsDataInterface
{

    /**
     * Returns a human-readable name for the feature.
     *
     * @return string|null the feature name, may be null
     */
    public function getCommonName();

    /**
     * Returns a human-readable description of the feature.
     *
     * @return string|null the feature description, may be null
     */
    public function getDescription();

    /**
     * Returns extra feature data.
     *
     * @return string[] the key-value pairs of extra data, may be null
     */
    public function getFeatureData();

    /**
     * Returns the unique feature ID.
     *
     * @return string the feature ID, not null
     */
    public function getId();

    /**
     * Returns a URL that provides more information about the feature.
     *
     * @return string|null the feature URL, may be null
     */
    public function getUrl();

    /**
     * Returns a feature version label.
     *
     * @return string|null the feature version label, may be null
     */
    public function getVersionLabel();
}
