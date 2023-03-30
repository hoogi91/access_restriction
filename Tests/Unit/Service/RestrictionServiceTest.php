<?php

declare(strict_types=1);

namespace Hoogi91\AccessRestriction\Tests\Unit\Service;

use Hoogi91\AccessRestriction\Service\CacheService;
use Hoogi91\AccessRestriction\Service\RestrictionService;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

use const PHP_EOL;

class RestrictionServiceTest extends UnitTestCase
{
    private const DATABASE_RESULT = [
        ['uid' => 1, 'tx_accessrestriction_restrictions' => '192.168.178.1'],
        ['uid' => 2, 'tx_accessrestriction_restrictions' => ''],
        ['uid' => 3, 'tx_accessrestriction_restrictions' => '192.168.178.1/24'],
        ['uid' => 4, 'tx_accessrestriction_restrictions' => '192.168.178.1-192.168.178.5'],
        ['uid' => 5, 'tx_accessrestriction_restrictions' => ''],
        [
            'uid' => 6,
            'tx_accessrestriction_restrictions' => '192.168.178.1/24' . PHP_EOL .
                '192.168.178.1' . PHP_EOL .
                '192.168.178.1-192.168.178.5',
        ],
    ];

    public function testEvaluatingIpAccessRestrictions(): void
    {
        /** @var RestrictionService&MockObject $restrictionService */
        $restrictionService = $this->getMockBuilder(RestrictionService::class)
            ->setConstructorArgs([$this->createMock(CacheService::class)])
            ->setMethods(['getAccessRestrictedFrontendGroups'])
            ->getMock();
        $restrictionService->method('getAccessRestrictedFrontendGroups')->willReturn(self::DATABASE_RESULT);

        $accessRestrictions = $restrictionService->getIpAccessRestrictions();
        $this->assertCount(4, $accessRestrictions);
        $this->assertEquals([1, 3, 4, 6], array_keys($accessRestrictions));
        $this->assertEquals('192.168.178.1-192.168.178.5', $accessRestrictions[4]);
    }
}
