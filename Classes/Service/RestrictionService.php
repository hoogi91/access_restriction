<?php

namespace Hoogi91\AccessRestriction\Service;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RestrictionService
{
    private CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    public function getIpAccessRestrictions(): array
    {
        return array_filter(array_column(
            $this->getAccessRestrictedFrontendGroups(),
            'tx_accessrestriction_restrictions',
            'uid'
        ));
    }

    protected function getAccessRestrictedFrontendGroups(): array
    {
        // if cache exists => return it immediately
        if (($result = $this->cacheService->get('frontendGroups')) !== false) {
            return $result;
        }

        // get query builder with default restrictions and fetch complete result
        $result = $this->getQueryWithDefaultRestrictions();
        if ((new Typo3Version)->getMajorVersion() < 11) {
            /**
             * because this is legacy code
             * @phpstan-ignore-next-line
             * @psalm-suppress UndefinedInterfaceMethod
             */
            $result = $result->execute()->fetchAll();
        } else {
            $result = $result->executeQuery()->fetchAllAssociative();
        }


        // save to cache and return
        $this->cacheService->set('frontendGroups', $result);
        return $result;
    }

    protected function getQueryWithDefaultRestrictions(): QueryBuilder
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('fe_groups');
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

        return $queryBuilder->select('*')->from('fe_groups')->where(...$restrictions);
    }
}
