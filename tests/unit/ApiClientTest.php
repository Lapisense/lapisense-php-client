<?php

namespace Lapisense\PHPClient\Tests\Unit;

use Lapisense\PHPClient\ApiClient;
use Lapisense\PHPClient\HttpClientInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Lapisense\PHPClient\ApiClient
 */
class ApiClientTest extends TestCase
{
    /** @var string */
    private $storeUrl = 'https://store.example.com';

    /** @var string */
    private $productUuid = '550e8400-e29b-41d4-a716-446655440000';

    /** @var string */
    private $baseEndpoint = 'https://store.example.com/wp-json/lapisense/v1';

    public function testEndpointBuildsCorrectBaseUrl(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with($this->baseEndpoint . '/licensing/products/' . $this->productUuid . '/information')
            ->willReturn(array('name' => 'Test Product'));

        $client = new ApiClient($this->storeUrl, $this->productUuid, $httpClient);
        $client->getProductInfo();
    }

    public function testEndpointStripsTrailingSlashFromStoreUrl(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with($this->baseEndpoint . '/licensing/products/' . $this->productUuid . '/information')
            ->willReturn(array('name' => 'Test Product'));

        $client = new ApiClient($this->storeUrl . '/', $this->productUuid, $httpClient);
        $client->getProductInfo();
    }

    public function testActivateCallsPostWithCorrectUrlAndBody(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('post')
            ->with(
                $this->baseEndpoint . '/licensing/activate',
                array(
                    'license_key'  => 'ABCD-1234',
                    'product_uuid' => $this->productUuid,
                    'site_url'     => 'https://client.example.com',
                )
            )
            ->willReturn(array('activation_uuid' => 'act-uuid'));

        $client = new ApiClient($this->storeUrl, $this->productUuid, $httpClient);
        $client->activate('ABCD-1234', 'https://client.example.com');
    }

    public function testActivateReturnsArrayOnSuccess(): void
    {
        $expected = array('activation_uuid' => 'act-uuid', 'status' => 'active');
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('post')->willReturn($expected);

        $client = new ApiClient($this->storeUrl, $this->productUuid, $httpClient);
        $result = $client->activate('ABCD-1234', 'https://client.example.com');

        $this->assertSame($expected, $result);
    }

    public function testActivateReturnsNullOnFailure(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('post')->willReturn(null);

        $client = new ApiClient($this->storeUrl, $this->productUuid, $httpClient);
        $result = $client->activate('ABCD-1234', 'https://client.example.com');

        $this->assertNull($result);
    }

    public function testDeactivateCallsDeleteWithCorrectUrlAndParams(): void
    {
        $activationUuid = 'act-uuid-123';
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('delete')
            ->with(
                $this->baseEndpoint . '/licensing/activations/' . $activationUuid,
                array('product_uuid' => $this->productUuid)
            )
            ->willReturn(array('deactivated' => true));

        $client = new ApiClient($this->storeUrl, $this->productUuid, $httpClient);
        $client->deactivate($activationUuid);
    }

    public function testDeactivateReturnsNullOnFailure(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('delete')->willReturn(null);

        $client = new ApiClient($this->storeUrl, $this->productUuid, $httpClient);
        $result = $client->deactivate('act-uuid-123');

        $this->assertNull($result);
    }

    public function testCheckUpdateCallsGetWithCorrectUrlAndParams(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with(
                $this->baseEndpoint . '/licensing/update-check',
                array(
                    'product_uuid'    => $this->productUuid,
                    'current_version' => '1.0.0',
                    'activation_uuid' => 'act-uuid-123',
                )
            )
            ->willReturn(array('version' => '1.1.0'));

        $client = new ApiClient($this->storeUrl, $this->productUuid, $httpClient);
        $client->checkUpdate('act-uuid-123', '1.0.0');
    }

    public function testCheckUpdateReturnsNullWhenNoUpdateAvailable(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('get')->willReturn(null);

        $client = new ApiClient($this->storeUrl, $this->productUuid, $httpClient);
        $result = $client->checkUpdate('act-uuid-123', '1.0.0');

        $this->assertNull($result);
    }

    public function testCheckFreeUpdateCallsGetWithoutActivationUuid(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with(
                $this->baseEndpoint . '/licensing/update-check',
                array(
                    'product_uuid'    => $this->productUuid,
                    'current_version' => '1.0.0',
                )
            )
            ->willReturn(array('version' => '1.1.0'));

        $client = new ApiClient($this->storeUrl, $this->productUuid, $httpClient);
        $client->checkFreeUpdate('1.0.0');
    }

    public function testCheckFreeUpdateReturnsArrayOnSuccess(): void
    {
        $expected = array('version' => '1.1.0', 'download_url' => 'https://store.example.com/download');
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('get')->willReturn($expected);

        $client = new ApiClient($this->storeUrl, $this->productUuid, $httpClient);
        $result = $client->checkFreeUpdate('1.0.0');

        $this->assertSame($expected, $result);
    }

    public function testGetProductInfoCallsGetWithCorrectUrl(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->expects($this->once())
            ->method('get')
            ->with($this->baseEndpoint . '/licensing/products/' . $this->productUuid . '/information')
            ->willReturn(array('name' => 'Test Product'));

        $client = new ApiClient($this->storeUrl, $this->productUuid, $httpClient);
        $client->getProductInfo();
    }

    public function testGetProductInfoReturnsNullOnFailure(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $httpClient->method('get')->willReturn(null);

        $client = new ApiClient($this->storeUrl, $this->productUuid, $httpClient);
        $result = $client->getProductInfo();

        $this->assertNull($result);
    }
}
