<?php

namespace Hoogi91\AccessRestriction\Hook;

use Hoogi91\AccessRestriction\Service\UserGroupService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

/**
 * Class UserAspect
 * @package Hoogi91\AccessRestriction\Hook
 */
class UserAspect extends \TYPO3\CMS\Core\Context\UserAspect
{

    /**
     * override to add restricted user groups
     *
     * @return array
     */
    public function getGroupIds(): array
    {
        $groups = parent::getGroupIds();
        if ($this->user instanceof FrontendUserAuthentication) {
            /** @var ObjectManager $objectManager */
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            /** @var UserGroupService $userGroupService */
            $userGroupService = $objectManager->get(UserGroupService::class);

            // push restricted groups to current group id's
            $groups = array_unique(array_merge($groups, $userGroupService->getRestrictionGroups()));
        }
        return $groups;
    }
}
