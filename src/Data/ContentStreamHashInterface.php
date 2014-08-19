<?php
namespace Dkd\PhpCmis\Data;

/**
 * Content hash.
 */
interface ContentStreamHashInterface extends ExtensionsDataInterface
{
    /**
     * Returns the hash algorithm.
     *
     * @return string|null the hash algorithm or null if the property value is invalid
     */
    public function getAlgorithm();

    /**
     * Returns the hash value.
     *
     * @return string|null the hash value or null if the property value is invalid
     */
    public function getHash();

    /**
     * Returns the content hash property value (cmis:contentStreamHash).
     *
     * @return string the content hash property value
     */
    public function getPropertyValue();
}
