@php use Filament\Support\Enums\Alignment; @endphp

<x-filament-panels::page>
    {{ $this->form }}

    <x-filament::actions
        :actions="$this->getFooterActions()"
        :alignment="Alignment::End"
    />
</x-filament-panels::page>
