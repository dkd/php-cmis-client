<?php
namespace Dkd\PhpCmis\Test\Unit;

/**
 * This file is part of php-cmis-lib.
 *
 * (c) Dimitri Ebert <dimitri.ebert@dkd.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use GuzzleHttp\Message\MessageFactory;

trait FixtureHelperTrait
{
    /**
     * Returns the content of a json fixture as array
     *
     * @param string $fixture the path to the json fixture file
     * @return array|mixed
     */
    protected function getResponseFixtureContentAsArray($fixture)
    {
        $messageFactory = new MessageFactory();
        $fixtureFilename = dirname(dirname(__FILE__)) . '/Fixtures/' . $fixture;
        if (!file_exists($fixtureFilename)) {
            $this->fail(sprintf('Fixture "%s" not found!', $fixtureFilename));
        }
        $response = $messageFactory->fromMessage(file_get_contents($fixtureFilename));

        $result = array();
        try {
            $result = $response->json();
        } catch (\GuzzleHttp\Exception\ParseException $exception) {
            $this->fail(sprintf('Fixture "%s" does not contain a valid JSON body!', $fixtureFilename));
        }

        return $result;
    }

    /**
     * Fails a test with the given message.
     *
     * @param  string $message
     */
    abstract public function fail($message = '');
}
