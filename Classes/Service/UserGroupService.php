<?php

declare(strict_types=1);

namespace Hoogi91\AccessRestriction\Service;

class UserGroupService
{
    public function __construct(
        private readonly RestrictionService $restrictionService,
        private readonly IpValidationService $ipValidationService
    ) {
    }

    /**
     * @return array<int>
     */
    public function getRestrictionGroups(): array
    {
        $groups = [];

        // get all groups that validate on ip
        $ipValidationGroups = $this->restrictionService->getIpAccessRestrictions();
        if (!empty($ipValidationGroups)) {
            foreach ($ipValidationGroups as $groupId => $restrictions) {
                if (
                    (is_string($restrictions) === true || is_array($restrictions) === true)
                    && $this->ipValidationService->findInList($restrictions) === true
                ) {
                    $groups[] = $groupId;
                }
            }
        }

        // iterate and add all validated groups to users group list
        return array_filter($groups);
    }
}
