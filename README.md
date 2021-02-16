# Deprecated:
## We don't maintain this version anymore, checkout [Laravel conditional migrations](https://github.com/mll-lab/laravel-conditional-migrations) for the latest version

---

## Laravel Conditional Migrations

[![CI Status](https://github.com/mll-lab/laravel-conditional-migrations/workflows/Continuous%20Integration/badge.svg)](https://github.com/mll-lab/laravel-conditional-migrations/actions)
[![codecov](https://codecov.io/gh/mll-lab/laravel-conditional-migrations/branch/master/graph/badge.svg)](https://codecov.io/gh/mll-lab/laravel-conditional-migrations)
[![StyleCI](https://github.styleci.io/repos/215751793/shield?branch=master)](https://github.styleci.io/repos/215751793)

[![Packagist](https://img.shields.io/packagist/dt/mll-lab/laravel-conditional-migrations.svg)](https://packagist.org/packages/mll-lab/laravel-conditional-migrations)
[![Latest Stable Version](https://poser.pugx.org/mll-lab/laravel-conditional-migrations/v/stable)](https://packagist.org/packages/mll-lab/laravel-conditional-migrations)
[![GitHub license](https://img.shields.io/github/license/mll-lab/laravel-conditional-migrations.svg)](https://github.com/mll-lab/laravel-conditional-migrations/blob/master/LICENSE)

Run migrations only if a condition is true

Based on https://github.com/onlinepets/laravel-conditional-migrations

## Installation

Via [composer](http://getcomposer.org):

    composer require mll-lab/laravel-conditional-migrations

## Usage

To run a migration conditionally, implement the `ConditionalMigration`
interface and its `->shouldRun()` method:

```php
use MLL\ConditionalMigrations\Contracts\ConditionalMigration;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;

class DoSomethingVeryIntensive extends Migration implements ConditionalMigration
{
    public function up() { ... }
    public function down() { ... }

    public function shouldRun(): bool
    {
        return (new Carbon('1 AM'))->lessThan(now())
            && (new Carbon('2 AM'))->greaterThan(now());
    }
}
```

The code snippet above will make sure the `do_something_very_intensive` migration
will be skipped unless it is executed between 1 AM and 2 AM. This can be useful
if your migration does something that should not be run during the daytime, like
adding an index to a table containing lots of data.

## Configuration

You can optionally publish the configuration file:

    php artisan vendor:publish --tags=conditional-migrations-config

This will create the file `config/conditional-migrations.php`.

The `always_run` option allows you to overrule the conditions set in individual migrations.

```php
'always_run' => env('APP_DEBUG', false),
``` 

You can also use a closure if you want to do more advanced calculations:

```php
'always_run' => function (): bool {
    // calculate if migrations should always run
},
```

## Changelog

All notable changes to this project are documented in [`CHANGELOG.md`](CHANGELOG.md).

## Contributing

Contributions are welcome, see [CONTRIBUTING.md](CONTRIBUTING.md).

## License

See [LICENSE.md](LICENSE.md).
