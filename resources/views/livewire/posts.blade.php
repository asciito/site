<?php

use Illuminate\Database\Eloquent\Builder;
use Livewire\Volt\Component;

new class extends Component {
    use \Livewire\WithPagination;

    const INCREMENT_PAGE_BY = 5;

    #[\Livewire\Attributes\Url]
    public string $search = '';

    public int $perPage = 5;

    public function updatingSearch(string $search): void
    {
        $this->search = htmlspecialchars($search);
    }

    public function posts(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return \App\Models\Post::when($this->search, function (Builder $query) {
            $query->where('title', 'LIKE', "%{$this->search}%");
        })->paginate(perPage: $this->perPage);
    }

    public function loadMorePosts(): void
    {
        $this->perPage += static::INCREMENT_PAGE_BY;
    }
}; ?>

<section>
    <div class="w-full mb-5">
        <input
            id="search"
            type="search"
            name="search"
            placeholder="Search by name"
            wire:model.live.debounce.500ms="search"
            class="px-2 py-1 w-full text-lg p-0 border-none bg-[rgba(51,255,51,1.0)] placeholder:text-slate-800 outline-none focus:ring focus:ring-slate-500">
    </div>

    <div class="flex flex-wrap space-y-5">
        @forelse($this->posts() as $post)
            @php
                $title = $post->title;
                $time = empty($post->deleted_at) ? $post->created_at : $post->updated_at;
                $url = route('post', $post);
            @endphp
            <article class="space-y-2">
                <header>
                    <h3 class="text-2xl">{{ $post->title }}</h3>

                    <p class="text-sm text-slate-500">Posted:
                        <time
                            datetime="{{ $time->format('y-m-d') }}">{{ $time->diffForHumans() }}</time>
                    </p>
                </header>

                <div class="space-y-2">
                    <p>
                        Lorem ipsum dolor sit amet, consectetur
                        adipisicing elit. A
                        adipisci, ipsum labore laudantium neque nobis
                        rem repudiandae
                        sunt tenetur voluptate!
                    </p>

                    <a href="{{ $url }}"
                       class="inline-block px-3 py-1 bg-[rgba(51,255,51,1.0)] text-sm cursor-pointer">
                        VISIT
                    </a>
                </div>
            </article>
        @empty
            <p class="w-full bg-slate-200/70 px-6 py-4 text-slate-800 text-center">There' no more post available</p>
        @endforelse
    </div>

    @if (! $this->posts()->onLastPage())
        <div class="flex justify-center">
            <button
                wire:click="loadMorePosts()"
                wire:loading.attr="disabled"
                wire:loading.class="disabled:opacity-80"
                class="mt-10 px-3 py-1 bg-[rgba(51,255,51,1.0)] text-xl cursor-pointer flex items-center space-x-2">
                <span>LOAD MORE..</span>
                <span class="loader hidden h-5 w-5 border-2 border-slate-50 border-b-transparent rounded-full animate-spin" wire:loading.class.remove="hidden"></span>
            </button>
        </div>
    @endif
</section>
