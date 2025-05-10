<x-site::error-layout :code="503" :title="__('Service Unavailable')">
    <x-slot:message>
        {{ __('Service Unavailable') }}
    </x-slot:message>
</x-site::error-layout>
