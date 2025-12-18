@php use Filament\Support\Enums\Alignment; @endphp

<x-filament-panels::page>
    {{ $this->form }}

    <x-filament::actions
        :actions="$this->getCachedFormActions()"
        :alignment="Alignment::End"
    />

    <x-filament-panels::unsaved-action-changes-alert />
</x-filament-panels::page>
