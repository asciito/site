<?php

use App\Blog\Models\Post;
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

    #[\Livewire\Attributes\Computed]
    public function posts(): \Illuminate\Pagination\LengthAwarePaginator
    {
        return \App\Blog\Models\Post::when($this->search, function (Builder $query) {
            $query->where('title', 'LIKE', "%{$this->search}%");
        })->orderBy('published_at', 'DESC')->paginate(perPage: $this->perPage);
    }

    public function loadMorePosts(): void
    {
        $this->perPage += static::INCREMENT_PAGE_BY;
    }
}; ?>

<section>
    <div class="w-full mb-5">
        <x-site::input
            id="search"
            type="search"
            name="search"
            placeholder="Search by name"
            wire:model.live.debounce.500ms="search"/>
    </div>

    <div class="grid gap-8">
        @forelse($this->posts as $post)
            @php
                /** @var Post $post */

                $title = $post->title;
                $url = route('post', $post);
            @endphp
            <article class="grid gap-3">
                <header class="space-y-1">
                    <h3 @class(["text-2xl", "flex items-center space-x-2" => $post->isDraft()])>
                        <span>{{ $post->title }}</span>

                        @if($post->isDraft())
                            <span class="text-xs px-2 py-1 bg-slate-200 text-slate-800">{{ $post->status->name }}</span>
                        @endif
                    </h3>

                    <p class="text-sm text-slate-500">
                        {{ $post->getDate() }}
                    </p>
                </header>

                <div class="space-y-2">
                    <p>
                        {{ $post->getExcerpt() }}
                    </p>

                    <x-site::button href="{{ $url }}" size="sm">
                        VISIT
                    </x-site::button>
                </div>
            </article>
        @empty
            <p class="w-full bg-slate-200/70 px-6 py-4 text-slate-800 text-center">There' no post available</p>
        @endforelse
    </div>

    @if (! $this->posts()->onLastPage())
        <x-site::button
            class="mt-5"
            position="center"
            show-loading-indicator
            wire:click.throttle.500ms="loadMorePosts()"
        >
            LOAD MORE
        </x-site::button>
    @endif
</section>
