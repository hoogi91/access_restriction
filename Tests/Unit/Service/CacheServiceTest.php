<?php

declare(strict_types=1);

namespace Hoogi91\AccessRestriction\Tests\Unit\Service;

use Hoogi91\AccessRestriction\Service\CacheService;
use TYPO3\CMS\Core\Cache\Backend\TransientMemoryBackend;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\VariableFrontend;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class CacheServiceTest extends UnitTestCase
{
    /**
     * @var bool
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     */
    protected $resetSingletonInstances = true;

    private CacheManager $cacheManager;

    protected function setUp(): void
    {
        parent::setUp();

        // only instantiate cache manager for this test class
        // cause otherwise singleton interface will set this for all requests/tests
        $this->cacheManager = new CacheManager();
        $this->cacheManager->setCacheConfigurations([
            'accessrestriction' => [
                'backend' => TransientMemoryBackend::class,
                'frontend' => VariableFrontend::class,
            ],
        ]);
    }

    public function testSetterAndGetterByIdentifier(): void
    {
        $cacheService = new CacheService($this->cacheManager);
        // test non-existing identifier
        $this->assertFalse($cacheService->get('lorem'));

        // test getting identifier after setting
        $cacheService->set('lorem', 'ipsum');
        $this->assertEquals('ipsum', $cacheService->get('lorem'));
    }

    /**
     * @depends testSetterAndGetterByIdentifier
     */
    public function testCacheFlushing(): void
    {
        // test if set cache entry is not available after flushing
        $cacheService = new CacheService($this->cacheManager);
        $cacheService->set('lorem', 'ipsum');
        $cacheService->flush();
        $this->assertFalse($cacheService->get('lorem'));
    }

    public function testInvalidCacheName(): void
    {
        $invalidCacheService = new CacheService(new CacheManager());
        // getter should always return false
        $this->assertFalse($invalidCacheService->get('lorem'));
        // setter should do nothing and simply return
        $invalidCacheService->set('lorem', 'ipsum');
        // flush should also do nothing and simply return void
        $invalidCacheService->flush();
    }
}
