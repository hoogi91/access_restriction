<?php

namespace Hoogi91\AccessRestriction\Hook;

use Hoogi91\AccessRestriction\Service\CacheService;

class DataHandler
{
    private CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    // @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function processDatamap_afterDatabaseOperations($status, $table, $id, $fields = [])
    {
        if ($table === 'fe_groups' && isset($fields['tx_accessrestriction_restrictions'])) {
            $this->cacheService->flush();
        }
    }
}
