<?php

declare(strict_types=1);

namespace App\Settings\Repositories\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface Repository
{
    public function get(string $name, mixed $default = null): mixed;

    public function set(string $name, mixed $value): void;

    public function setMany(array $settings): void;

    public function delete(string $name): void;

    public function deleteMany(array $names): int;

    public function getAll(): array;

    public function query(): Builder;

    public function getGroup(): string;

    public function setGroup(string $group): void;

    public function withGroup(): Builder;
}
