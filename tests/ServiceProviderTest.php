<?php

namespace MLL\ConditionalMigrations\Tests;

use GrahamCampbell\TestBenchCore\ServiceProviderTrait;
use Illuminate\Database\Migrations\Migrator as LaravelMigrator;
use MLL\ConditionalMigrations\Migrator;

class ServiceProviderTest extends TestCase
{
    use ServiceProviderTrait;

    public function test_migrator_is_replaced_in_the_container(): void
    {
        $this->assertTrue($this->app->has('migrator'));

        $migrator = $this->app->get('migrator');

        $this->assertInstanceOf(Migrator::class, $migrator);
        $this->assertInstanceOf(LaravelMigrator::class, $migrator);
    }
}
