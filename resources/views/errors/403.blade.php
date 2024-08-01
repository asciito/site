<x-site-errors::minimal :code="403" :title="__('Forbidden')">
    <x-slot:message>
        {{ __($exception->getMessage() ?: 'Access forbidden') }}
    </x-slot:message>
</x-site-errors::minimal>
