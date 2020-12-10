<?php

namespace MLL\ConditionalMigrations;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\ConnectionResolverInterface as Resolver;
use Illuminate\Database\Migrations\MigrationRepositoryInterface;
use Illuminate\Database\Migrations\Migrator as LaravelMigrator;
use Illuminate\Filesystem\Filesystem;
use MLL\ConditionalMigrations\Contracts\ConditionalMigration;

class Migrator extends LaravelMigrator
{
    /**
     * @var \Illuminate\Config\Repository
     */
    protected $config;

    public function __construct(
        MigrationRepositoryInterface $repository,
        Resolver $resolver,
        Filesystem $files,
        Dispatcher $dispatcher,
        Repository $config
    ) {
        parent::__construct($repository, $resolver, $files, $dispatcher);

        $this->config = $config;
    }

    protected function runUp($file, $batch, $pretend): void
    {
        // First we will resolve a "real" instance of the migration class from this
        // migration file name. Once we have the instances we can run the actual
        // command such as "up" or "down", or we can just simulate the action.
        $migration = $this->resolve(
            $name = $this->getMigrationName($file)
        );

        if ($pretend) {
            $this->pretendToRun($migration, 'up');

            return;
        }

        if ($migration instanceof ConditionalMigration && ! $this->shouldRunNow($migration)) {
            $this->note("<info>Skipped migrating</info>  {$name}");

            return;
        }

        $this->note("<comment>Migrating:</comment>  {$name}");

        $startTime = microtime(true);

        $this->runMigration($migration, 'up');

        $runTime = round(microtime(true) - $startTime, 2);

        // Once we have run a migrations class, we will log that it was run in this
        // repository so that we don't try to run it next time we do a migration
        // in the application. A migration repository keeps the migrate order.
        $this->repository->log($name, $batch);

        $this->note("<info>Migrated:</info>  {$name} ({$runTime} seconds)");
    }

    protected function shouldRunNow(ConditionalMigration $migration): bool
    {
        $alwaysRun = value($this->config->get('conditional-migrations.always_run'));

        return $alwaysRun || $migration->shouldRun();
    }
}
