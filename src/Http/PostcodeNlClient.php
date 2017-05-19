<?php

namespace Speelpenning\PostcodeNl\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Config\Repository;
use Psr\Http\Message\ResponseInterface;

class PostcodeNlClient
{
    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var Client
     */
    protected $client;

    /**
     * Client constructor.
     *
     * @param Repository $config
     * @param Client $client
     */
    public function __construct(Repository $config, Client $client)
    {
        $this->config = $config;
        $this->client = $client;
    }

    /**
     * Performs a GET request compatible with Postcode.nl.
     *
     * @param string $uri
     * @return ResponseInterface
     */
    public function get($uri)
    {
        try {
            return $this->client->get($uri, $this->getRequestOptions());
        } catch (ClientException $e) {
            $this->handleClientException($e);
        }
    }

    /**
     * Returns the configured request options.
     *
     * @return array
     */
    protected function getRequestOptions()
    {
        return $this->config->get('postcode-nl.requestOptions');
    }

    /**
     * Handles the Guzzle client exception.
     *
     * @param ClientException $e
     * @throws 401 Unauthorized
     * @throws 403 AccountSuspended
     * @throws 404 AddressNotFound
     */
    protected function handleClientException(ClientException $e)
    {
        switch ($e->getCode()) {
            case 401:
                abort(401, 'Unauthorized');
            case 403:
                abort(403, 'AccountSuspended');
            case 404:
                abort(404, 'AddressNotFound');
        }
    }
}
