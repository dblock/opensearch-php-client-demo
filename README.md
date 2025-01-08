# OpenSearch PHP Client Demo

Makes requests to Amazon OpenSearch using the [OpenSearch PHP Client](https://github.com/opensearch-project/opensearch-php).

## Prerequisites

### PHP

Install [PHP](https://www.php.net/manual/en/install.php). YMMV.

```
$ php --version
PHP 8.2.0 (cli) (built: Dec  9 2022 16:19:06) (NTS)
```

### Composer

Install [Composer](https://getcomposer.org/download/). Again, YMMV.

```
$ composer --version
Composer version 2.5.1 2022-12-22 15:33:54
```

## Running

Create an OpenSearch domain in (AWS) which support IAM based AuthN/AuthZ.

```
export AWS_ACCESS_KEY_ID=
export AWS_SECRET_ACCESS_KEY=
export AWS_SESSION_TOKEN=
export AWS_REGION=us-west2

export ENDPOINT=https://....us-west-2.es.amazonaws.com
export SERVICE=es # use aoss for OpenSearch Serverless

$ composer install
$ composer run demo
```

This will output the version of OpenSearch and a search result.

```
opensearch: 2.3.0
Array
(
    [_index] => movies
    [_id] => 1
    [_score] => 0.2876821
    [_source] => Array
        (
            [title] => Moneyball
            [director] => Bennett Miller
            [year] => 2011
        )

)
```

The [code](index.php) will create an index, add a document to it, search, then cleanup.

Use `composer run demo:symfony` to exercise [index-symfony.php](index-symfony.php) and `composer run demo:guzzle` to exercise [index-guzzle.php](index-guzzle.php). Use `composer run demo:json` to run the [raw JSON sample](json.php). 

## Next Version

To test the next version of the client use this in [composer.json](composer.json).

```json
  "require": {
    "opensearch-project/opensearch-php": "dev-main",
  },
  "repositories": [{
    "url": "https://github.com/opensearch-project/opensearch-php",
    "type": "git"
  }]
```

## License 

This project is licensed under the [Apache v2.0 License](LICENSE.txt).

## Copyright

Copyright OpenSearch Contributors. See [NOTICE](NOTICE.txt) for details.
