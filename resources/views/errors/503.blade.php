<x-site-errors::minimal :code="503" :title="__('Service Unavailable')">
    <x-slot:message>
        {{ __('Service Unavailable') }}
    </x-slot:message>
</x-site-errors::minimal>
