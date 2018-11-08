<?php

namespace MixtureApiClient\Test;

use MixtureApiClient\Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    private $httpClient;
    private $mockGraphqlResponseBuilder;
    private $client;

    public function setUp()
    {
        $this->httpClient = $this->createMock(\GuzzleHttp\ClientInterface::class);
        $this->mockGraphqlResponseBuilder = $this->createMock(\MixtureApiClient\ResponseBuilder::class);
        $this->client = new Client($this->httpClient, $this->mockGraphqlResponseBuilder);
    }

    public function testSimpleQueryWhenHasNetworkErrors()
    {
        $this->httpClient->expects($this->once())
            ->method('request')
            ->willThrowException(new \GuzzleHttp\Exception\TransferException('library error'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Network Error.');

        $query = $this->getGraphQLSimpleQuery();
        $this->client->query($query);
    }

    public function testCanRetrievePreviousExceptionWhenSimpleQueryHasErrors()
    {
        $previousException = null;
        try {
            $originalException = new \GuzzleHttp\Exception\ServerException(
                'Server side error',
                $this->createMock(\Psr\Http\Message\RequestInterface::class)
            );

            $this->httpClient->expects($this->once())
                ->method('request')
                ->willThrowException($originalException);

            $query = $this->getGraphQLSimpleQuery();
            $this->client->query($query);
        } catch (\Exception $e) {
            $previousException = $e->getPrevious();
        } finally {
            $this->assertSame($originalException, $previousException);
        }
    }

    public function testSimpleQueryWhenInvalidJsonIsReceived()
    {
        $query = $this->getGraphQLSimpleQuery();

        $mockHttpResponse = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
        $this->mockGraphqlResponseBuilder->expects($this->once())
            ->method('build')
            ->with($mockHttpResponse)
            ->willThrowException(new \UnexpectedValueException('Invalid JSON response.'));
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                '/graphql',
                [
                    'json' => [
                        'query' => $query,
                    ],
                ]
            )
            ->willReturn($mockHttpResponse);

        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Invalid JSON response.');

        $this->client->query($query);
    }

    public function testGraphQLSimpleQuery()
    {
        $mockResponse = $this->createMock(\MixtureApiClient\Response::class);
        $mockHttpResponse = $this->createMock(\Psr\Http\Message\ResponseInterface::class);

        $query = $this->getGraphQLSimpleQuery();

        $this->mockGraphqlResponseBuilder->expects($this->once())
            ->method('build')
            ->with($mockHttpResponse)
            ->willReturn($mockResponse);

        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                '/graphql',
                [
                    'json' => [
                        'query' => $query,
                    ],
                ]
            )
            ->willReturn($mockHttpResponse);

        $response = $this->client->query($query);
        $this->assertInstanceOf(\MixtureApiClient\Response::class, $response);
    }

    public function testGraphQLQueryWithVariables()
    {
        $mockResponse = $this->createMock(\MixtureApiClient\Response::class);
        $mockHttpResponse = $this->createMock(\Psr\Http\Message\ResponseInterface::class);
        
        $response = [
            'data' => [
                'program' => [
                    'id_appstore' => null,
                ],
            ],
        ];

        $query = $this->getGraphQLQueryWithVariables();
        $variables = [
            'idProgram' => '642e69c0-9b2e-11e6-9850-00163ed833e7',
            'locale'    => 'nl',
        ];

        $options = [];
        $options['end_point'] = '/graphql';

        $this->mockGraphqlResponseBuilder->expects($this->once())
            ->method('build')
            ->with($mockHttpResponse)
            ->willReturn($mockResponse);
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                '/graphql',
                [
                    'json' => [
                        'query' => $query,
                        'variables' => $variables,
                    ],
                    'end_point' => '/graphql',
                ]
            )
            ->willReturn($mockHttpResponse);

        $response = $this->client->query($query, $variables, $options);
        $this->assertInstanceOf(\MixtureApiClient\Response::class, $response);
    }

    public function testGetQuery()
    {
        $mockResponse = $this->createMock(\MixtureApiClient\Response::class);
        $mockHttpResponse = $this->createMock(\Psr\Http\Message\ResponseInterface::class);

        $response = [
            'data' => [
                'program' => [
                    'id_appstore' => null,
                ],
            ],
        ];
        $expectedData = $response['data'];
        $query = $this->getGraphQLSimpleQuery();
        $request['body'] = $query;

        $this->mockGraphqlResponseBuilder->expects($this->once())
            ->method('build')
            ->with($mockHttpResponse)
            ->willReturn($mockResponse);
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                '/',
                $request
            )
            ->willReturn($mockHttpResponse);

        $response = $this->client->get('/', $request);
        $this->assertInstanceOf(\MixtureApiClient\Response::class, $response);
    }

    public function testPostQuery()
    {
        $mockResponse = $this->createMock(\MixtureApiClient\Response::class);
        $mockHttpResponse = $this->createMock(\Psr\Http\Message\ResponseInterface::class);

        $response = [
            'data' => [
                'program' => [
                    'id_appstore' => null,
                ],
            ],
        ];
        $expectedData = $response['data'];
        $query = $this->getGraphQLSimpleQuery();
        $request['body'] = $query;

        $this->mockGraphqlResponseBuilder->expects($this->once())
            ->method('build')
            ->with($mockHttpResponse)
            ->willReturn($mockResponse);
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'POST',
                '/',
                $request
            )
            ->willReturn($mockHttpResponse);

        $response = $this->client->post('/', $request);
        $this->assertInstanceOf(\MixtureApiClient\Response::class, $response);
    }

    public function testPutQuery()
    {
        $mockResponse = $this->createMock(\MixtureApiClient\Response::class);
        $mockHttpResponse = $this->createMock(\Psr\Http\Message\ResponseInterface::class);

        $response = [
            'data' => [
                'program' => [
                    'id_appstore' => null,
                ],
            ],
        ];
        $expectedData = $response['data'];
        $query = $this->getGraphQLSimpleQuery();
        $request['body'] = $query;

        $this->mockGraphqlResponseBuilder->expects($this->once())
            ->method('build')
            ->with($mockHttpResponse)
            ->willReturn($mockResponse);
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'PUT',
                '/',
                $request
            )
            ->willReturn($mockHttpResponse);

        $response = $this->client->put('/', $request);
        $this->assertInstanceOf(\MixtureApiClient\Response::class, $response);
    }

    public function testDeleteQuery()
    {
        $mockResponse = $this->createMock(\MixtureApiClient\Response::class);
        $mockHttpResponse = $this->createMock(\Psr\Http\Message\ResponseInterface::class);

        $response = [
            'data' => [
                'program' => [
                    'id_appstore' => null,
                ],
            ],
        ];
        $expectedData = $response['data'];
        $query = $this->getGraphQLSimpleQuery();
        $request['body'] = $query;

        $this->mockGraphqlResponseBuilder->expects($this->once())
            ->method('build')
            ->with($mockHttpResponse)
            ->willReturn($mockResponse);
        $this->httpClient->expects($this->once())
            ->method('request')
            ->with(
                'DELETE',
                '/',
                $request
            )
            ->willReturn($mockHttpResponse);

        $response = $this->client->delete('/', $request);
        $this->assertInstanceOf(\MixtureApiClient\Response::class, $response);
    }

    private function getGraphQLSimpleQuery()
    {
        return <<<'QUERY'
{
  foo(id:"bar") {
    id_foo
  }
}
QUERY;
    }

    private function getGraphQLQueryWithVariables()
    {
        return <<<'QUERY'
query GetFooBar($idFoo: String, $idBar: String) {
  foo(id: $idFoo) {
    id_foo
    bar (id: $idBar) {
      id_bar
    }
  }
}
QUERY;
    }
}
