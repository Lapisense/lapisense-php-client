<?php

namespace Lapisense\PHPClient;

/**
 * HTTP client interface for making API requests.
 *
 * All methods return decoded JSON as an associative array, or null on error/non-2xx.
 * Implements [TS 10.1].
 */
interface HttpClientInterface
{
    /**
     * @param array<string, string> $params Query parameters.
     * @return array<string, mixed>|null
     */
    public function get($url, $params = array());

    /**
     * @param array<string, mixed> $body Request body.
     * @return array<string, mixed>|null
     */
    public function post($url, $body);

    /**
     * @param array<string, string> $params Query parameters.
     * @return array<string, mixed>|null
     */
    public function delete($url, $params = array());
}
