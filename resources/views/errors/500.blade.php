<x-site::error-layout :code="500" :title="__('Server Error')">
    <x-slot:message>
        {{ __('Server Error') }}
    </x-slot:message>
</x-site::error-layout>
