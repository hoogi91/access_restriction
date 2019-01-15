<?php

namespace Hoogi91\AccessRestriction\Tests\Functional\Service;

/**
 * Class UserGroupServiceWithExcludedIPTest
 * @package Hoogi91\AccessRestriction\Tests\Functional\Service
 */
class UserGroupServiceWithExcludedIPTest extends AbstractUserGroupService
{

    /**
     * @return string
     */
    public function getRemoteAddress(): string
    {
        return '192.168.178.123';
    }

    /**
     * @test
     */
    public function testEvaluatingRestrictionGroups()
    {
        $restrictedGroups = $this->userGroupService->getRestrictionGroups();
        $this->assertInternalType('array', $restrictedGroups);
        $this->assertCount(0, $restrictedGroups);
    }
}