<?php

namespace Hoogi91\AccessRestriction\Tests\Unit\Service;

use Hoogi91\AccessRestriction\Service\CacheService;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Core\Cache\Backend\TransientMemoryBackend;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;
use TYPO3\CMS\Core\Information\Typo3Version;

/**
 * Class CacheServiceTest
 * @package Hoogi91\AccessRestriction\Tests\Unit\Service
 */
class CacheServiceTest extends UnitTestCase
{

    /**
     * @var CacheService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cacheService;

    /**
     * @var CacheService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $invalidCacheService;

    protected function setUp()
    {
        parent::setUp();

        $this->cacheService = $this->getMockBuilder(CacheService::class)->setMethods([
            'getCache',
        ])->getMock();

        $cacheName = class_exists(Typo3Version::class) && (new Typo3Version())->getMajorVersion() >= 10
            ? CacheService::CACHE_NAME
            : 'cache_'. CacheService::CACHE_NAME;

        // only instantiate cache manager for this test class
        // cause otherwise singleton interface will set this for all requests/tests
        $cacheManager = new CacheManager();
        $cacheManager->setCacheConfigurations([
            $cacheName => [
                'backend'  => TransientMemoryBackend::class,
                'frontend' => VariableFrontend::class,
            ],
        ]);
        $this->cacheService->method('getCache')->willReturn($cacheManager->getCache($cacheName));

        $this->invalidCacheService = $this->getMockBuilder(CacheService::class)->setMethods([
            'getCache',
        ])->getMock();
        $this->invalidCacheService->method('getCache')->willReturnCallback(function () use ($cacheManager) {
            return $cacheManager->getCache('not-existing-cache');
        });
    }

    /**
     * @test
     */
    public function testSetterAndGetterByIdentifier()
    {
        // test non-existing identifier
        $this->assertFalse($this->cacheService->get('lorem'));

        // test getting identifier after setting
        $this->cacheService->set('lorem', 'ipsum');
        $this->assertEquals('ipsum', $this->cacheService->get('lorem'));
    }

    /**
     * @depends testSetterAndGetterByIdentifier
     * @test
     */
    public function testCacheFlushing()
    {
        // test if set cache entry is not available after flushing
        $this->cacheService->set('lorem', 'ipsum');
        $this->cacheService->flush();
        $this->assertFalse($this->cacheService->get('lorem'));
    }

    /**
     * @test
     */
    public function testInvalidCacheName()
    {
        // getter should always return false
        $this->assertFalse($this->invalidCacheService->get('lorem'));
        // setter should do nothing and simply return
        $this->invalidCacheService->set('lorem', 'ipsum');
        // flush should also do nothing and simply return void
        $this->invalidCacheService->flush();
    }
}
