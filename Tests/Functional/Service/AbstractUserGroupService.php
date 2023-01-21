<?php

namespace Hoogi91\AccessRestriction\Tests\Functional\Service;

use Hoogi91\AccessRestriction\Service\UserGroupService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

abstract class AbstractUserGroupService extends FunctionalTestCase
{
    protected UserGroupService $userGroupService;

    protected $testExtensionsToLoad = [
        'typo3conf/ext/access_restriction'
    ];

    protected function setUp(): void
    {
        parent::setUp();

        // set remote address to match our ip ranges inside fixtures
        $_SERVER['REMOTE_ADDR'] = $this->getRemoteAddress();
        $this->importDataSet(__DIR__ . '/../../Fixtures/fe_groups.xml');

        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->userGroupService = $objectManager->get(UserGroupService::class);
    }

    abstract public function getRemoteAddress(): string;

    abstract public function testEvaluatingRestrictionGroups(): void;
}
