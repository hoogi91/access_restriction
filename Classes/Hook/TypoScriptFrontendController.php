<?php

namespace Hoogi91\AccessRestriction\Hook;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use Hoogi91\AccessRestriction\Service\UserGroupService;

/**
 * Class TypoScriptFrontendController
 * @package Hoogi91\AccessRestriction\Hook
 */
class TypoScriptFrontendController extends \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
{

    /**
     * override initializing of user groups to add groups from IP restrictions
     */
    public function initUserGroups()
    {
        parent::initUserGroups();

        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var UserGroupService $userGroupService */
        $userGroupService = $objectManager->get(UserGroupService::class, $this);
        $userGroupService->addRestrictionGroups();
    }
}
