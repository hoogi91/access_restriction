<?php

namespace Hoogi91\AccessRestriction\Service;

class UserGroupService
{
    private RestrictionService $restrictionService;

    private IpValidationService $ipValidationService;

    public function __construct(RestrictionService $restrictionService, IpValidationService $ipValidationService)
    {
        $this->restrictionService = $restrictionService;
        $this->ipValidationService = $ipValidationService;
    }

    public function getRestrictionGroups(): array
    {
        $groups = [];

        // get all groups that validate on ip
        $ipValidationGroups = $this->restrictionService->getIpAccessRestrictions();
        if (!empty($ipValidationGroups)) {
            foreach ($ipValidationGroups as $groupId => $restrictions) {
                if ($this->ipValidationService->findInList($restrictions) === true) {
                    $groups[] = $groupId;
                }
            }
        }

        // iterate and add all validated groups to users group list
        return array_filter($groups);
    }
}
