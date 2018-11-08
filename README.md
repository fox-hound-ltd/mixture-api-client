PHP Mixture API Client
=====

[![Latest Version](https://img.shields.io/github/release/fox-hound-ltd/mixture-api-client.svg?style=flat-square)](https://github.com/fox-hound-ltd/mixture-api-client/releases)
[![Software License](https://img.shields.io/badge/license-Apache%202.0-blue.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/scrutinizer/build/g/fox-hound-ltd/mixture-api-client.svg?style=flat-square)](https://travis-ci.org/fox-hound-ltd/mixture-api-client)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/fox-hound-ltd/mixture-api-client.svg?style=flat-square)](https://scrutinizer-ci.com/g/fox-hound-ltd/mixture-api-client/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/fox-hound-ltd/mixture-api-client.svg?style=flat-square)](https://scrutinizer-ci.com/g/fox-hound-ltd/mixture-api-client)
[![Total Downloads](https://img.shields.io/packagist/dt/fox-hound-ltd/mixture-api-client.svg?style=flat-square)](https://packagist.org/packages/fox-hound-ltd/mixture-api-client)


概要
-------

GraphQLとRESTに対応したClientライブラリ

PHP Client for [GraphQL](http://graphql.org/) & REST

[GuzzleHttp Base](https://github.com/guzzle/guzzle)ベース

インストール
-------

[Composer](https://getcomposer.org/)を利用

```
$ composer require fox-hound-ltd/mixture-api-client
```

利用方法サンプル
-------

シンプルパターン

``` php
<?php
use MixtureApiClient\ClientBuilder;

$client = ClientBuilder::build(
    'https://hogehoge.com/graphql'
);

$query = <<<'QUERY'
query GetHoge($target_id: ID, $member_name: String) {
  foo(id: $target_id) {
    id_foo
    bar (id: $idBar) {
      id_bar
    }
  }
}
QUERY;

$variables = [
    'target_id' => 11,
    'member_name' => 'bar',
];
$response = $client->query($query, $variables);
```

JsonWebTokenを利用する場合

``` php
<?php
use MixtureApiClient\ClientBuilder;
use MixtureApiClient\JWT;

$jwt= new JWT();

$payload['secret_key'] = 'fiwivkbbeec1e958a6c5dd31336242607c0887754b1d43ebf071fb2890533d611a';

$headers = [
    'Authorization' => $jwt->makeToken('secret', $payload),
];

$config = ['headers' => $headers];

$client = ClientBuilder::build(
    'https://hogehoge.com/graphql',
    $config
);

$query = <<<'QUERY'
query GetHoge($target_id: ID, $member_name: String) {
  foo(id: $target_id) {
    id_foo
    bar (id: $idBar) {
      id_bar
    }
  }
}
QUERY;

$variables = [
    'target_id' => 11,
    'member_name' => 'bar',
];
$response = $client->query($query, $variables);
```

テストについて
-------

``` bash
$ composer test
```

ライセンス
-------

The Apache 2.0 license. Please see [LICENSE](LICENSE) for more information.

[PSR-2]: http://www.php-fig.org/psr/psr-2/
[PSR-4]: http://www.php-fig.org/psr/psr-4/
