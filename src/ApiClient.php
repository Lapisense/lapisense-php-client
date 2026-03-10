<?php

namespace Lapisense\PHPClient;

/**
 * API client for the Lapisense licensing REST API.
 *
 * Implements [TS 10.2]. All methods for both licensed and free flows.
 * PHP 7.4 compatible — no constructor promotion, no readonly, no union types.
 */
final class ApiClient
{
    /** @var string */
    private $storeUrl;

    /** @var string */
    private $productUuid;

    /** @var HttpClientInterface */
    private $httpClient;

    /**
     * @param string $storeUrl Base URL of the WooCommerce store.
     * @param string $productUuid Product UUID.
     * @param HttpClientInterface $httpClient HTTP client implementation.
     */
    public function __construct($storeUrl, $productUuid, HttpClientInterface $httpClient)
    {
        $this->storeUrl = rtrim($storeUrl, '/');
        $this->productUuid = $productUuid;
        $this->httpClient = $httpClient;
    }

    /**
     * Activate a license.
     *
     * @param string $licenseKey
     * @param string $siteUrl
     * @return array<string, mixed>|null
     */
    public function activate($licenseKey, $siteUrl)
    {
        return $this->httpClient->post(
            $this->endpoint('/licensing/activate'),
            array(
                'license_key'  => $licenseKey,
                'product_uuid' => $this->productUuid,
                'site_url'     => $siteUrl,
            )
        );
    }

    /**
     * Deactivate an activation.
     *
     * @param string $activationUuid
     * @return array<string, mixed>|null
     */
    public function deactivate($activationUuid)
    {
        return $this->httpClient->delete(
            $this->endpoint('/licensing/activations/' . $activationUuid),
            array('product_uuid' => $this->productUuid)
        );
    }

    /**
     * Check for updates.
     *
     * @param string $currentVersion
     * @param string|null $activationUuid Optional activation UUID.
     * @return array<string, mixed>|null
     */
    public function checkUpdate($currentVersion, $activationUuid = null)
    {
        $params = array(
            'product_uuid'    => $this->productUuid,
            'current_version' => $currentVersion,
        );

        if ($activationUuid !== null) {
            $params['activation_uuid'] = $activationUuid;
        }

        return $this->httpClient->get(
            $this->endpoint('/licensing/update-check'),
            $params
        );
    }

    /**
     * Get product information.
     *
     * @return array<string, mixed>|null
     */
    public function getProductInfo()
    {
        return $this->httpClient->get(
            $this->endpoint('/licensing/products/' . $this->productUuid . '/information')
        );
    }

    /**
     * Build a full API endpoint URL.
     *
     * @param string $path
     * @return string
     */
    private function endpoint($path)
    {
        return $this->storeUrl . '/wp-json/lapisense/v1' . $path;
    }
}
