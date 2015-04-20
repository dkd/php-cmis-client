<?php
namespace Dkd\PhpCmis\Bindings;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Sascha Egerer <sascha.egerer@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Provides access to internal links. It bypasses the CMIS domain model. Use
 * with care!
 */
interface LinkAccessInterface
{
    /**
     * Gets the content link from the cache if it is there or loads it into the
     * cache if it is not there.
     *
     * @param string $repositoryId
     * @param string $documentId
     * @return string|null
     */
    public function loadContentLink($repositoryId, $documentId);

    /**
     * Gets a rendition content link from the cache if it is there or loads it
     * into the cache if it is not there.
     *
     * @param string $repositoryId
     * @param string $documentId
     * @param string $streamId
     * @return string|null
     */
    public function loadRenditionContentLink($repositoryId, $documentId, $streamId);
}
