<x-site::error-layout :code="429" :title="__('Too Many Requests')">
    <x-slot:message>
        {{ __('Too Many Requests') }}
    </x-slot:message>
</x-site::error-layout>
