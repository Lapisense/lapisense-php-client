# Lapisense PHP Client

Generic PHP client for the Lapisense licensing REST API. Framework-agnostic — requires an `HttpClientInterface` implementation for HTTP transport.

## Requirements

- PHP 7.4+

## Installation

```bash
composer require lapisense/php-client
```

## Usage

```php
use Lapisense\PHPClient\ApiClient;

$client = new ApiClient($storeUrl, $productUuid, $httpClient);

$client->activate($licenseKey, $siteUrl);
$client->deactivate($activationUuid);
$client->checkUpdate($activationUuid, $currentVersion);
$client->checkFreeUpdate($currentVersion);
$client->getProductInfo();
```

The `$httpClient` parameter must implement `Lapisense\PHPClient\HttpClientInterface`. See [lapisense/wordpress-client](https://github.com/Lapisense/lapisense-wordpress-client) for a WordPress implementation.

## License

MIT
