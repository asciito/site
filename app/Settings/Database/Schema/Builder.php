<?php

declare(strict_types=1);

namespace App\Settings\Database\Schema;

use App\Settings\Repositories\EloquentRepository;

class Builder
{
    protected EloquentRepository $repo;

    public function __construct()
    {
        $this->repo = app('settings.repository');
    }

    public function in(string $group, \Closure $callback): void
    {
        $repo = tap($this->repo, fn ($repo) => $repo->setGroup($group));
        $blueprint = new Blueprint($repo);

        $callback($blueprint);
    }

    public function default(\Closure $callback): void
    {
        $repo = tap($this->repo, fn ($repo) => $repo->setGroup('default'));
        $blueprint = new Blueprint($repo);

        $callback($blueprint);
    }

    public function dropSettingsIn(string $group): void
    {
        $repo = tap($this->repo, fn ($repo) => $repo->setGroup($group));

        $repo->query()->delete();
    }
}
