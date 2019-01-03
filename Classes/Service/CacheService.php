<?php

namespace Hoogi91\AccessRestriction\Service;

use TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;

/**
 * Class CacheService
 * @package Hoogi91\AccessRestriction\Service
 */
class CacheService
{
    const CACHE_NAME = 'cache_accessrestriction';

    /**
     * @TYPO3\CMS\Extbase\Annotation\Inject
     * @var \TYPO3\CMS\Core\Cache\CacheManager
     */
    protected $cacheManager;

    /**
     * @param string $identifier
     *
     * @return mixed
     */
    public function get($identifier)
    {
        try {
            return $this->getCache()->get($identifier);
        } catch (NoSuchCacheException $e) {
            return false;
        }
    }

    /**
     * @param string $identifier
     * @param mixed  $data
     */
    public function set($identifier, $data)
    {
        try {
            $this->getCache()->set($identifier, $data);
        } catch (NoSuchCacheException $e) {
            // ignore exception when cache is not found
        }
    }

    /**
     * flush cached entries ;)
     */
    public function flush()
    {
        try {
            $this->getCache()->flush();
        } catch (NoSuchCacheException $e) {
            // ignore exception when cache is not found
        }
    }

    /**
     * @return FrontendInterface
     * @throws NoSuchCacheException
     */
    protected function getCache()
    {
        return $this->cacheManager->getCache(static::CACHE_NAME);
    }
}
