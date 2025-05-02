<?php

declare(strict_types=1);

namespace App\Settings;

use App\Settings\Repositories\SettingRepository;

abstract class Settings
{
    protected static array $settings = [];

    public function __construct(protected SettingRepository $repository)
    {
        $this->setupGroup();

        $this->fill($this->repository->getAll());
    }

    public function get(string $name, mixed $default = null): mixed
    {
        if (array_key_exists($name, static::$settings)) {
            return static::$settings[$name];
        }

        return $this->repository->get($name, $default);
    }

    public function set(string $name, mixed $value): static
    {
        $this->repository->set($name, $value);

        static::$settings[$name] = $value;

        return $this;
    }

    public function fill(array $data): static
    {
        $properties = (new \ReflectionClass($this))->getProperties(\ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            $name = $property->getName();

            if (array_key_exists($name, $data)) {
                $property->setValue($this, $data[$name] ?? null);
            }
        }

        return $this;
    }

    public function all(): array
    {
        if (filled(static::$settings)) {
            return static::$settings;
        }

        $properties = (new \ReflectionClass($this))->getProperties(\ReflectionProperty::IS_PUBLIC);

        return static::$settings = collect($properties)
            ->map(fn (\ReflectionProperty $property) => $property->getValue($this))
            ->all();
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
        if (array_key_exists($name, static::$settings)) {
            return static::$settings[$name];
        }

        throw new \BadMethodCallException('Undefined property: ' . static::class . '::$' . $name);
    }
}
