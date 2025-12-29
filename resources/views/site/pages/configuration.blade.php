@php use Filament\Support\Enums\Alignment; @endphp

<x-filament-panels::page>
    {{ $this->form }}

    <x-filament::section
        aside
        compact
        :contained="false"
    >
        <x-slot:heading>
            Categories
        </x-slot:heading>

        <x-slot:description>
            Manage the categories available for your site, you can add, edit or delete categories as needed.
        </x-slot:description>

        {{ $this->table }}
    </x-filament::section>

    <x-filament::actions
        :actions="$this->getCachedFormActions()"
        :alignment="Alignment::End"
    />
</x-filament-panels::page>
