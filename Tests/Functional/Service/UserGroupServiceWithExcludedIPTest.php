<?php

namespace Hoogi91\AccessRestriction\Tests\Functional\Service;

class UserGroupServiceWithExcludedIPTest extends AbstractUserGroupService
{
    public function getRemoteAddress(): string
    {
        return '192.168.178.123';
    }

    public function testEvaluatingRestrictionGroups(): void
    {
        $restrictedGroups = $this->userGroupService->getRestrictionGroups();
        $this->assertCount(0, $restrictedGroups);
    }
}
