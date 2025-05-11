<?php

declare(strict_types=1);

namespace App\Settings;

use App\Settings\Repositories\EloquentRepository;

abstract class Settings
{
    protected array $newSettings = [];

    protected array $oldSettings = [];

    protected array $initialSettings = [];

    public function __construct(protected EloquentRepository $repository)
    {
        $this->setupGroup();

        $this->fill($this->repository->getAll());
    }

    public function get(string $name, mixed $default = null): mixed
    {
        if (array_key_exists($name, $this->newSettings)) {
            return $this->newSettings[$name];
        }

        return $this->initialSettings[$name] ?? $default;
    }

    public function set(string $name, mixed $value): static
    {
        if (! array_key_exists($name, $this->initialSettings)) {
            return $this;
        }

        $this->oldSettings[$name] = $this->newSettings[$name] ?? $this->initialSettings[$name];

        if ($value === $this->oldSettings[$name]) {
            unset($this->oldSettings[$name]);

            return $this;
        }

        $this->newSettings[$name] = $value;

        return $this;
    }

    public function fill(array $data): static
    {
        $properties = (new \ReflectionClass($this))->getProperties(\ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            $name = $property->getName();

            if (array_key_exists($name, $data)) {
                $this->$name = $data[$name] ?? null;

                $this->initialSettings[$name] = $data[$name];
            }
        }

        return $this;
    }

    public function update(array $data): static
    {
        foreach ($data as $name => $value) {
            $this->set($name, $value);
        }

        return $this;
    }

    public function save(): void
    {
        $this->repository->setMany($this->newSettings());
    }

    public function all(): array
    {
        return collect($this->initialSettings)
            ->merge($this->newSettings)
            ->mapWithKeys(fn (mixed $payload, string $setting) => [$setting => $payload])
            ->all();
    }

    public function newSettings(): array
    {
        return $this->newSettings;
    }

    public function oldSettings(): array
    {
        return $this->oldSettings;
    }

    protected function setupGroup(): void
    {
        try {
            $method = new \ReflectionMethod($this, 'group');

            if ($method->isStatic()) {
                $this->repository->setGroup($method->invoke(null));
            } else {
                throw new \RuntimeException('The group method must be static.');
            }
        } catch (\ReflectionException $e) {
            $this->repository->setGroup('default');
        }
    }

    public function __get(string $name): mixed
    {
        if (array_key_exists($name, $this->newSettings)) {
            return $this->newSettings[$name];
        }

        if (array_key_exists($name, $this->initialSettings)) {
            return $this->initialSettings[$name];
        }

        throw new \BadMethodCallException('Undefined property: '.static::class.'::$'.$name);
    }
}
