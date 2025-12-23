<?php

use App\Models\JobExperience;
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

    protected const int INCREMENT_BY = 2;
    public int $perPage = 5;

    #[Computed]
    public function experience(): LengthAwarePaginator
    {
        return JobExperience::paginate($this->perPage);
    }

    public function loadMore(): void
    {
        $this->perPage += static::INCREMENT_BY;

        $this->dispatch('more-loaded');
    }
}; ?>

<div class="grid gap-8">
    <div
        class="grid grid-cols-1 gap-8 content"
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
                            <h3 class="m-0">{{ $job->title }}</h3>

                            <p class="shrink-0 text-sm mt-[.225rem] text-zinc-500 self-start m-0">
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

                    <div x-show="amIOpened()">
                        <p>
                            {{ $job->description }}
                        </p>
                    </div>
                </section>
            </div>
        @endforeach
    </div>

    @if ($this->experience->hasMorePages())
        <x-site::button position="center" :show-loading-indicator="true" wire:click="loadMore">Load More
        </x-site::button>
    @endif
</div>

@script
<script>
    // Still testing if it's look good, or not... maybe not...
    // Livewire.on('more-loaded', function () {
    //     window.scrollBy({
    //         top: window.innerHeight,
    //         behaviour: "smooth",
    //     });
    // })
</script>
@endscript
