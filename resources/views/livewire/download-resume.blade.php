<?php

use Livewire\Attributes\Computed;
use Livewire\Volt\Component;

new class extends Component {
    #[Computed]
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
    x-data="{
        canBeDownloaded: {{ json_encode($this->resumeExists) }},
        download: function () {
            if (! this.canBeDownloaded) return;

            confirm('Are you ok with downloading my resume?') && $wire.download();
        }
    }"
    @click="download"
    :should-expand="true"
>
    {{ $this->resumeExists ? 'Download my resume' : 'Resume not available' }}
</x-site::button>
