<?php

namespace Hoogi91\AccessRestriction\Tests\Unit\Service;

use Hoogi91\AccessRestriction\Service\RestrictionService;
use Nimut\TestingFramework\TestCase\UnitTestCase;

/**
 * Class RestrictionServiceTest
 * @package Hoogi91\AccessRestriction\Tests\Unit\Service
 */
class RestrictionServiceTest extends UnitTestCase
{

    const DATABASE_RESULT = [
        ['uid' => 1, 'tx_accessrestriction_restrictions' => '192.168.178.1'],
        ['uid' => 2, 'tx_accessrestriction_restrictions' => ''],
        ['uid' => 3, 'tx_accessrestriction_restrictions' => '192.168.178.1/24'],
        ['uid' => 4, 'tx_accessrestriction_restrictions' => '192.168.178.1-192.168.178.5'],
        ['uid' => 5, 'tx_accessrestriction_restrictions' => ''],
        [
            'uid'                               => 6,
            'tx_accessrestriction_restrictions' => '192.168.178.1/24' . PHP_EOL . '192.168.178.1' . PHP_EOL . '192.168.178.1-192.168.178.5',
        ],
    ];

    /**
     * @test
     */
    public function testEvaluatingIpAccessRestrictions()
    {
        /** @var RestrictionService|\PHPUnit_Framework_MockObject_MockObject $restrictionService */
        $restrictionService = $this->getMockBuilder(RestrictionService::class)->setMethods([
            'getAccessRestrictedFrontendGroups',
        ])->getMock();
        $restrictionService->method('getAccessRestrictedFrontendGroups')->willReturn(self::DATABASE_RESULT);

        $accessRestrictions = $restrictionService->getIpAccessRestrictions();
        $this->assertInternalType('array', $accessRestrictions);
        $this->assertCount(4, $accessRestrictions);
        $this->assertArraySubset([1, 3, 4, 6], array_keys($accessRestrictions));
        $this->assertEquals('192.168.178.1-192.168.178.5', $accessRestrictions[4]);
    }
}