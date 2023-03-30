<?php

declare(strict_types=1);

namespace Hoogi91\AccessRestriction\Hook;

use Hoogi91\AccessRestriction\Service\CacheService;

class DataHandler
{
    public function __construct(private readonly CacheService $cacheService)
    {
    }

    /**
     * @phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
     * @param array<mixed> $fields
     */
    public function processDatamap_afterDatabaseOperations(
        string $status,
        string $table,
        mixed $id,
        array $fields
    ): void {
        if ($table === 'fe_groups' && isset($fields['tx_accessrestriction_restrictions'])) {
            $this->cacheService->flush();
        }
    }
}
