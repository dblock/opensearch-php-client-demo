<?php

/**
 * Copyright OpenSearch Contributors
 * SPDX-License-Identifier: Apache-2.0
 */

require_once __DIR__ . '/vendor/autoload.php';

// setup
$service = getenv("SERVICE") ?: 'es';

$client = (new \OpenSearch\ClientBuilder())
    ->setHosts([getenv("ENDPOINT")])
    ->setSigV4Service($service)
    ->setSigV4Region(getenv("AWS_REGION"))
    ->setSigV4CredentialProvider(true)
    ->build();

if ($service != 'aoss') {
    $info = $client->info();
    echo "{$info['version']['distribution']}: {$info['version']['number']}\n";
}

$indexName = "movies";

// create an index 
if (!$client->indices()->exists(['index' => $indexName])) {
    $client->indices()->create(['index' => $indexName]);
}

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
    print_r($result['hits']['hits'][0]);
}

// delete a single document
$client->delete(['index' => $indexName, 'id' => 1]);
// delete index
$client->indices()->delete(['index' => $indexName]);

?>