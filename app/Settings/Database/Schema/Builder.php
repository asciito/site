<?php

declare(strict_types=1);

namespace App\Settings\Database\Schema;

use App\Settings\Repositories\EloquentRepository;

class Builder
{
    const string DEFAULT_GROUP = 'default';

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
        $repo = tap($this->repo, fn ($repo) => $repo->setGroup(static::DEFAULT_GROUP));
        $blueprint = new Blueprint($repo);

        $callback($blueprint);
    }

    public function dropSettingsIn(string $group): void
    {
        $repo = tap($this->repo, fn ($repo) => $repo->setGroup($group));

        $repo->deleteAll();
    }

    public function renameGroup(string $oldGroup, string $newGroup): void
    {
        $repo = tap($this->repo, fn ($repo) => $repo->setGroup($oldGroup));

        $repo->renameGroup($newGroup);
    }
}
