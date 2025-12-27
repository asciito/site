<?php

use App\Models\JobExperience;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Pluralizer;
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
        class="grid grid-cols-1 gap-12"
        x-data="{
            currentlyOpen: null,
            showMe(id) {
                this.currentlyOpen = id === this.currentlyOpen ? null : id;
            }
        }"
    >
        @forelse($this->experience as $job)
            <div wire:key="{{ $job->id }}" class="grid grid-cols-[3rem_1fr] gap-3 group"> <!-- Main container -->
                <div class="flex flex-col items-center"> <!-- Connector -->
                    <div> <!-- Icon -->
                        <div class="bg-harlequin size-12 grid place-content-center">
                            <x-filament::icon :icon="Heroicon::Briefcase" class="text-zinc-900"/>
                        </div>
                    </div>

                    <div class="w-full h-full grid grid-cols-[1fr_auto_1fr] group-last:hidden"> <!-- Divider -->
                        <div></div>
                        <div class="w-1 h-[calc(100%+theme('gap.12'))] bg-harlequin"></div>
                        <div></div>
                    </div>
                </div>

                <section x-data="{ amIOpened: () => currentlyOpen == {{ $job->id }} }"> <!-- Content -->
                    <div class="flex justify-between gap-x-2">
                        <div class="grid cursor-pointer" @click="showMe({{ $job->id }})">
                            <h2 class="text-xl bold flex space-x-2">{{ $job->title }}</h2>

                            <p class="shrink-0 text-xs text-dark-blue/50 self-start m-0">
                                <x-site::date-range
                                    :from="$job->start_date"
                                    :to="$job->end_date"
                                    :empty-state-to="$job->working_here ? new HtmlString('<strong>Working Here®</strong>') : null"
                                    :relative="! $job->working_here && $job->date_range_as_relative"
                                />
                            </p>
                        </div>

                        <div class="grid items-start">
                            <x-site::button
                                class="px-2! py-1!"
                                x-bind:class="{ 'bg-harlequin-800/10!': amIOpened() }"
                                :size="Size::ExtraSmall"
                                @click.stop="showMe({{ $job->id }})"
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

                        @if ($technologies = $job->categories)
                            <div class="space-y-2">
                                <h3 class="font-semibold">Technologies</h3>

                                <ul class="flex flex-wrap gap-2">
                                    @foreach($technologies as $tech)
                                        <li class="text-xs uppercase shink-0 bg-dark-blue text-white px-2 py-1">{{ $tech->name }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </section>
            </div>
        @empty
            <div class="text-center">
                <h2 class="text-4xl">No work experience... added yet</h2>

                <p class="mt-4">Please wait while I add all my past experience</p>
            </div>
        @endforelse
    </div>
</div>
