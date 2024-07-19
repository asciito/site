<?php

use Livewire\Volt\Component;

new class extends Component {
    public function download(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return response()->download(
            public_path('ayax_cordova_resume.pdf')
        );
    }
}; ?>

<x-site::button wire:click="download" should-expand>DOWNLOAD RESUME</x-site::button>
