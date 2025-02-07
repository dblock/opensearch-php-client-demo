<?php

/**
 * Copyright OpenSearch Contributors
 * SPDX-License-Identifier: Apache-2.0
 */

require_once __DIR__ . '/vendor/autoload.php';

// setup
$endpoint = getenv("ENDPOINT");
$host = parse_url($endpoint)['host'];

echo "Connecting to {$host} ...";

$psrClient = (new \Symfony\Component\HttpClient\Psr18Client())->withOptions([
    'base_uri' => $endpoint,
    'headers' => [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json'
    ],
]);

$serializer = new \OpenSearch\Serializers\SmartSerializer();

$service = getenv("SERVICE") ?: 'es';
$signer = new Aws\Signature\SignatureV4(
    $service,
    getenv("AWS_REGION")
);

$credentials = new Aws\Credentials\Credentials(
    getenv("AWS_ACCESS_KEY_ID"),
    getenv("AWS_SECRET_ACCESS_KEY"),
    getenv("AWS_SESSION_TOKEN")
);

$signingClient = new \OpenSearch\Aws\SigningClientDecorator(
    $psrClient,
    $credentials,
    $signer,
    [
        "Host" => $host
    ]
);

$requestFactory = new \OpenSearch\RequestFactory(
    $psrClient,
    $psrClient,
    $psrClient,
    $serializer,
);

$transport = (new \OpenSearch\TransportFactory())
    ->setHttpClient($signingClient)
    ->setRequestFactory($requestFactory)
    ->create();

$endpointFactory = new \OpenSearch\EndpointFactory();

$client = new \OpenSearch\Client(
    $transport,
    $endpointFactory,
    []
);

if ($service != 'aoss') {
    $info = $client->info();
    echo "{$info['version']['distribution']}: {$info['version']['number']}\n";
}

$indexName = "movies";

// create an index
$client->indices()->create(['index' => $indexName]);

// create a document
$client->create([
    'index' => $indexName,
    'id' => 1,
    'body' => [
        'title' => 'Moneyball',
        'director' => 'Bennett Miller',
        'year' => 2011
    ]
]);

sleep(1);

// search for the doc
$result = $client->search([
    'index' => $indexName,
    'body' => [
        'query' => [
            'multi_match' => [
                'query' => 'miller',
                'fields' => ['title^2', 'director']
            ]
        ]
    ]
]);

if ($result['hits']['total']['value'] != 0) {
    print_r($result['hits']['hits'][0], false);
}

// delete a single document
$client->delete(['index' => $indexName, 'id' => 1]);

// delete index
$client->indices()->delete(['index' => $indexName]);

?>