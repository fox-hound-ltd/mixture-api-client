<?php

namespace MixtureApiClient;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\TransferException;

/**
 * Class Client
 *
 * @package MixtureApiClient
 */
class Client
{
    private $httpClient;
    private $responseBuilder;

    public function __construct(ClientInterface $httpClient, ResponseBuilder $responseBuilder)
    {
        $this->httpClient = $httpClient;
        $this->responseBuilder = $responseBuilder;
    }

    /**
     * @param  string                                $query
     * @param  array|null                            $variables
     * @param  array                                 $options
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return Response
     */
    public function query(string $query, array $variables = null, array $options = []): Response
    {
        $options['json']['query'] = $query;
        if (!is_null($variables)) {
            $options['json']['variables'] = $variables;
        }

        // Endpointがあれば設定。基本はgraphql
        $end_point = '/graphql';
        if (isset($options['end_point'])) {
            $end_point = $options['end_point'];
        }

        return $this->request('POST', $end_point, $options);
    }

    /**
     * @param  string                                $end_point
     * @param  array                                 $options
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return Response
     */
    public function get(string $end_point = '/', array $options = []): Response
    {
        return $this->request('GET', $end_point, $options);
    }

    /**
     * @param  string                                $end_point
     * @param  array                                 $options
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return Response
     */
    public function post(string $end_point = '/', array $options = []): Response
    {
        return $this->request('POST', $end_point, $options);
    }

    /**
     * @param  string                                $end_point
     * @param  array                                 $options
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return Response
     */
    public function put(string $end_point = '/', array $options = []): Response
    {
        return $this->request('PUT', $end_point, $options);
    }

    /**
     * @param  string                                $end_point
     * @param  array                                 $options
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return Response
     */
    public function delete(string $end_point = '/', array $options = []): Response
    {
        return $this->request('DELETE', $end_point, $options);
    }

    /**
     * @param  string                                $method
     * @param  string                                $end_point
     * @param  array|null                            $options
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @return Response
     */
    private function request(string $method, string $end_point, array $options = []): Response
    {
        try {
            $response = $this->httpClient->request($method, $end_point, $options);
        } catch (TransferException $e) {
            throw new \RuntimeException('Network Error.' . $e->getMessage(), 0, $e);
        }
        return $this->responseBuilder->build($response);
    }
}
