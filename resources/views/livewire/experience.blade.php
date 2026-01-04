<?php

use App\Models\JobExperience;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Pluralizer;
use Illuminate\View\ComponentAttributeBag;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use function Filament\Support\generate_icon_html;

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
        x-init="
            let detailsName = null;
            const detailsList = document.querySelectorAll('[name=job-experience]');

            const toggle = (details) => {
                /**
                 * The `name` attribute behaves like the `radio` input so only one can be open at a time, and we want
                 * to have all open when printing, so we temporarily remove it before toggling the `open` attribute.
                 */
                if (detailsName) {
                    details.setAttribute('name', detailsName);
                } else {
                    details.removeAttribute('name');
                }

                details.toggleAttribute('open');
            };

            window.addEventListener('beforeprint', () => {
                detailsList.forEach(toggle);

                detailsName = detailsList[0].getAttribute('name');
            });

            window.addEventListener('afterprint', () => {
                detailsList.forEach(toggle);

                detailsName = null;
            });
        "
    >
        @php($alreadyWorking = false)

        @forelse($this->experience as $job)
            <div wire:key="{{ $job->id }}" class="grid grid-cols-[3rem_1fr] gap-3 group"> <!-- Main container -->
                <div class="flex flex-col items-center"> <!-- Connector -->
                    <div> <!-- Icon -->
                        <div class="bg-harlequin size-12 grid place-content-center">
                            {{ generate_icon_html(Heroicon::Briefcase, attributes: new ComponentAttributeBag(['class' => 'text-zinc-900 size-6'])) }}
                        </div>
                    </div>

                    <div class="w-full h-full grid grid-cols-[1fr_auto_1fr] group-last:hidden"> <!-- Divider -->
                        <div></div>
                        <div class="w-1 h-[calc(100%+theme('gap.12'))] bg-harlequin"></div>
                        <div></div>
                    </div>
                </div>

                <details name="job-experience" class="group"> <!-- Content -->
                    <summary
                        class="flex items-start justify-between gap-4 cursor-pointer select-none outline-none focus-visible:ring-2 focus-visible:ring-offset-4 focus-visible:ring-blue-600">
                        <span class="grid gap-1">
                            <span class="text-2xl font-semibold leading-6">{{ $job->title }}</span>

                            <span class="text-base!">
                                <span>At</span>

                                @if ($job->company_website)
                                    <a href="{{ $job->company_website }}"
                                       class="text-dark-blue-100 hover:text-dark-blue-400 active:text-dark-blue-400 hover:underline"
                                       target="_blank" rel="noopener noreferrer">
                                        {{ $job->company }}
                                    </a>
                                @else
                                    <span>{{ $job->company }}</span>
                                @endif
                            </span>

                            <span class="text-sm text-zinc-500 leading-snug m-0">
                                <x-site::date-range
                                    :from="$job->start_date"
                                    :to="! $job->working_here ? $job->end_date : null"
                                    :empty-state-to="$job->working_here ? new HtmlString('<strong>Working Here®'. ($alreadyWorking  ? ' too' : '') .'</strong>') : null"
                                    :relative="! $job->working_here && $job->date_range_as_relative"
                                />
                            </span>
                        </span>

                        <span class="relative mt-1 shrink-0 size-6" aria-controls="job-description-{{ $job->id }}">
                            <span
                                class="absolute inline-block w-6 h-[.1875rem] bg-zinc-900 origin-center top-1/2 group-hover:bg-dark-blue-200"></span>
                            <span
                                class="absolute inline-block w-6 h-[.1875rem] bg-zinc-900 origin-center top-1/2 group-hover:bg-dark-blue-200 group-open:bg-dark-blue-200 transition-transform ease-in duration-100 group-not-open:rotate-90 group-open:rotate-180"></span>
                        </span>
                    </summary>

                    <div id="job-description-{{ $job->id }}" class="mt-4 space-y-6">
                        <div class="content">
                            {{ RichContentRenderer::make($job->description) }}
                        </div>

                        @if ($job->categories->isNotEmpty())
                            <div class="space-y-2">
                                <h3 class="font-semibold">Technologies</h3>

                                <ul class="flex flex-wrap gap-2">
                                    @foreach($job->categories as $tech)
                                        <li class="text-xs uppercase shrink-0 bg-dark-blue text-white px-2 py-1">{{ $tech->name }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </details>
            </div>

            @php($alreadyWorking = $alreadyWorking || $job->working_here)
        @empty
            <div class="text-center">
                <h2 class="text-4xl">No work experience... added yet</h2>

                <p class="mt-4">Please wait while I add all my past experience</p>
            </div>
        @endforelse
    </div>
</div>
