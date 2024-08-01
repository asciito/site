<x-site-errors::minimal :code="401" :title="__('Unauthorized')">
    <x-slot:message>
        {{ __('¡Oops!, You are unauthorized to visit this page') }}
    </x-slot:message>
</x-site-errors::minimal>
