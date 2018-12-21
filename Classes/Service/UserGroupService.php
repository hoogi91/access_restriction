<?php

namespace Hoogi91\AccessRestriction\Service;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class UserGroupService
 * @package Hoogi91\AccessRestriction\Service
 */
class UserGroupService
{
    /**
     * @var TypoScriptFrontendController
     */
    protected $controller;

    /**
     * @var \Hoogi91\AccessRestriction\Service\RestrictionService
     * @inject
     */
    protected $restrictionService;

    /**
     * @var \Hoogi91\AccessRestriction\Service\IpValidationService
     * @inject
     */
    protected $ipValidationService;

    /**
     * UserGroupService constructor.
     *
     * @param TypoScriptFrontendController $typoscriptFrontendController
     */
    public function __construct(TypoScriptFrontendController $typoscriptFrontendController)
    {
        $this->controller = $typoscriptFrontendController;
    }

    /**
     * adds frontend user groups if restriction matches for current user
     */
    public function addRestrictionGroups()
    {
        $addGroups = [];

        // get all groups that validate on ip
        $ipValidationGroups = $this->restrictionService->getIpAccessRestrictions();
        if (!empty($ipValidationGroups)) {
            foreach ($ipValidationGroups as $groupId => $restrictions) {
                if ($this->ipValidationService->findInList($restrictions) === true) {
                    $addGroups[] = $groupId;
                }
            }
        }

        // iterate and add all validated groups to users group list
        $addGroups = array_filter($addGroups);
        if (!empty($addGroups)) {
            foreach ($addGroups as $group) {
                if (GeneralUtility::inList($this->controller->gr_list, $group) === false) {
                    $this->controller->gr_list .= ',' . $group;
                }
            }
        }
    }

}
