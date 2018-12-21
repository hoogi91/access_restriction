<?php

namespace Hoogi91\AccessRestriction\Hook;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use Hoogi91\AccessRestriction\Service\CacheService;

/**
 * Class DataHandler
 * @package Hoogi91\AccessRestriction\Hook
 */
class DataHandler
{

    /**
     * @var \Hoogi91\AccessRestriction\Service\CacheService
     */
    protected $cacheService;

    /**
     * @param string $status
     * @param string $table
     * @param int    $id
     * @param array  $fields
     */
    public function processDatamap_afterDatabaseOperations($status, $table, $id, $fields = [])
    {
        if ($table === 'fe_groups' && isset($fields['tx_accessrestriction_restrictions'])) {
            $this->getCacheService()->flush();
        }
    }

    /**
     * @return object|CacheService
     */
    protected function getCacheService()
    {
        if (!isset($this->cacheService)) {
            /** @var ObjectManager $objectManager */
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            return $this->cacheService = $objectManager->get(CacheService::class);
        }
        return $this->cacheService;
    }
}
