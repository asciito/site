<x-site::error-layout :code="503" :title="__('Service Unavailable')">
    <x-slot:message>
        @if(! app()->maintenanceMode())
            <p>{{ __('Service Unavailable') }}<p>
        @else
            <p>{{ __('The application is in maintenance mode.') }}</p>
            <p>{{ __('Please check back later.') }}</p>
        @endif
    </x-slot:message>
</x-site::error-layout>
