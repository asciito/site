<?php

declare(strict_types=1);

namespace App\Settings;

use App\Settings\Repositories\EloquentRepository;

abstract class Settings
{
    protected array $oldSettings = [];

    protected array $initialSettings = [];

    protected array $cachedPublicPropertyNames = [];

    public function __construct(protected EloquentRepository $repository)
    {
        $this->setupGroup();

        $this->fill($this->repository->getAll());
    }

    public function fill(array $data): static
    {
        $properties = $this->getCachedPropertyNames();

        foreach ($properties as $name => $type) {
            if (array_key_exists($name, $data)) {
                $this->$name = filled($data[$name]) ? $this->castValue($data[$name], $type) : null;

                $this->initialSettings[$name] = $this->$name;
            }
        }

        return $this;
    }



    public function getUpdated(): array
    {
        $properties = $this->getCachedPropertyNames();
        $updatedSettings = [];

        foreach ($properties as $name => $type) {
            if (array_key_exists($name, $this->initialSettings)) {
                if ($this->$name !== $this->initialSettings[$name]) {
                    $updatedSettings[$name] = $this->$name;
                }
            }
        }

        return $updatedSettings;
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

    /**
     * Get the public property names and their types.
     *
     * @return array<string, null|\ReflectionIntersectionType|\ReflectionNamedType|\ReflectionUnionType>
     */
    protected function getCachedPropertyNames(): array
    {
        if (empty($this->cachedPublicPropertyNames)) {
            $properties = (new \ReflectionClass($this))->getProperties(\ReflectionProperty::IS_PUBLIC);

            $this->cachedPublicPropertyNames = collect($properties)
                ->mapWithKeys(fn (\ReflectionProperty $property) => [$property->name => $property->getType()])
                ->all();
        }

        return $this->cachedPublicPropertyNames;
    }

    protected function castValue(string $value,  null|\ReflectionIntersectionType|\ReflectionNamedType|\ReflectionUnionType $type): mixed
    {
        if ($type === null) {
            return $value;
        }

        if ($type instanceof \ReflectionIntersectionType) {
            throw new \InvalidArgumentException('Intersection types are not supported.');
        }

        if ($type instanceof \ReflectionUnionType) {
            $types = $type->getTypes();

            if (count($types) > 1) {
                throw new \InvalidArgumentException('Union types with more than one type are not supported.');
            }

            $type = $types[0];
        }

        if ($type->allowsNull() && ($value === 'null' || $value === '')) {
            return null;
        }

        return match ($type->getName()) {
            'array' => json_decode($value, true),
            'int' => (int) $value,
            'float' => (float) $value,
            'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            'string' => $value,
            default => throw new \InvalidArgumentException("Unsupported type casting: {$type->getName()}"),
        };
    }

    public function save(): void
    {
        $updatedSettings = $this->getUpdated()();

        if (filled($updatedSettings)) {
            $this->repository->setMany($updatedSettings);

            foreach ($updatedSettings as $name => $value) {
                // save the old settings
                $this->oldSettings[$name] = $this->initialSettings[$name];

                // update the initial settings
                $this->initialSettings[$name] = $value;
            }
        }
    }

    public function all(): array
    {
        return collect($this->initialSettings)
            ->merge($this->getUpdated()())
            ->mapWithKeys(fn (mixed $payload, string $setting) => [$setting => $payload])
            ->all();
    }

    public function toArray(): array
    {
        return $this->all();
    }
}
