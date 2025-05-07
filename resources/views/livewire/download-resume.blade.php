<?php

use Livewire\Volt\Component;

new class extends Component {
    public function resumeExists(): bool
    {
        return \Illuminate\Support\Facades\Storage::exists('resume.pdf');
    }

    public function download(): ?\Symfony\Component\HttpFoundation\StreamedResponse
    {
        if (!$this->resumeExists()) {
            return null;
        }

        return \Illuminate\Support\Facades\Storage::download('resume.pdf');
    }
}; ?>

<x-site::button
    wire:click="download"
    wire:confirm="Are you ok with downloading my resume?"
    should-expand>
        {{ $this->resumeExists() ? 'Download my resume' : 'Resume not available' }}
</x-site::button>
