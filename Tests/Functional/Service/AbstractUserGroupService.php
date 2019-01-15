<?php

namespace Hoogi91\AccessRestriction\Tests\Functional\Service;

use Hoogi91\AccessRestriction\Service\UserGroupService;
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class AbstractUserGroupServiceTest
 * @package Hoogi91\AccessRestriction\Tests\Functional\Service
 */
abstract class AbstractUserGroupService extends FunctionalTestCase
{

    /**
     * @var UserGroupService
     */
    protected $userGroupService;

    /**
     * @var array load access_restriction to test
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/access_restriction',
    ];

    /**
     * Sets up this test case.
     */
    protected function setUp()
    {
        parent::setUp();

        // set remote address to match our ip ranges inside fixtures
        $_SERVER['REMOTE_ADDR'] = $this->getRemoteAddress();
        $this->importDataSet(__DIR__ . '/../../Fixtures/fe_groups.xml');

        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->userGroupService = $objectManager->get(UserGroupService::class);
    }

    /**
     * @return string
     */
    abstract public function getRemoteAddress(): string;

    /**
     * @test
     */
    abstract public function testEvaluatingRestrictionGroups();

}