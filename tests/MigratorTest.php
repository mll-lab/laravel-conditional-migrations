<?php

namespace MLL\ConditionalMigrations\Tests;

use Illuminate\Database\Capsule\Manager as CapsuleManager;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use MLL\ConditionalMigrations\Migrator;

class MigratorTest extends TestCase
{
    /**
     * @var \Illuminate\Database\Capsule\Manager
     */
    protected $db;

    /**
     * @var \MLL\ConditionalMigrations\Migrator
     */
    protected $migrator;

    public function setUp(): void
    {
        parent::setUp();

        $this->db = $db = new CapsuleManager;

        $db->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);
        $db->setAsGlobal();

        $this->app->instance('db', $db->getDatabaseManager());

        $repository = new DatabaseMigrationRepository($db->getDatabaseManager(), 'migrations');
        if (! $repository->repositoryExists()) {
            $repository->createRepository();
        }

        $this->migrator = new Migrator(
            $repository,
            $db->getDatabaseManager(),
            new Filesystem,
            new Dispatcher($this->app),
            $this->app->get('config')
        );
    }

    public function test_handles_regular_migrations_normally(): void
    {
        $this->migrator->run([
            __DIR__.'/migrations/always',
        ]);

        $this->assertTrue($this->db->schema()->hasTable('unconditional_users'));
    }

    public function test_skips_migrations_that_should_not_run(): void
    {
        $this->migrator->run([
            __DIR__.'/migrations/always',
            __DIR__.'/migrations/conditional',
        ]);

        $this->assertTrue($this->db->schema()->hasTable('unconditional_users'));
        $this->assertTrue($this->db->schema()->hasTable('conditional_users_one'));
        $this->assertFalse($this->db->schema()->hasTable('conditional_users_two'));
    }

    public function test_configuration_values_take_precedence_over_individual_configuration(): void
    {
        $this->app->get('config')->set('conditional-migrations.always_run', true);

        $this->migrator->run([
            __DIR__.'/migrations/conditional',
        ]);

        $this->assertTrue($this->db->schema()->hasTable('conditional_users_one'));
        $this->assertTrue($this->db->schema()->hasTable('conditional_users_two'));
    }
}
