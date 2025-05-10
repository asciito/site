<x-site::error-layout :code="403" :title="__('Forbidden')">
    <x-slot:message>
        {{ __($exception->getMessage() ?: 'Access forbidden') }}
    </x-slot:message>
</x-site::error-layout>
