<?php

declare(strict_types=1);

namespace App\Settings\Repositories;

use App\Settings\Models\Setting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EloquentRepository implements Contracts\Repository
{
    public function __construct(protected string $model, protected ?string $group = null)
    {
        //
    }

    public function get(string $name, mixed $default = null): mixed
    {
        try {
            return $this->withGroup()->where('name', $name)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return $default;
        }
    }

    public function getAll(): array
    {
        return $this->withGroup()->get()->mapWithKeys(function (Setting $item) {
            return [$item->name => $item->payload];
        })->all();
    }

    public function set(string $name, mixed $value): void
    {
        $this->query()->updateOrCreate(['name' => $name, 'group' => $this->getGroup()], ['payload' => $value])->save();
    }

    public function setMany(array $settings): void
    {
        if (empty($settings)) {
            return;
        }

        $data = [];

        foreach ($settings as $name => $payload) {
            $data[] = [
                'name' => $name,
                'group' => $this->getGroup(),
                'payload' => $payload ? json_encode($payload) : null,
            ];
        }

        $this->query()->upsert($data, ['group', 'name'], ['payload']);
    }

    public function delete(string $name): void
    {
        $this->withGroup()->where('name', $name)->delete();
    }

    public function deleteMany(array $names): int
    {
        if (empty($names)) {
            return 0;
        }

        return (int) $this->withGroup()->whereIn('name', $names)->delete();
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function setGroup(string $group): void
    {
        $this->group = $group;
    }

    public function deleteAll(): void
    {
        $this->withGroup()->delete();
    }

    public function renameGroup($newGroup): void
    {
        $this->withGroup()->update(['group' => $newGroup]);
    }

    protected function query(): Builder
    {
        return $this->model::query();
    }

    protected function withGroup(): Builder
    {
        return $this->query()->byGroup($this->getGroup());
    }
}
