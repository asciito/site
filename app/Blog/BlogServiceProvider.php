<?php

declare(strict_types=1);

namespace App\Blog;

use Illuminate\Support\ServiceProvider;

class BlogServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->loadViewsFrom(resource_path('views/blog'), 'blog');
    }
}
