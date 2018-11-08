<?php

namespace MixtureApiClient;

/**
 * Class ClientBuilder
 *
 * @package MixtureApiClient
 */
class ClientBuilder
{
    public static function build(string $endpoint, array $guzzleOptions = []): Client
    {
        $guzzleOptions = array_merge(['base_uri' => $endpoint], $guzzleOptions);

        return new \MixtureApiClient\Client(
            new \GuzzleHttp\Client($guzzleOptions),
            new \MixtureApiClient\ResponseBuilder()
        );
    }
}
