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
 * Representation of an extension feature.
 */
interface ExtensionFeatureInterface extends ExtensionDataInterface
{

    /**
     * Returns a human-readable name for the feature.
     *
     * @return string the feature name, may be empty
     */
    public function getCommonName();

    /**
     * Returns a human-readable description of the feature.
     *
     * @return string the feature description, may be empty
     */
    public function getDescription();

    /**
     * Returns extra feature data.
     *
     * @return string[] the key-value pairs of extra data, may be empty
     */
    public function getFeatureData();

    /**
     * Returns the unique feature ID.
     *
     * @return string the feature ID, not <code>null</code>
     */
    public function getId();

    /**
     * Returns a URL that provides more information about the feature.
     *
     * @return string the feature URL, may be empty
     */
    public function getUrl();

    /**
     * Returns a feature version label.
     *
     * @return string the feature version label, may be empty
     */
    public function getVersionLabel();
}
