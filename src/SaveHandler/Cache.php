<?php

namespace Laminas\Session\SaveHandler;

use Laminas\Cache\Exception\ExceptionInterface;
use Laminas\Cache\Storage\ClearExpiredInterface as ClearExpiredCacheStorage;
use Laminas\Cache\Storage\StorageInterface as CacheStorage;
use ReturnTypeWillChange;

/**
 * Cache session save handler
 *
 * @see ReturnTypeWillChange
 */
class Cache implements SaveHandlerInterface
{
    /**
     * Session Save Path
     */
    protected ?string $sessionSavePath = null;

    /**
     * Session Name
     */
    protected ?string $sessionName = null;

    /**
     * Constructor
     */
    public function __construct(protected CacheStorage $cacheStorage)
    {
    }

    /**
     * Open Session
     */
    #[ReturnTypeWillChange]
    public function open(string $path, string $name): bool
    {
        // @todo figure out if we want to use these
        $this->sessionSavePath = $path;
        $this->sessionName     = $name;

        return true;
    }

    /**
     * Close session
     */
    #[ReturnTypeWillChange]
    public function close(): bool
    {
        return true;
    }

    /**
     * Read session data
     *
     * @param non-empty-string $id
     * @throws ExceptionInterface
     */
    #[ReturnTypeWillChange]
    public function read(string $id): string
    {
        return (string) $this->getCacheStorage()->getItem($id);
    }

    /**
     * Write session data
     *
     * @param non-empty-string $id
     * @throws ExceptionInterface
     */
    #[ReturnTypeWillChange]
    public function write(string $id, string $data): bool
    {
        return $this->getCacheStorage()->setItem($id, $data);
    }

    /**
     * Destroy session
     *
     * @param non-empty-string $id
     * @throws ExceptionInterface
     */
    #[ReturnTypeWillChange]
    public function destroy(string $id): bool
    {
        $this->getCacheStorage()->getItem($id, $exists);
        if (! $exists) {
            return true;
        }

        return $this->getCacheStorage()->removeItem($id);
    }

    /**
     * Garbage Collection
     *
     * @phpcs:disable WebimpressCodingStandard.NamingConventions.ValidVariableName.NotCamelCaps
     */
    #[ReturnTypeWillChange]
    public function gc(int $max_lifetime): bool
    {
        $cache = $this->getCacheStorage();
        if ($cache instanceof ClearExpiredCacheStorage) {
            return $cache->clearExpired();
        }
        return true;
    }

    /**
     * Get cache storage
     */
    public function getCacheStorage(): CacheStorage
    {
        return $this->cacheStorage;
    }
}
