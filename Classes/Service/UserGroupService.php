<?php

namespace Hoogi91\AccessRestriction\Service;

/**
 * Class UserGroupService
 * @package Hoogi91\AccessRestriction\Service
 */
class UserGroupService
{

    /**
     * @var \Hoogi91\AccessRestriction\Service\RestrictionService
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $restrictionService;

    /**
     * @var \Hoogi91\AccessRestriction\Service\IpValidationService
     * @TYPO3\CMS\Extbase\Annotation\Inject
     */
    protected $ipValidationService;

    /**
     * adds frontend user groups if restriction matches for current user
     */
    public function getRestrictionGroups()
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
