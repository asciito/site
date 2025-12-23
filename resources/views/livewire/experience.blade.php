<?php

use App\Models\JobExperience;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    /**
     * @return Collection<JobExperience>
     **/
    #[Computed]
    public function experience(): Collection
    {
        return JobExperience::orderBy('order')->get();
    }
}; ?>

<div class="grid gap-10">
    <div
        class="grid grid-cols-1 gap-8"
        x-data="{
            currentlyOpen: null,
            showMe(id) {
                this.currentlyOpen = id === this.currentlyOpen ? null : id;
            }
        }"
    >
        @foreach($this->experience as $job)
            <div wire:key="{{ $job->id }}" class="grid grid-cols-[5rem_1fr] group"> <!-- Main container -->
                <div class="flex flex-col items-center"> <!-- Connector -->
                    <div> <!-- Icon -->
                        <div class="bg-harlequin rounded-full size-12 grid place-content-center">
                            <x-filament::icon :icon="Heroicon::User" class="text-zinc-900 "/>
                        </div>
                    </div>

                    <div class="w-full h-full grid grid-cols-[1fr_auto_1fr] group-last:hidden"> <!-- Divider -->
                        <div></div>
                        <div class="w-1 h-[calc(100%+theme('gap.8'))] bg-harlequin"></div>
                        <div></div>
                    </div>
                </div>

                <section x-data="{ amIOpened: () => currentlyOpen == {{ $job->id }} }"> <!-- Content -->
                    <div class="flex justify-between">
                        <div class="grid mt-2">
                            <h2 class="text-xl bold">{{ $job->title }}</h2>

                            <p class="shrink-0 text-xs mt-[.225rem] text-dark-blue/50 self-start m-0">
                                <time>Jan, 2018</time>
                                -
                                <time>{{ now()->format('M, Y') }}</time>
                            </p>
                        </div>

                        <div class="grid items-center">
                            <x-site::button
                                class="px-2! py-1!"
                                x-bind:class="{ 'bg-harlequin-800/10!': amIOpened() }"
                                :size="Size::ExtraSmall"
                                @click="showMe({{ $job->id }})"
                            >
                                <template x-if="! amIOpened()">
                                    <x-filament::icon class="text-zinc-900" :icon="Heroicon::ChevronRight"/>
                                </template>

                                <template x-if="amIOpened()">
                                    <x-filament::icon class="text-zinc-900" :icon="Heroicon::ChevronDown"/>
                                </template>
                            </x-site::button>
                        </div>
                    </div>

                    <div class="mt-4 space-y-6" x-cloak x-show="amIOpened()">
                        <div class="content">
                            {{ RichContentRenderer::make($job->description) }}
                        </div>

                        <div class="space-y-2">
                            <h3 class="font-semibold">Technologies</h3>

                            <ul class="flex flex-wrap gap-2">
                                @foreach($job->technologies as $tech)
                                    <li class="text-xs uppercase shink-0 bg-dark-blue text-white px-2 py-1">{{ $tech }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </section>
            </div>
        @endforeach
    </div>
</div>
