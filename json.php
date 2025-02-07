<?php

/**
 * Copyright OpenSearch Contributors
 * SPDX-License-Identifier: Apache-2.0
 */

require_once __DIR__ . '/vendor/autoload.php';

$client = (new \OpenSearch\ClientBuilder())
    ->setSigV4Service(getenv("SERVICE") ?: 'es')
    ->setHosts([getenv("ENDPOINT")])
    ->setSigV4Region(getenv("AWS_REGION"))
    ->setSigV4CredentialProvider(true)
    ->build();

$info = $client->request('GET', '/');

echo "{$info['version']['distribution']}: {$info['version']['number']}\n";

$indexName = "movies";

// create an index 
$client->request('PUT', "/$indexName");

// create a document
$client->request('POST', "/$indexName/_doc/1", [
    'body' => [
        'title' => 'Moneyball',
        'director' => 'Bennett Miller',
        'year' => 2011
    ]
]);

sleep(3);

// search for the doc
$result = $client->request('POST', "/$indexName/_search", [
    'body' => [
        'query' => [
            'multi_match' => [
                'query' => 'miller',
                'fields' => ['title^2', 'director']
            ]
        ]
    ]
]);

print_r($result['hits']['hits'][0], false);

// delete a single document
$client->request('DELETE', "/$indexName/_doc/1");

// delete index
$client->request('DELETE', "/$indexName");

?>