HTTP Multipart Components
=========================

[![Build Status](https://travis-ci.org/dickolsson/http-multipart.svg?branch=1.0)](https://travis-ci.org/dickolsson/http-multipart)

This project contains PHP components to handle multipart requests and responses in PHP.

The goal of this project is to eventually file these classes as upstream patches (hence borrowing the upstream namespaces).

## Client request

To be done.

## Client response

Client responses are implemented as a [Guzzle](http://guzzlephp.org/) message factory.

```php
$client = new Client(['message_factory' => new MultipartMessageFactory()]);
$respone = $client->get('http://example.com/');

echo $respone->getBody(); // Outputs first part
echo $respone->getBody(); // Outputs second part
```


## Server response

Server responses are implemented as an extension of
[Symfony HTTP Foundation](http://symfony.com/components/HttpFoundation).

```php
$request = new Request();
$response = new MultipartResponse(
    new Response('hello', 200, ['Language' => 'en']),
    new Response('hejsan', 200, ['Language' => 'se'])
);

$response->prepare($request);
$response->send(); // Will send all parts as one multipart response.
```
