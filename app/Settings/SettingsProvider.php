<?php

declare(strict_types=1);

namespace App\Settings;

use App\Settings\Database\Schema\Builder;
use App\Settings\Repositories\EloquentRepository;
use Illuminate\Support\ServiceProvider;

class SettingsProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind('settings.repository', function () {
            return new EloquentRepository(model: config('settings.model'));
        });

        $this->app->bind('settings.schema', function () {
            return new Builder;
        });

        $this->bindSettingClasses();
    }

    public function boot(): void
    {
        //
    }

    public function bindSettingClasses(): void
    {
        $classes = config('settings.classes');

        foreach ($classes as $class) {
            $this->app->scoped($class, function ($app) use ($class) {
                $repository = $app->make('settings.repository');

                return new $class($repository);
            });
        }
    }
}
