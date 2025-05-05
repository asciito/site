<?php

declare(strict_types=1);

namespace App\Settings\Repositories;

use App\Models\Setting;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class SettingRepository
{
    protected string $group;

    /**
     * @var class-string The Setting model class name
     */
    protected string $model = \App\Models\Setting::class;

    public function get(string $name, mixed $default = null): mixed
    {
        try {
            return $this->query()->where('name', $name)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return $default;
        }
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

        $cases = [];
        $bindings = [];
        $whereBindings = [];

        foreach ($settings as $name => $payload) {
            $cases[] = 'WHEN ? THEN ?';
            $bindings[] = $name;
            $bindings[] = $payload ? json_encode($payload) : null;
            $whereBindings[] = $name;
        }

        $cases = implode("\n", $cases);
        $table = (new $this->model)->getTable();
        $inClause = implode(',', array_fill(0, count($whereBindings), '?'));

        $sql = <<<SQL
        UPDATE $table
        SET payload = CASE `name`
            $cases
            ELSE payload
        END
        WHERE `name` IN ($inClause)
        SQL;

        DB::update($sql, array_merge($bindings, $whereBindings));
    }

    public function delete(string $name): void
    {
        $this->query()->where('name', $name)->delete();
    }

    public function deleteMany(array $names): void
    {
        foreach ($names as $name) {
            $this->delete($name);
        }
    }

    public function getAll(): array
    {
        return $this->query()->get()->mapWithKeys(function (Setting $item) {
            return [$item->name => $item->payload];
        })->all();
    }

    public function query(): \Illuminate\Database\Eloquent\Builder
    {
        return $this->model::query()->byGroup($this->getGroup());
    }

    public function getGroup(): string
    {
        return $this->group;
    }

    public function setGroup(string $group): void
    {
        $this->group = $group;
    }
}
