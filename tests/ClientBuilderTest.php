<?php

namespace MixtureApiClient\Test;

use MixtureApiClient\ClientBuilder;
use PHPUnit\Framework\TestCase;

class ClientBuilderTest extends TestCase
{
    public function testBuild()
    {
        $client = ClientBuilder::build('http://foo.bar/qux');
        $this->assertInstanceOf(\MixtureApiClient\Client::class, $client);
    }
}
