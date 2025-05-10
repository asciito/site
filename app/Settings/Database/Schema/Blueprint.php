<?php

declare(strict_types=1);

namespace App\Settings\Database\Schema;

use App\Settings\Repositories\SettingRepository;

class Blueprint
{
    protected array $settingsToAdd = [];

    protected array $settingsToDelete = [];

    public function __construct(protected SettingRepository $repository)
    {
        //
    }

    public function add(string $name, mixed $value = null): static
    {
        $this->settingsToAdd[$name] = $value;

        return $this;
    }

    public function remove(string $name): static
    {
        $this->settingsToDelete[] = $name;

        return $this;
    }

    protected function addSettings(array $settings): void
    {
        $this->repository->setMany($settings);
    }

    protected function deleteSettings(array $settings): void
    {
        $this->repository->deleteMany($settings);
    }

    public function __destruct()
    {
        $this->deleteSettings($this->settingsToDelete);

        $this->addSettings($this->settingsToAdd);
    }
}
