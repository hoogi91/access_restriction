<?php

declare(strict_types=1);

namespace Hoogi91\AccessRestriction\Service;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RestrictionService
{
    public function __construct(private readonly CacheService $cacheService)
    {
    }

    /**
     * @return array<mixed>
     */
    public function getIpAccessRestrictions(): array
    {
        return array_filter(array_column(
            $this->getAccessRestrictedFrontendGroups(),
            'tx_accessrestriction_restrictions',
            'uid'
        ));
    }

    /**
     * @return array<mixed>
     */
    protected function getAccessRestrictedFrontendGroups(): array
    {
        // if cache exists => return it
        $result = $this->cacheService->get('frontendGroups');
        if ($result !== false) {
            return (array) $result;
        }

        // get query builder with default restrictions and fetch complete
        $result = (new Typo3Version())->getMajorVersion() < 11
            ? $this->getQueryWithDefaultRestrictions()->execute()->fetchAll()
            : $this->getQueryWithDefaultRestrictions()->executeQuery()->fetchAllAssociative();

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
