<?php

namespace Hoogi91\AccessRestriction\Hook;

use Hoogi91\AccessRestriction\Service\UserGroupService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

class UserAspect extends \TYPO3\CMS\Core\Context\UserAspect
{

    public function getGroupIds(): array
    {
        $groups = parent::getGroupIds();
        if ($this->user instanceof FrontendUserAuthentication) {
            /** @var UserGroupService $userGroupService */
            $userGroupService = GeneralUtility::makeInstance(ObjectManager::class)->get(UserGroupService::class);

            // push restricted groups to current group id's
            $groups = array_unique(array_merge($groups, $userGroupService->getRestrictionGroups()));
        }
        return $groups;
    }
}
