<?php

namespace Hoogi91\AccessRestriction\Service;

use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;

class CacheService
{
    private ?FrontendInterface $cache = null;

    public function __construct(CacheManager $cacheManager)
    {
        try {
            $this->cache = $cacheManager->getCache('accessrestriction');
        } catch (NoSuchCacheException $e) {
            // ignore exception when cache is not found
        }
    }

    public function get(string $identifier)
    {
        if ($this->cache === null) {
            return false;
        }
        return $this->cache->get($identifier);
    }

    /**
     * @param mixed $data
     */
    public function set(string $identifier, $data): void
    {
        if ($this->cache !== null) {
            $this->cache->set($identifier, $data);
        }
    }

    public function flush(): void
    {
        if ($this->cache !== null) {
            $this->cache->flush();
        }
    }
}
