<?php

namespace Hoogi91\AccessRestriction\Tests\Functional\Service;

/**
 * Class UserGroupServiceTest
 * @package Hoogi91\AccessRestriction\Tests\Functional\Service
 */
class UserGroupServiceTest extends AbstractUserGroupService
{

    /**
     * @return string
     */
    public function getRemoteAddress(): string
    {
        return '192.168.178.61';
    }

    /**
     * @test
     */
    public function testEvaluatingRestrictionGroups()
    {
        $restrictedGroups = $this->userGroupService->getRestrictionGroups();
        $this->assertInternalType('array', $restrictedGroups);
        $this->assertCount(1, $restrictedGroups);
        $this->assertEquals(4, $restrictedGroups[0]);
    }
}