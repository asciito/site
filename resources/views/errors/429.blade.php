<x-site-errors::minimal :code="429" :title="__('Too Many Requests')">
    <x-slot:message>
        {{ __('Too Many Requests') }}
    </x-slot:message>
</x-site-errors::minimal>
