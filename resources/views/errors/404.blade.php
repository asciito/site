<x-site::error-layout :code="404" :title="__('Not Found')">
    <x-slot:message class="space-y-4">
        <p>{{ __('¡Oops! Page not found') }}</p>

        <p class="text-[0.8em] text-slate-600">{{ __('The page you requested might not exist or was moved. Please check the URL') }}</p>
    </x-slot:message>
</x-site::error-layout>
