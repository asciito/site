<x-site-errors::minimal :code="500" :title="__('Server Error')">
    <x-slot:message>
        {{ __('Server Error') }}
    </x-slot:message>
</x-site-errors::minimal>
