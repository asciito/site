<?php

declare(strict_types=1);

namespace App\Settings;

use App\Settings\Repositories\SettingRepository;

abstract class Settings
{
    protected static array $newSettings = [];

    protected static array $oldSettings = [];

    protected static array $initialSettings = [];

    public function __construct(protected SettingRepository $repository)
    {
        $this->setupGroup();

        $this->fill($this->repository->getAll());
    }

    public function get(string $name, mixed $default = null): mixed
    {
        if (array_key_exists($name, static::$newSettings)) {
            return static::$newSettings[$name];
        }

        return static::$initialSettings[$name];
    }

    public function set(string $name, mixed $value): static
    {
        if (! array_key_exists($name, static::$initialSettings)) {
            return $this;
        }

        static::$oldSettings[$name] = static::$newSettings[$name] ?? static::$initialSettings[$name];

        if ($value === static::$oldSettings[$name]) {
            unset(static::$oldSettings[$name]);

            return $this;
        }

        static::$newSettings[$name] = $value;

        return $this;
    }

    public function fill(array $data): static
    {
        $properties = (new \ReflectionClass($this))->getProperties(\ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            $name = $property->getName();

            if (array_key_exists($name, $data)) {
                $this->$name = $data[$name] ?? null;

                static::$initialSettings[$name] = $data[$name];
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
        return collect(static::$initialSettings)
            ->merge(static::$newSettings)
            ->mapWithKeys(fn (mixed $payload, string $setting) => [$setting => $payload])
            ->all();
    }

    public function newSettings(): array
    {
        return static::$newSettings;
    }

    public function oldSettings(): array
    {
        return static::$oldSettings;
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
        if (array_key_exists($name, static::$newSettings)) {
            return static::$newSettings[$name];
        }

        if (array_key_exists($name, static::$initialSettings)) {
            return static::$initialSettings[$name];
        }

        throw new \BadMethodCallException('Undefined property: ' . static::class . '::$' . $name);
    }
}
