<?php

namespace Hoogi91\AccessRestriction\Tests\Functional\Service;

class UserGroupServiceTest extends AbstractUserGroupService
{
    public function getRemoteAddress(): string
    {
        return '192.168.178.61';
    }

    public function testEvaluatingRestrictionGroups(): void
    {
        $restrictedGroups = $this->userGroupService->getRestrictionGroups();
        $this->assertCount(1, $restrictedGroups);
        $this->assertEquals(4, $restrictedGroups[0]);
    }
}
