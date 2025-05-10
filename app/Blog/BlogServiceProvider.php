<?php

declare(strict_types=1);

namespace App\Blog;

use Illuminate\Support\ServiceProvider;
use Livewire\Volt\Volt;

class BlogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->loadViewsFrom(resource_path('views/blog'), 'blog');
    }

    public function boot(): void
    {
        Volt::mount([
            resource_path('views/blog/livewire')
        ]);
    }
}
