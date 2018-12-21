<?php

namespace Hoogi91\AccessRestriction\Service;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class RestrictionService
 * @package Hoogi91\AccessRestriction\Service
 */
class RestrictionService
{
    const TABLE = 'fe_groups';
    const CACHE_IDENTIFIER = 'frontendGroups';

    /**
     * @var \Hoogi91\AccessRestriction\Service\CacheService
     * @inject
     */
    protected $cacheService;

    /**
     * @return array
     */
    public function getIpAccessRestrictions()
    {
        return array_filter(array_column(
            $this->getAccessRestrictedFrontendGroups(),
            'tx_accessrestriction_restrictions',
            'uid'
        ));
    }

    /**
     * @return array
     */
    protected function getAccessRestrictedFrontendGroups()
    {
        // if cache exists => return it immediately
        if (($result = $this->cacheService->get(static::CACHE_IDENTIFIER)) !== false) {
            return $result;
        }

        // get query builder with default restrictions and fetch complete result
        $result = $this->getQueryWithDefaultRestrictions()->execute()->fetchAll();

        // save to cache and return
        $this->cacheService->set(static::CACHE_IDENTIFIER, $result);
        return $result;
    }

    /**
     * @return QueryBuilder
     */
    protected function getQueryWithDefaultRestrictions()
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(static::TABLE);
        $queryBuilder->resetQueryParts();
        $queryBuilder->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class))
            ->add(GeneralUtility::makeInstance(HiddenRestriction::class));

        $restrictions = [];
        if (isset($GLOBALS['TCA']['fe_groups']['columns']['tx_extbase_type'])) {
            $restrictions[] = $queryBuilder->expr()->eq(
                'tx_extbase_type',
                $queryBuilder->createNamedParameter('access_restriction')
            );
        }

        return $queryBuilder->select('*')->from(static::TABLE)->where(...$restrictions);
    }
}
