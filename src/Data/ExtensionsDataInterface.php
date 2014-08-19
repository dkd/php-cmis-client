<?php
namespace Dkd\PhpCmis\Data;

/**
 * Holds extension data either set by the CMIS repository or the client.
 */
interface ExtensionsDataInterface
{
    /**
     * Returns the list of top-level extension elements.
     *
     * @return CmisExtensionElementInterface[]
     */
    public function getExtensions();

    /**
     * Sets the list of top-level extension elements.
     *
     * @param CmisExtensionElementInterface[] $extensions
     * @return void
     */
    public function setExtensions(array $extensions);
}
