<?php

namespace Lapisense\PHPClient;

/**
 * Key-value storage interface for persisting client state.
 *
 * Implements [TS 10.1].
 */
interface StorageInterface
{
    /**
     * @return string|null
     */
    public function get(string $key);

    /**
     * @return void
     */
    public function set(string $key, string $value);

    /**
     * @return void
     */
    public function delete(string $key);
}
