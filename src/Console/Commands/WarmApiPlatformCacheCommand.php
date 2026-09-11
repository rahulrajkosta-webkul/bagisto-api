<?php

namespace Webkul\BagistoApi\Console\Commands;

use ApiPlatform\GraphQl\Type\FieldsBuilderEnumInterface;
use ApiPlatform\GraphQl\Type\TypesContainerInterface;
use ApiPlatform\GraphQl\Type\TypesFactoryInterface;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\Factory\ResourceNameCollectionFactoryInterface;
use Illuminate\Console\Command;
use Webkul\BagistoApi\GraphQl\QueryScopedSchemaBuilder;

class WarmApiPlatformCacheCommand extends Command
{
    protected $signature = 'bagisto-api-platform:warm-cache';

    protected $description = 'Pre-build the API Platform resource-metadata cache so the first request after a deploy/cache-clear does not pay the full metadata rebuild cost.';

    public function handle(): int
    {
        $this->warnWhenDebugDisablesTheCache();

        $nameFactory = app(ResourceNameCollectionFactoryInterface::class);
        $metadataFactory = app(ResourceMetadataCollectionFactoryInterface::class);

        $count = 0;
        $failed = 0;

        foreach ($nameFactory->create() as $resourceClass) {
            try {
                $metadataFactory->create($resourceClass);
                $count++;
            } catch (\Throwable $e) {
                $failed++;
                $this->warn(sprintf('Skipped "%s": %s', $resourceClass, $e->getMessage()));
            }
        }

        $this->info(sprintf('Warmed metadata cache for %d resource(s)%s.', $count, $failed ? sprintf(' (%d skipped)', $failed) : ''));

        try {
            $scopedBuilder = new QueryScopedSchemaBuilder(
                $nameFactory,
                $metadataFactory,
                app(TypesFactoryInterface::class),
                app(TypesContainerInterface::class),
                app(FieldsBuilderEnumInterface::class),
            );

            $this->info(sprintf('Pre-built the GraphQL query-scope map (%d fields).', $scopedBuilder->warmFieldMap()));
        } catch (\Throwable $e) {
            $this->warn(sprintf('Could not pre-build the GraphQL query-scope map: %s', $e->getMessage()));
        }

        return self::SUCCESS;
    }

    /**
     * API Platform stores property metadata in the per-request `array` store whenever
     * `app.debug` is true, so warming it writes to a bag that is thrown away when the
     * process ends. Every request then rebuilds the metadata for every resource, which on
     * a surface this size costs seconds per request and can exhaust max_execution_time.
     * Without this notice the command reports a warmed cache that does not exist.
     */
    protected function warnWhenDebugDisablesTheCache(): void
    {
        if (! config('app.debug')) {
            return;
        }

        $this->components->warn('APP_DEBUG is true, so API Platform is using the per-request "array" metadata store and nothing warmed below survives the process. Set APP_DEBUG=false and re-run this command, or every request will rebuild the metadata for every resource.');

        $this->newLine();
    }
}
