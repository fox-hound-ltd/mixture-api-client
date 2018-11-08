<?php
require '../vendor/autoload.php';

use MixtureApiClient\ClientBuilder;
use MixtureApiClient\JWT;

$jwt= new JWT();

$payload = [];
$payload['secret_key'] = '72091beec1e958a6c5dd31336242607c0887754b1d43ebf071fb2890533d611a';

$headers = [
    'Authorization' => $jwt->makeToken('secret', $payload),
];

$config = ['headers' => $headers];

$client = ClientBuilder::build(
    'http://localhost:8070',
    $config
);

$id = 2;
$query = <<<'QUERY'
query GetHogehoge($target_id: ID) {
  serviceGroup(id: $target_id)
  {
    id
    name
  }
}
QUERY;

$variables = ['target_id' => "2"];

//$config['end_point'] = '/graphql';

$response = $client->query($query, $variables, $config);

var_dump($response->getData());
exit();
